<?php

namespace Database\Seeders;

use App\Models\Regency;
use App\Models\Venue;
use Illuminate\Database\Seeder;

/**
 * Daftar sanggar & tempat latihan seni budaya di Daerah Istimewa Yogyakarta.
 *
 * Jalankan ulang kapan saja (idempoten):
 *     php artisan db:seed --class=VenueSanggarSeeder --force
 */
class VenueSanggarSeeder extends Seeder
{
    public function run(): void
    {
        $created = 0;
        $updated = 0;

        foreach ($this->items() as $regencySlug => $venues) {
            $regency = Regency::where('slug', $regencySlug)->first();

            if (! $regency) {
                $this->command?->warn("Kabupaten/kota \"{$regencySlug}\" tidak ditemukan, dilewati.");

                continue;
            }

            foreach ($venues as $venue) {
                [$name, $skills, $latitude, $longitude] = $venue;

                $model = Venue::updateOrCreate(
                    ['slug' => str($name)->slug()],
                    [
                        'name' => $name,
                        'type' => $this->type($skills),
                        'category' => $this->category($skills),
                        'address' => $regency->name.', Daerah Istimewa Yogyakarta',
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'regency_id' => $regency->id,
                        'description' => $skills,
                        'skills' => $skills,
                    ]
                );

                $model->wasRecentlyCreated ? $created++ : $updated++;
            }
        }

        $this->command?->info("Sanggar DIY: {$created} baru, {$updated} diperbarui.");
    }

    /**
     * Kategori tampilan, diturunkan dari materi yang dipelajari.
     *
     * Urutan penting: tari diperiksa sebelum wayang, karena banyak sanggar
     * tari juga menyebut wayang (mis. wayang suket atau wayang wong).
     */
    private function category(string $skills): string
    {
        $s = str($skills)->lower();

        return match (true) {
            $s->contains(['tari', 'dance', 'beksa']) => 'Tari',
            $s->contains(['batik', 'membatik', 'canting']) => 'Batik',
            $s->contains(['wayang', 'tatah sungging']) => 'Wayang',
            $s->contains(['gamelan', 'karawitan']) => 'Karawitan',
            $s->contains(['jatilan', 'reog', 'teater', 'ketoprak', 'jemblung', 'pertunjukan']) => 'Pertunjukan',
            default => 'Lainnya',
        };
    }

    /**
     * Kolom `type` bawaan tabel (enum terbatas).
     */
    private function type(string $skills): string
    {
        $s = str($skills)->lower();

        return match (true) {
            $s->contains(['gamelan', 'karawitan']) => 'karawitan',
            $s->contains(['tari', 'dance', 'beksa']) => 'sanggar_tari',
            $s->contains(['pertunjukan', 'teater', 'ketoprak']) => 'komunitas',
            default => 'lainnya',
        };
    }

