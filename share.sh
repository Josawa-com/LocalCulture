#!/usr/bin/env bash
#
# Membuka CultureRaya di Wi-Fi yang sama, agar bisa diakses dari HP /
# laptop lain tanpa perlu install apa pun.
#
#   ./share.sh          -> nyalakan (aset di-build, tanpa Vite)
#   ./share.sh --stop   -> hentikan
#
# Kenapa tanpa Vite?
#   Mode ini memakai aset hasil `npm run build`, jadi hanya ada SATU
#   port (8000). Lebih andal untuk perangkat lain: tidak ada hot-reload
#   yang bergantung pada port kedua, dan tidak butuh Node tetap jalan.
#
# Setelah dijalankan, buka alamat "Jaringan" yang tercetak di layar
# dari HP teman. Pastikan HP terhubung ke Wi-Fi yang sama.

set -euo pipefail

cd "$(dirname "$0")"

PID_FILE="storage/share-server.pid"
LOG_FILE="/tmp/cultureraya-share.log"
PORT=8000

stop_server() {
    if [ -f "$PID_FILE" ]; then
        while read -r pid; do
            kill "$pid" 2>/dev/null || true
        done < "$PID_FILE"
        rm -f "$PID_FILE"
    fi
    fuser -k "${PORT}/tcp" 2>/dev/null || true
    # Pastikan tidak ada sisa mode dev yang menunjuk Vite.
    rm -f public/hot
    echo "Server berbagi dihentikan."
}

if [ "${1:-}" = "--stop" ]; then
    stop_server
    exit 0
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

echo "Menjalankan migrasi yang belum jalan..."
php artisan migrate --force --no-interaction >/dev/null

# ---------- Aset ----------
# Mode berbagi memakai aset build, BUKAN dev server.
# Jalankan ulang `npm run build` setiap kali CSS/JS diubah.
rm -f public/hot

if [ ! -f public/build/manifest.json ]; then
    echo "Membangun aset (pertama kali)..."
    npm run build
fi

# ---------- Bersihkan server lama ----------
stop_server >/dev/null 2>&1 || true
sleep 1

# ---------- Alamat ----------
LAN_IP="$(hostname -I 2>/dev/null | awk '{print $1}')"
if [ -z "$LAN_IP" ]; then
    LAN_IP="$(ip route get 1.1.1.1 2>/dev/null | awk '{print $7; exit}')"
fi

echo "──────────────────────────────────────────────"
echo " CultureRaya — mode berbagi Wi-Fi"
echo "──────────────────────────────────────────────"
echo " Di komputer ini : http://localhost:${PORT}"

if [ -n "$LAN_IP" ]; then
    echo " Dari perangkat lain : http://${LAN_IP}:${PORT}"
    echo " Panel admin         : http://${LAN_IP}:${PORT}/rezka"
else
    echo " (IP jaringan tidak terdeteksi)"
fi

echo "──────────────────────────────────────────────"

# ---------- Jalankan ----------
php artisan serve --host=0.0.0.0 --port="${PORT}" > "$LOG_FILE" 2>&1 &
echo $! > "$PID_FILE"

cleanup() {
    echo
    echo "Menghentikan server..."
    stop_server
    exit 0
}
trap cleanup INT TERM

# Tunggu sampai benar-benar melayani.
for _ in $(seq 1 40); do
    if curl -fsS -o /dev/null --max-time 2 "http://127.0.0.1:${PORT}/" 2>/dev/null; then
        break
    fi
    sleep 0.5
done

# ---------- Cek akses lewat IP LAN ----------
if [ -n "$LAN_IP" ]; then
    CODE="$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 "http://${LAN_IP}:${PORT}/" 2>/dev/null || echo 000)"

    if [ "$CODE" = "200" ]; then
        echo "Siap! Perangkat lain bisa membuka http://${LAN_IP}:${PORT}"
    else
        echo "PERHATIAN: alamat LAN belum merespons (kode ${CODE})."
        echo
        echo "Kemungkinan firewall memblokir port ${PORT}. Jalankan:"
        echo "    sudo ufw allow ${PORT}/tcp"
        echo "lalu jalankan ulang ./share.sh"
    fi
fi

echo
echo "Tekan Ctrl+C untuk berhenti."
echo

tail -n 0 -F "$LOG_FILE" &
TAIL_PID=$!

wait $TAIL_PID 2>/dev/null || true
cleanup
