<?php

namespace Database\Seeders;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Island;
use App\Models\Mystery;
use App\Models\Province;
use App\Models\Quest;
use App\Models\Question;
use App\Models\Regency;
use App\Models\Show;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin & demo user
        $admin = User::create([
            'name' => 'Admin CultureRaya',
            'email' => 'admin@localculture.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $users = User::factory(24)->create();

        // Wilayah: pulau → provinsi → kabupaten/kota
        $islands = collect([
            'Sumatera' => ['Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Sumatera Selatan'],
            'Jawa' => ['DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur'],
            'Kalimantan' => ['Kalimantan Barat', 'Kalimantan Timur', 'Kalimantan Selatan'],
            'Sulawesi' => ['Sulawesi Selatan', 'Sulawesi Utara', 'Sulawesi Tengah'],
            'Bali Nusa Tenggara' => ['Bali', 'NTB', 'NTT'],
            'Maluku Papua' => ['Maluku', 'Papua', 'Papua Barat'],
        ])->map(function (array $provinces, string $islandName) {
            $island = Island::create([
                'name' => $islandName,
                'slug' => str($islandName)->slug(),
                'description' => "Pulau $islandName dengan kekayaan budaya yang luar biasa.",
            ]);

            return collect($provinces)->map(fn (string $p) => Province::create([
                'island_id' => $island->id,
                'name' => $p,
                'slug' => str($p)->slug(),
            ]));
        })->flatten();

        $regencies = $islands->flatMap(function (Province $province) {
            return collect(['Kota '.$province->name, 'Kab. '.$province->name])->map(
                fn (string $r) => Regency::create(['province_id' => $province->id, 'name' => $r, 'slug' => str($r)->slug()])
            );
        });

        // Kategori budaya
        $categories = collect([
            ['Rumah Adat', '🏠'], ['Pakaian Adat', '👘'], ['Tarian Tradisional', '💃'],
            ['Alat Musik Tradisional', '🥁'], ['Kuliner Khas', '🍜'], ['Tradisi & Kesenian', '🎭'],
        ])->map(fn (array $c) => CultureCategory::create(['name' => $c[0], 'slug' => str($c[0])->slug(), 'icon' => $c[1]]));

        // Materi budaya: setiap regency dapat 2 kategori x 2 materi
        $regencies->each(function (Regency $regency) use ($categories) {
            $categories->random(2)->each(function (CultureCategory $category) use ($regency) {
                for ($i = 1; $i <= 2; $i++) {
                    Culture::create([
                        'regency_id' => $regency->id,
                        'culture_category_id' => $category->id,
                        'name' => $category->name.' '.$regency->name.' '.$i,
                        'slug' => str($category->slug.'-'.$regency->slug.'-'.$i)->slug(),
                        'description' => "Materi {$category->name} khas {$regency->name}.",
                        'content' => fake()->paragraphs(3, true),
                        'philosophy' => fake()->boolean(60) ? fake()->paragraphs(2, true) : null,
                    ]);
                }
            });
        });

        // Quest harian (hari ini) & mingguan
        $daily = Quest::create([
            'type' => 'daily', 'title' => 'Kuis Harian Budaya', 'description' => 'Uji pengetahuan budayamu hari ini!',
            'subtitle' => 'Rumah adat, tarian & kuliner Nusantara',
            'question_count' => 10,
            'available_on' => today(), 'ends_at' => today()->endOfDay(), 'time_limit' => 7,
        ]);
        $weekly = Quest::create([
            'type' => 'weekly', 'title' => 'Kuis Mingguan Budaya Nusantara', 'description' => 'Tantangan mingguan seputar budaya Indonesia.',
            'subtitle' => 'Warisan budaya & alat musik tradisional',
            'question_count' => 10,
            'available_on' => now()->startOfWeek(), 'ends_at' => now()->endOfWeek(), 'time_limit' => 7,
        ]);

        $questionBank = [
            ['Rumah adat khas Sumatera Barat dengan atap bergonjong adalah...?', ['Rumah Gadang', 'Rumah Joglo', 'Rumah Honai', 'Rumah Bolon'], 0],
            ['Tari Kecak berasal dari pulau...?', ['Jawa', 'Bali', 'Sumatera', 'Papua'], 1],
            ['Angklung merupakan alat musik tradisional dari...?', ['Sunda, Jawa Barat', 'Bali', 'Minangkabau', 'Asmat'], 0],
            ['Rendang adalah kuliner khas...?', ['Padang', 'Bandung', 'Solo', 'Medan'], 0],
            ['Upacara Kasada dilakukan oleh suku...?', ['Baduy', 'Tengger', 'Dayak', 'Toraja'], 1],
            ['Rumah adat Honai berasal dari...?', ['Papua', 'Jawa', 'Batak', 'Betawi'], 0],
            ['Tari Saman berasal dari provinsi...?', ['Aceh', 'Bali', 'Jogja', 'Maluku'], 0],
            ['Gamelan adalah ansambel musik tradisional terutama dari...?', ['Jawa & Bali', 'Sumatera', 'Sulawesi', 'Kalimantan'], 0],
            ['Batik pertama kali diakui UNESCO berasal dari...?', ['Indonesia', 'Malaysia', 'Thailand', 'India'], 0],
            ['Tari Piring berasal dari...?', ['Sumatera Barat', 'Jawa Timur', 'Papua', 'Bali'], 0],
        ];

        foreach ([$daily, $weekly] as $quest) {
            foreach ($questionBank as $i => [$q, $options, $answer]) {
                Question::create([
                    'quest_id' => $quest->id,
                    'question' => $q,
                    'options' => $options,
                    'correct_index' => $answer,
                    'points' => 100,
                    'sort_order' => $i,
                ]);
            }
        }

        // Mystery Budaya
        collect([
            ['Rumah Gadang', 'Sumatera Barat', ['Atapnya melengkung runcing seperti tanduk kerbau.', 'Terdiri atas bilangan ganjil ruangan.', 'Fungsi utamanya sebagai rumah adat suku Minangkabau.']],
            ['Tari Kecak', 'Bali', ['Tarian tanpa alat musik, diiringi bunyi "cak-cak-cak".', 'Ditarik oleh puluhan pria duduk melingkar.', 'Menceritakan kisah Ramayana.']],
            ['Angklung', 'Jawa Barat', ['Alat musik dari bambu yang digoyang.', 'Setiap alat menghasilkan satu nada.', 'Diakui UNESCO sebagai warisan budaya takbenda.']],
            ['Rumah Honai', 'Papua', ['Rumah mungil beratap jerami berbentuk kubah.', 'Dibangun tanpa paku.', 'Rumah adat suku Dani.']],
            ['Tari Saman', 'Aceh', ['Ditarik oleh delapan orang atau lebih secara berbaris.', 'Gerakan tangan sangat cepat dan serempak.', 'Dijuluki "tarian seribu tangan".']],
        ])->each(fn (array $m) => Mystery::create([
            'answer' => $m[0], 'origin' => $m[1], 'clues' => $m[2], 'max_clues' => 3, 'time_limit' => 60,
        ]));

        // Pertunjukan & sanggar
        foreach ($regencies->random(8) as $regency) {
            Show::create([
                'title' => 'Festival Budaya '.$regency->name,
                'slug' => str('festival-budaya-'.$regency->slug)->slug(),
                'description' => 'Persembakan seni dan budaya dari '.$regency->name.' dengan berbagai pertunjukan tradisional.',
                'starts_at' => fake()->dateTimeBetween('+3 days', '+2 months'),
                'location_name' => 'Lapangan Utama '.$regency->name,
                'regency_id' => $regency->id,
                'ticket_price' => fake()->randomElement([0, 25000, 50000]),
                'organizer' => 'Dinas Kebudayaan '.$regency->name,
            ]);
        }

        foreach ($regencies->random(8) as $regency) {
            Venue::create([
                'name' => 'Sanggar Budaya '.$regency->name,
                'slug' => str('sanggar-budaya-'.$regency->slug)->slug(),
                'type' => fake()->randomElement(['sanggar_tari', 'gamelan', 'karawitan', 'komunitas']),
                'address' => 'Jl. Kebudayaan No. '.fake()->numberBetween(1, 99).', '.$regency->name,
                'regency_id' => $regency->id,
                'phone' => fake()->phoneNumber(),
                'description' => 'Tempat belajar seni dan budaya tradisional di '.$regency->name.'.',
            ]);
        }

        // Reputasi awal untuk leaderboard
        $users->each(fn (User $u) => $u->reputation()->create([
            'total_score' => fake()->numberBetween(100, 3000),
            'quests_completed' => fake()->numberBetween(0, 10),
            'mysteries_solved' => fake()->numberBetween(0, 5),
            'streak' => fake()->numberBetween(0, 5),
        ]));
        $admin->reputation()->create(['total_score' => 0]);

        $this->command->info('Seeded: 1 admin (admin@localculture.test / password), '.$users->count().' user demo, '.$islands->count().' pulau, '.$regencies->count().' kab/kota.');
    }
}