    /**
     * @return array<string, list<array{0: string, 1: string, 2: ?float, 3: ?float}>>
     */
    private function items(): array
    {
        return [

            // ============================================================
            // KOTA YOGYAKARTA — 15 tempat
            // ============================================================
            'kota-jogja' => [
                ['Sanggar Seni Kinanti Sekar', 'Tari klasik Yogyakarta, tari kreasi, tari Nusantara, tembang Jawa, aksara Jawa, wayang suket', -7.8072568, 110.367495],
                ['Siswa Among Beksa', 'Tari klasik gaya Yogyakarta, wayang wong, karawitan, tembang', -7.8067070, 110.3581944],
                ['LKP Tari Natya Lakshita', 'Tari klasik, tari kontemporer, tari kreasi', null, null],
                ['Sanggar Tari Kusuma Wiraga', 'Tari tradisional', null, null],
                ['Sanggar Seni Padma', 'Seni tari', null, null],
                ['Sanggar Tari Saraswati', 'Tari Bali', null, null],
                ['Sanggar Tari Siwa Nata Raja', 'Tari Bali, seni tari, seni lukis, tata rias', null, null],
                ['Sanggar Kalpika', 'Batik dan batik lukis', null, null],
                ['Museum Batik Yogyakarta', 'Membatik, batik tulis', null, null],
                ['Batik Seno Painting, Cloth & Course', 'Membatik, batik lukis', null, null],
                ['Lukman Batik Workshop', 'Membatik, canting, pewarnaan batik', null, null],
                ['Komunitas Gayam 16', 'Gamelan Jawa', null, null],
                ['Omah Wayang Yogyakarta', 'Wayang kulit dan kebudayaan pewayangan', null, null],
                ['Sanggar Jemblung', 'Seni pertunjukan Jemblung', null, null],
                ['Sanggar Tari Bagong Kussudiardja', 'Seni tari kontemporer dan tari kreasi', null, null],
            ],

            // ============================================================
            // KABUPATEN SLEMAN — 18 tempat
            // ============================================================
            'sleman' => [
                ['Sanggar Tari Kembang Sakura', 'Tari tradisional dan tari modern', null, null],
                ['Sanggar Tari Gandes Luwes', 'Seni tari', null, null],
                ['Sanggar Tari Larassanti', 'Seni tari', null, null],
                ['Sanggar Tari Cakra Kembang', 'Seni tari', null, null],
                ['Sanggar Tari Kembang Setaman', 'Seni tari', null, null],
                ['Sanggar Tari Manggar Sejanjang', 'Seni tari', null, null],
                ['Sanggar Tari Mangun Harjo', 'Seni tari', null, null],
                ['Sanggar Tari Margo Budoyo Bromonilan', 'Seni tari', null, null],
                ['Sanggar Tari Nakula Sadewa', 'Seni tari', null, null],
                ['Sanggar Tari Nilaksmitha Wirama Beksa', 'Seni tari', null, null],
                ['Sanggar Tari Pradita', 'Seni tari', null, null],
                ['Sanggar Tari Sekar Jayashree', 'Seni tari', null, null],
                ['Sanggar Tari Trisna Budaya Adi', 'Seni tari', null, null],
                ['LKP Bailamos Dance School', 'Seni tari dan dance', null, null],
                ['Larasati Dance Studio', 'Seni tari', null, null],
                ['Puri Shivanataraja Performing Art', 'Seni tari', null, null],
                ['Sanggar Tari Langen Bekso', 'Seni tari tradisional', null, null],
                ['Sanggar Tari Kembang Sore', 'Tari kreasi', null, null],
            ],

            // ============================================================
            // KABUPATEN BANTUL — 17 tempat
            // ============================================================
            'bantul' => [
                ['Padepokan Seni Bagong Kussudiardja', 'Tari, teater, musik, karawitan, ketoprak', null, null],
                ['Sanggar Tari Bimo Murti', 'Seni tari, Reog Wayang', null, null],
                ['Sanggar Tari Banjar Beksa Muda', 'Tari klasik', null, null],
                ['Sanggar Tari Bekti Taruno Ngalogo', 'Seni tari', null, null],
                ['Sanggar Tari Langgeng Budoyo', 'Seni tari', null, null],
                ['Sanggar Tari Nuansa Tradition', 'Seni tari tradisional', null, null],
                ['Omah Paes dan Sanggar Tari Ganes Kenes', 'Seni tari, tata rias pengantin', null, null],
                ['Pendopo Jomblang Dance Studio', 'Seni tari', null, null],
                ['Pendopo Tari ISI Yogyakarta', 'Seni tari', null, null],
                ['Sanggar Tari Akusara', 'Seni tari', null, null],
                ['Sanggar Tari dan Karawitan Langen Putra', 'Seni tari, karawitan', null, null],
                ['Sanggar Tari dan Rias Sekar Melati', 'Seni tari, tata rias', null, null],
                ['Sanggar Tari Ganes Khenes', 'Seni tari', null, null],
                ['Sanggar Tari Kembang Sore Cabang Bantul', 'Seni tari', null, null],
                ['Wisata Wayang Wukirsari', 'Wayang kulit, tatah sungging, mewarnai wayang', null, null],
                ['Omah Cangkem', 'Gamelan Jawa', null, null],
                ['Sanggar Tari Saraswati Pleret', 'Tari, musik, seni', null, null],
            ],

            // ============================================================
            // KABUPATEN KULON PROGO — 18 tempat
            // ============================================================
            'kulon-progo' => [
                ['LKP Tari Semusim', 'Seni tari', null, null],
                ['Sanggar Dewa Mambang', 'Seni tari', null, null],
                ['Sanggar Jati Budaya', 'Seni tari dan budaya', null, null],
                ['Sanggar Karawitan Lestari Budaya', 'Karawitan', null, null],
                ['Sanggar Langit Alang-alang', 'Seni pertunjukan', null, null],
                ['Sanggar Lintangsari', 'Seni tari', null, null],
                ['Sanggar Ngamarto', 'Seni tari', null, null],
                ['Sanggar Ngrenas', 'Seni tari', null, null],
                ['Sanggar Sekar Srikandi', 'Seni tari', null, null],
                ['Sanggar Move Art Dance', 'Seni tari', null, null],
                ['Sanggar Seni Bodronoyo Watumurah', 'Seni pertunjukan tradisional', null, null],
                ['Sanggar Seni Damarwulan', 'Seni tari', null, null],
                ['Sanggar Seni Jatilan Turonggo Sari', 'Jatilan', null, null],
                ['Sanggar Sripanglaras', 'Seni tari', null, null],
                ['Sanggar Tari Jagad Adiluhung', 'Seni tari', null, null],
                ['Sanggar Tari Sekar Kenes', 'Seni tari', null, null],
                ['Sanggar Tari Siwi Kinanthi', 'Seni tari', null, null],
                ['Sanggar Tari Sri Budaya', 'Tari kreasi', null, null],
            ],

            // ============================================================
            // KABUPATEN GUNUNGKIDUL — 6 tempat
            // ============================================================
            'gunung-kidul' => [
                ['Sanggar Tari Sario Kencono', 'Tari tradisional', null, null],
                ['Sanggar Sekar Nyentrik', 'Seni tari', null, null],
                ['Sanggar Tari Isna Laksita', 'Seni tari', null, null],
                ['Sanggar Tari Sambeng III', 'Seni tari', null, null],
                ['Sanggar Wagiyem', 'Seni tradisional', null, null],
                ['Sanggar Yatmin', 'Seni tradisional', null, null],
            ],
        ];
    }
}
