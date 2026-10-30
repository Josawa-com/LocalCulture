#!/usr/bin/env bash
#
# Menjalankan CultureRaya untuk dibuka di browser (mode development).
#
#   ./dev.sh          -> hanya bisa dibuka di komputer ini (127.0.0.1)
#   ./dev.sh --lan    -> bisa dibuka dari HP / perangkat lain di Wi-Fi yang sama
#   ./dev.sh --stop   -> menghentikan semua server
#
# Untuk berbagi ke teman di Wi-Fi yang sama, LEBIH BAIK pakai:
#   ./share.sh        -> satu port saja (aset build, tanpa Vite), lebih andal
#
# Ctrl+C menghentikan keduanya.

set -euo pipefail

cd "$(dirname "$0")"

PID_FILE="storage/dev-servers.pid"
SERVE_LOG="/tmp/cultureraya-serve.log"
VITE_LOG="/tmp/cultureraya-vite.log"

stop_servers() {
    if [ -f "$PID_FILE" ]; then
        while read -r pid; do
            kill "$pid" 2>/dev/null || true
        done < "$PID_FILE"
        rm -f "$PID_FILE"
        echo "Server dihentikan."
    else
        # Cadangan: hentikan berdasarkan port.
        fuser -k 8000/tcp 5173/tcp 2>/dev/null || true
        echo "Tidak ada PID tersimpan; port 8000 & 5173 dibersihkan."
    fi
    # public/hot hanya ada saat mode dev — hapus agar tidak menunjuk server mati.
    rm -f public/hot
}

if [ "${1:-}" = "--stop" ]; then
    stop_servers
    exit 0
fi

HOST="127.0.0.1"
if [ "${1:-}" = "--lan" ]; then
    HOST="0.0.0.0"
fi

# ---------- Persiapan ----------
if [ ! -f .env ]; then
    echo "Membuat .env dari .env.example..."
    cp .env.example .env
    php artisan key:generate --no-interaction
fi

if [ ! -f database/database.sqlite ]; then
    echo "Membuat database SQLite..."
    touch database/database.sqlite
fi

# Hanya migrasi bila ada yang belum jalan (tidak menyentuh data).
php artisan migrate --force --no-interaction >/dev/null 2>&1 || true

# ---------- Bersihkan sisa server lama ----------
if [ -f "$PID_FILE" ]; then
    stop_servers
fi
fuser -k 8000/tcp 5173/tcp 2>/dev/null || true
sleep 1

# ---------- Info alamat ----------
LAN_IP="$(hostname -I 2>/dev/null | awk '{print $1}')"
[ -z "$LAN_IP" ] && LAN_IP="$(ip route get 1.1.1.1 2>/dev/null | awk '{print $7; exit}')"

echo "──────────────────────────────────────────────"
echo " CultureRaya"
echo "──────────────────────────────────────────────"
echo " Lokal     : http://localhost:8000"

if [ "$HOST" = "0.0.0.0" ] && [ -n "$LAN_IP" ]; then
    echo " Jaringan  : http://$LAN_IP:8000"
fi

echo " Admin     : http://localhost:8000/rezka"
echo "──────────────────────────────────────────────"

# ---------- Jalankan ----------
: > "$PID_FILE"

# Vite: mode LAN bila diminta.
if [ "$HOST" = "0.0.0.0" ]; then
    DEV_HOST=0.0.0.0 DEV_HMR_HOST="${LAN_IP:-localhost}" npm run dev > "$VITE_LOG" 2>&1 &
else
    npm run dev > "$VITE_LOG" 2>&1 &
fi
echo $! >> "$PID_FILE"

php artisan serve --host="$HOST" --port=8000 > "$SERVE_LOG" 2>&1 &
echo $! >> "$PID_FILE"

cleanup() {
    echo
    echo "Menghentikan server..."
    stop_servers
    exit 0
}
trap cleanup INT TERM

# Tunggu sampai benar-benar siap.
for _ in $(seq 1 40); do
    if curl -fsS -o /dev/null --max-time 2 http://127.0.0.1:8000/ 2>/dev/null; then
        break
    fi
    sleep 0.5
done

echo "Siap. Buka http://localhost:8000 di browser."
echo "Tekan Ctrl+C untuk berhenti."
echo

# Tampilkan log bergantian; kalau salah satu mati, hentikan semua.
tail -n 0 -F "$SERVE_LOG" "$VITE_LOG" &
TAIL_PID=$!
echo $TAIL_PID >> "$PID_FILE"

wait $TAIL_PID 2>/dev/null || true
cleanup
