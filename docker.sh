#!/usr/bin/env bash
#
# Menjalankan CultureRaya dengan Docker (mode produksi).
#
#   ./docker.sh            -> build + jalankan di http://localhost:8000
#   ./docker.sh --lan      -> tampilkan juga alamat untuk Wi-Fi
#   ./docker.sh --seed     -> isi data contoh (admin, pulau, provinsi, kuis)
#   ./docker.sh --logs     -> lihat log
#   ./docker.sh --stop     -> hentikan
#   ./docker.sh --reset    -> hentikan + hapus semua data (HATI-HATI)
#
# Data tersimpan di volume Docker sehingga tetap ada walau kontainer
# dihapus atau di-rebuild.

set -euo pipefail

cd "$(dirname "$0")"

COMPOSE="docker compose"

case "${1:-}" in
    --stop)
        $COMPOSE down
        exit 0
        ;;
    --logs)
        $COMPOSE logs -f app
        exit 0
        ;;
    --reset)
        echo "Menghapus kontainer DAN semua data (database + unggahan)..."
        read -r -p "Yakin? (ketik 'ya'): " jawab
        if [ "$jawab" = "ya" ]; then
            $COMPOSE down -v
            echo "Data dihapus."
        else
            echo "Dibatalkan."
        fi
        exit 0
        ;;
esac

echo "Membangun image..."
$COMPOSE build

echo "Menjalankan kontainer..."
$COMPOSE up -d

echo "Menunggu aplikasi siap..."
for _ in $(seq 1 60); do
    if curl -fsS -o /dev/null --max-time 2 http://127.0.0.1:8000/up 2>/dev/null; then
        break
    fi
    sleep 1
done

if [ "${1:-}" = "--seed" ]; then
    echo "Mengisi data contoh..."
    $COMPOSE exec -T app php artisan db:seed --force
fi

LAN_IP="$(hostname -I 2>/dev/null | awk '{print $1}')"

echo "──────────────────────────────────────────────"
echo " CultureRaya (Docker)"
echo "──────────────────────────────────────────────"
echo " Lokal : http://localhost:8000"
if [ "${1:-}" = "--lan" ] && [ -n "$LAN_IP" ]; then
    echo " Wi-Fi : http://${LAN_IP}:8000"
fi
echo " Admin : http://localhost:8000/rezka"
echo "──────────────────────────────────────────────"
echo " Status : $($COMPOSE ps --format '{{.Status}}' | head -1)"
echo " Log    : ./docker.sh --logs"
echo " Stop   : ./docker.sh --stop"
