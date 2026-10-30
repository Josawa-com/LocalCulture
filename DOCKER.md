# Deploy CultureRaya dengan Docker

Panduan singkat untuk menjalankan aplikasi di server/hosting memakai Docker.

---

## Cara tercepat

```bash
./docker.sh --seed
```

Buka **http://localhost:8000**.

| Perintah | Fungsi |
|---|---|
| `./docker.sh` | build + jalankan |
| `./docker.sh --seed` | build + jalankan + isi data contoh |
| `./docker.sh --lan` | tampilkan alamat Wi-Fi |
| `./docker.sh --logs` | lihat log langsung |
| `./docker.sh --stop` | hentikan |
| `./docker.sh --reset` | hapus kontainer **dan semua data** |

Seed membuat akun admin: `admin@localculture.test` / `password`.
**Ganti password ini setelah login pertama.**

---

## Cara manual

```bash
docker compose build
docker compose up -d
docker compose exec app php artisan db:seed --force   # opsional
```

Hentikan:

```bash
docker compose down          # data tetap aman
docker compose down -v       # data ikut terhapus
```

---

## Yang ada di dalam

| File | Isi |
|---|---|
| `Dockerfile` | 3 tahap: build aset (Node) → dependensi PHP (Composer) → runtime (PHP-FPM + nginx) |
| `docker-compose.yml` | Service `app` + MySQL opsional |
| `.dockerignore` | File yang tidak dikirim ke image |
| `docker/entrypoint.sh` | Menyiapkan APP_KEY, migrasi, cache, lalu menyalakan server |
| `docker/nginx.conf` | Konfigurasi nginx |
| `docker/fpm.conf` | Konfigurasi PHP-FPM |
| `docker/php.ini` | Batas memori/upload |
| `docker/opcache.ini` | Optimasi OPcache |
| `docker/supervisord.conf` | Menjalankan nginx + PHP-FPM dalam satu kontainer |

Arsitektur: **satu kontainer**, nginx dan PHP-FPM dijalankan bersama oleh supervisord. PHP 8.5 + nginx + OPcache.

---

## Data yang bertahan

Dua volume Docker:

| Volume | Isi |
|---|---|
| `cultureraya-database` | Database SQLite (user, kuis, percobaan, kunjungan) |
| `cultureraya-storage` | Unggahan (avatar) + `app_key` |

`APP_KEY` dibuat otomatis pada run pertama dan disimpan di volume, sehingga sesi dan data terenkripsi tidak rusak saat kontainer di-rebuild.

> **Penting:** jangan pernah `docker compose down -v` di server produksi kecuali memang ingin menghapus semua data.

---

## Memakai MySQL (opsional)

SQLite sudah cukup untuk skala kecil–menengah. Untuk beban lebih besar:

```bash
docker compose --profile mysql up -d
```

Lalu ubah bagian `environment` service `app` di `docker-compose.yml`:

```yaml
- DB_CONNECTION=mysql
- DB_HOST=mysql
- DB_PORT=3306
- DB_DATABASE=cultureraya
- DB_USERNAME=cultureraya
- DB_PASSWORD=ubah-saya
```

Sesuaikan juga `MYSQL_PASSWORD` pada service `mysql` agar sama.

---

## Deploy ke VPS

1. Pasang Docker & Docker Compose di VPS.
2. Salin proyek (atau `git clone`) ke VPS.
3. Jalankan:

```bash
./docker.sh --seed
```

4. Aplikasi berjalan di port **8000**. Untuk domain + HTTPS, letakkan reverse proxy (Caddy/Nginx/Traefik) di depannya:

```nginx
server {
    listen 80;
    server_name budaya.example.com;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

> Container hanya mendengarkan port 80 di dalam. Port host diatur lewat
> `APP_PORT` (default 8000): `APP_PORT=80 docker compose up -d`.

---

## Mengubah port

```bash
APP_PORT=8080 docker compose up -d
```

---

## Perintah berguna

```bash
docker compose exec app php artisan migrate --force    # migrasi
docker compose exec app php artisan db:seed --force     # data contoh
docker compose exec app php artisan optimize:clear      # bersihkan cache
docker compose exec app php artisan tinker               # shell Laravel
docker compose exec app sh                              # shell kontainer
docker compose logs -f app                              # log
```

---

## Masalah umum

**Halaman 500 / "View path not found"**
Folder `storage/framework/views` belum ada. Entrypoint sudah membuatnya otomatis; jalankan ulang `docker compose restart app`.

**Halaman 404 setelah deploy**
Database kosong. Jalankan `docker compose exec app php artisan db:seed --force`.

**Perubahan CSS/JS tidak terlihat**
Aset dibangun di dalam image. Rebuild:

```bash
docker compose build --no-cache && docker compose up -d
```

**Tidak bisa diakses dari perangkat lain**
Pastikan port terbuka di firewall server:

```bash
sudo ufw allow 8000/tcp
```

---

## Catatan pengembangan

Docker ini untuk **produksi** (aset sudah di-build, tanpa Vite).
Untuk pengembangan sehari-hari tetap pakai:

```bash
composer run dev     # atau ./dev.sh
```
