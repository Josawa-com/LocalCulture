# CultureRaya — Jelajah Budaya Nusantara

Aplikasi web untuk menjelajahi, mempelajari, dan melestarikan budaya Indonesia.
Berisi materi budaya per pulau → provinsi → kabupaten/kota, kuis, minigame
Mystery, papan peringkat, direktori sanggar, dan panel admin.

Dibangun dengan **Laravel 13**, **Tailwind CSS 4**, **SQLite**, dan **Docker**.

---

## Akun Admin

Hanya ada **satu** akun admin:

| | |
|---|---|
| **Email** | `anonimexe676767@gmail.com` |
| **Password** | `admin123` |
| **Panel admin** | `http://localhost:8000/rezka` |

Cara masuk:

1. Buka `/login`, masukkan email & password di atas.
2. Setelah masuk, buka `/rezka` (harus diketik langsung di address bar).

Akun ini dibuat otomatis dari `config/admin.php`. Nilainya bisa diubah lewat
`.env`:

```dotenv
ADMIN_NAME="Admin CultureRaya"
ADMIN_EMAIL=anonimexe676767@gmail.com
ADMIN_PASSWORD=admin123
```

Setelah mengubah `.env`, buat ulang akunnya:

```bash
php artisan db:seed --class=AdminUserSeeder --force
```

Di Docker, akun admin **dibuat ulang otomatis setiap kontainer menyala**
(lihat `docker/entrypoint.sh`). Matikan dengan `ADMIN_SEED=false`.

---

## Menjalankan

### Docker (paling mudah)

```bash
cp .env.example .env      # opsional, untuk mengubah port / akun admin
docker compose up -d --build
```

Buka **http://localhost:8000**.

Database SQLite disimpan di volume `cultureraya-database`, jadi data tetap ada
walau kontainer dihapus. Untuk mengisi data contoh lengkap (materi budaya,
kuis, mystery, sanggar):

```bash
docker compose exec app php artisan db:seed --force
```

### Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build
php artisan serve
```

Atau langsung:

```bash
./dev.sh          # lokal + Vite
./dev.sh --lan    # bisa dibuka dari HP di jaringan yang sama
./share.sh        # satu port, aset sudah di-build
./docker.sh       # --seed | --logs | --stop | --reset
```

---

## Halaman

| Path | Isi |
|---|---|
| `/` | Beranda |
| `/budaya` | Peta pulau & materi budaya |
| `/budaya/{pulau}` | Daftar provinsi di pulau tersebut |
| `/budaya/{pulau}/{provinsi}` | Daftar kabupaten/kota |
| `/budaya/{pulau}/{provinsi}/{kab}` | Materi budaya per wilayah |
| `/kuis` | Kuis harian, mingguan, bulanan, pengetahuan |
| `/misteri` | Minigame Mystery Budaya |
| `/sanggar` | Direktori 74 sanggar seni DIY + tautan Google Maps |
| `/pertunjukan` | Jadwal pertunjukan |
| `/leaderboard` | Papan peringkat kuis & Mystery |
| `/profil` | Profil, riwayat kuis, pengaturan |
| `/rezka` | Panel admin |

---

## Navigasi

Menu utama berada di **dock melayang di bawah layar** bergaya *Liquid Glass*:
kaca transparan dengan `backdrop-filter`, tanpa animasi, dan membagi ruang
secara merata. Header atas hanya berisi logo dan tombol akun.

Dock: `Budaya` · `Kuis` · `Mystery` · `Pertunjukan` · `Sanggar` · `Peringkat`.

---

## Data Budaya

Materi budaya DIY lengkap — **173 materi**:

| Wilayah | Materi |
|---|---|
| Kulon Progo | 39 |
| Kota Yogyakarta | 37 |
| Gunungkidul | 36 |
| Bantul | 34 |
| Sleman | 27 |

Seeder terpisah per wilayah, semuanya idempoten:

```bash
php artisan db:seed --class=KulonProgoCultureSeeder --force
php artisan db:seed --class=BantulCultureSeeder --force
php artisan db:seed --class=SlemanCultureSeeder --force
php artisan db:seed --class=GunungKidulCultureSeeder --force
php artisan db:seed --class=KotaJogjaCultureSeeder --force
```

### Sanggar

74 sanggar & tempat latihan seni di lima kabupaten/kota DIY
(`VenueSanggarSeeder`). Setiap kartu membuka Google Maps dengan pencarian
nama sanggar + wilayah + "Daerah Istimewa Yogyakarta".

Dua lokasi sudah punya koordinat terverifikasi: **Sanggar Seni Kinanti Sekar**
dan **Siswa Among Beksa**.

---

## Mengatur Tata Letak Peta Pulau

Semua posisi gambar peta diatur di satu file: **`config/island_maps.php`**.

```php
[
    'file'   => 'PulauSumatra10.png', // file di public/Pulau/
    'label'  => 'Sumatera',
    'island' => 'sumatera',           // slug tujuan saat diklik
    'x' => 0, 'y' => 40,              // titik tengah, persen kotak
    'w' => 28,                        // lebar, persen kotak
    'z' => 3,                         // urutan tumpuk
    'r' => -5,                        // rotasi, derajat
],
```

`aspect` di atas daftar gambar mengatur rasio kotak (lebar : tinggi).
Jumlah provinsi dibaca otomatis dari angka terakhir nama file
(`PulauJawa6.png` → 6 provinsi). Di HP posisi tidak berubah — hanya seluruh
kotak diperkecil seragam agar muat.

---

## Perintah Berguna

```bash
php artisan test --compact        # jalankan tes
vendor/bin/pint --dirty           # rapikan format kode
php artisan route:list            # daftar rute
php artisan migrate:fresh --seed  # reset database + data contoh
```

---

## Lisensi

MIT.
