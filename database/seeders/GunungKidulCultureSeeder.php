<?php

namespace Database\Seeders;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Regency;
use Illuminate\Database\Seeder;

/**
 * Materi budaya Kabupaten Gunungkidul, DI Yogyakarta.
 *
 * Jalankan ulang kapan saja (idempoten):
 *     php artisan db:seed --class=GunungKidulCultureSeeder --force
 */
class GunungKidulCultureSeeder extends Seeder
{
    public function run(): void
    {
        $regency = Regency::where('slug', 'gunung-kidul')->first();

        if (! $regency) {
            $this->command?->warn('Kabupaten Gunungkidul tidak ditemukan. Jalankan DatabaseSeeder dulu.');

            return;
        }

        $categories = CultureCategory::pluck('id', 'name');
        $items = $this->items();
        $created = 0;
        $updated = 0;

        foreach ($items as $item) {
            $categoryId = $categories[$item['category']] ?? null;

            if (! $categoryId) {
                $this->command?->warn("Kategori \"{$item['category']}\" tidak ada, dilewati: {$item['name']}");

                continue;
            }

            $culture = Culture::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'regency_id' => $regency->id,
                    'culture_category_id' => $categoryId,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'content' => $item['content'],
                    'philosophy' => $item['philosophy'] ?? null,
                ]
            );

            $culture->wasRecentlyCreated ? $created++ : $updated++;
        }

        $this->command?->info("Gunungkidul: {$created} materi baru, {$updated} diperbarui (total ".count($items).').');
    }

    /**
     * @return list<array{name: string, slug: string, category: string, description: string, content: string, philosophy?: ?string}>
     */
    private function items(): array
    {
        return [

            // ============================================================
            // SEJARAH & CIRI KHAS
            // ============================================================
            [
                'name' => 'Sejarah dan Asal-usul Gunungkidul',
                'slug' => 'sejarah-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Asal nama "Gunung Kidul", hubungan dengan Mataram Islam, dan penetapan kabupaten pada 1831.',
                'content' => 'Nama Gunungkidul berasal dari bahasa Jawa, yaitu kata "Gunung" yang berarti pegunungan atau kawasan berbukit dan "Kidul" yang berarti selatan. Nama tersebut menggambarkan letak geografis wilayah ini yang berada di bagian selatan Daerah Istimewa Yogyakarta.'
                    ."\n\n"
                    .'Secara historis, Gunungkidul memiliki hubungan erat dengan perkembangan kerajaan-kerajaan Jawa, khususnya Mataram Islam dan Kesultanan Yogyakarta. Pengaruh kebudayaan Jawa terlihat dalam bahasa, kesenian, adat istiadat, sistem sosial, serta berbagai tradisi masyarakat.'
                    ."\n\n"
                    .'Kabupaten Gunungkidul secara administratif dibentuk pada tahun 1831. Wonosari kemudian berkembang menjadi pusat pemerintahan Kabupaten Gunungkidul.'
                    ."\n\n"
                    .'Sejak dahulu, masyarakat Gunungkidul hidup dengan menyesuaikan diri terhadap kondisi alam yang didominasi perbukitan kapur. Sebagian wilayah memiliki keterbatasan sumber air permukaan sehingga masyarakat mengembangkan cara tradisional untuk menampung dan memanfaatkan air melalui telaga, sumur, serta sumber air bawah tanah.',
                'philosophy' => 'Nama wilayahnya sendiri sudah bercerita: gunung di selatan — tanah kapur yang menuntut ketangguhan warganya.',
            ],
            [
                'name' => 'Ciri Khas Budaya Gunungkidul',
                'slug' => 'ciri-khas-budaya-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Identitas Gunungkidul yang dibentuk alam karst: tradisi Rasulan, kesenian rakyat, dan kearifan air.',
                'content' => 'Kabupaten Gunungkidul merupakan salah satu kabupaten di Daerah Istimewa Yogyakarta yang memiliki kekayaan budaya, tradisi, kesenian, adat istiadat, sejarah, dan kearifan lokal yang sangat beragam.'
                    ."\n\n"
                    .'Gunungkidul dikenal dengan bentang alam perbukitan karst, gua-gua alami, pantai selatan, telaga, serta kawasan pegunungan kapur. Kondisi alam tersebut turut membentuk karakter kehidupan masyarakatnya, terutama dalam bidang pertanian, peternakan, pemanfaatan sumber daya alam, dan pelaksanaan tradisi yang diwariskan secara turun-temurun.'
                    ."\n\n"
                    .'Masyarakat Gunungkidul masih mempertahankan berbagai kebudayaan leluhur yang menjadi identitas daerah. Tradisi seperti Rasulan, Nyadran, Bersih Desa, dan berbagai kesenian tradisional masih dilaksanakan di sejumlah wilayah. Selain itu, Gunungkidul juga memiliki kekayaan kuliner khas, kerajinan tradisional, serta peninggalan sejarah yang menjadi bagian penting dari identitas budayanya.'
                    ."\n\n"
                    .'Ciri khas utama Gunungkidul terletak pada hubungan erat antara budaya masyarakat dengan lingkungan alam perbukitan karst dan kawasan pesisir selatan. Keterbatasan sumber air, kehidupan pertanian lahan kering, serta pemanfaatan sumber daya alam membentuk kearifan lokal yang diwariskan dari generasi ke generasi.',
            ],

            // ============================================================
            // TRADISI
            // ============================================================
            [
                'name' => 'Rasulan',
                'slug' => 'rasulan-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tradisi paling terkenal Gunungkidul: syukuran panen dengan kirab, wayang, jathilan, dan kenduri warga.',
                'content' => 'Rasulan merupakan salah satu tradisi paling terkenal di Gunungkidul. Tradisi ini juga dikenal sebagai Bersih Desa atau Merti Dusun. Rasulan biasanya dilaksanakan setelah masyarakat menyelesaikan masa panen sebagai bentuk rasa syukur atas hasil pertanian, keselamatan, dan kesejahteraan masyarakat.'
                    ."\n\n"
                    .'Pelaksanaannya dapat berbeda antara satu desa dengan desa lainnya. Kegiatan Rasulan biasanya meliputi doa bersama, kenduri, kirab budaya, pertunjukan kesenian tradisional, pentas wayang kulit, jathilan, serta kegiatan kebersamaan warga.'
                    ."\n\n"
                    .'Masyarakat biasanya menyiapkan berbagai makanan tradisional untuk dinikmati bersama. Rasulan bukan hanya kegiatan adat, tetapi juga menjadi sarana mempererat hubungan sosial dan menjaga persatuan masyarakat.',
                'philosophy' => 'Rasulan adalah rasa syukur yang dirayakan bersama — panen tidak dinikmati sendiri, tetapi dibagi ke seluruh warga.',
            ],
            [
                'name' => 'Nyadran Gunungkidul',
                'slug' => 'nyadran-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Ziarah dan bersih makam leluhur menjelang Ramadan, dilanjutkan kenduri dan makan bersama.',
                'content' => 'Nyadran merupakan tradisi masyarakat Jawa yang dilakukan untuk mendoakan leluhur dan keluarga yang telah meninggal dunia. Tradisi ini biasanya dilaksanakan menjelang bulan Ramadan atau pada waktu tertentu sesuai kebiasaan masyarakat setempat.'
                    ."\n\n"
                    .'Kegiatan Nyadran dapat meliputi membersihkan makam, berziarah, membaca doa, mengadakan kenduri, dan makan bersama. Tradisi ini mencerminkan penghormatan kepada leluhur serta hubungan kekeluargaan yang masih dijaga oleh masyarakat Gunungkidul.',
            ],
            [
                'name' => 'Bersih Desa',
                'slug' => 'bersih-desa-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Memohon keselamatan dan ketenteraman desa, menjaga keseimbangan manusia, alam, dan lingkungan.',
                'content' => 'Bersih Desa merupakan tradisi yang bertujuan memohon keselamatan, ketenteraman, dan kesejahteraan bagi seluruh masyarakat desa. Tradisi ini biasanya dilaksanakan secara bersama-sama oleh warga dengan melibatkan tokoh masyarakat dan pemuka adat.'
                    ."\n\n"
                    .'Kegiatannya dapat berupa doa bersama, kenduri, pertunjukan kesenian, dan kegiatan sosial. Bersih Desa memiliki hubungan erat dengan kepercayaan masyarakat terhadap pentingnya menjaga keseimbangan kehidupan manusia, alam, dan lingkungan.',
            ],
            [
                'name' => 'Wiwitan Gunungkidul',
                'slug' => 'wiwitan-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Syukuran menjelang panen sebagai ungkapan terima kasih atas hasil pertanian lahan kering.',
                'content' => 'Wiwitan merupakan tradisi masyarakat agraris yang berkaitan dengan kegiatan pertanian, terutama menjelang panen. Tradisi ini dilakukan sebagai ungkapan rasa syukur atas hasil tanaman dan harapan agar kegiatan pertanian berikutnya berjalan dengan baik.'
                    ."\n\n"
                    .'Dalam pelaksanaannya, masyarakat biasanya menyiapkan makanan tradisional dan mengadakan doa bersama. Tradisi ini memperlihatkan hubungan erat antara kehidupan masyarakat Gunungkidul dengan alam dan kegiatan pertanian.',
            ],
            [
                'name' => 'Bersih Telaga',
                'slug' => 'bersih-telaga',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Membersihkan telaga bersama-sama — wujud kepedulian pada sumber air di kawasan karst.',
                'content' => 'Telaga memiliki peranan penting bagi masyarakat Gunungkidul karena kondisi geografisnya yang didominasi kawasan karst. Pada masa lalu, telaga menjadi salah satu sumber air penting untuk memenuhi kebutuhan sehari-hari dan kegiatan pertanian.'
                    ."\n\n"
                    .'Di beberapa wilayah, masyarakat memiliki kegiatan membersihkan telaga atau lingkungan sumber air secara bersama-sama. Kegiatan tersebut mencerminkan kepedulian masyarakat terhadap kelestarian lingkungan dan pentingnya menjaga sumber kehidupan.',
                'philosophy' => 'Di tanah kapur, air adalah harta — menjaganya berarti menjaga kelangsungan hidup bersama.',
            ],
            [
                'name' => 'Kenduri',
                'slug' => 'kenduri-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Berkumpul untuk berdoa dan berbagi makanan; bagian dari Rasulan, Nyadran, dan kegiatan adat lain.',
                'content' => 'Kenduri merupakan kegiatan berkumpul untuk berdoa dan memohon keselamatan serta keberkahan. Dalam kegiatan ini, masyarakat biasanya membawa makanan untuk didoakan dan dibagikan kepada peserta.'
                    ."\n\n"
                    .'Kenduri sering menjadi bagian dari tradisi Rasulan, Nyadran, dan berbagai kegiatan adat lainnya. Tradisi ini menjadi perekat sosial yang mempertemukan warga dari berbagai usia.',
            ],

            // ============================================================
            // KESENIAN
            // ============================================================
            [
                'name' => 'Jathilan Gunungkidul',
                'slug' => 'jathilan-gunungkidul',
                'category' => 'Tarian Tradisional',
                'description' => 'Tari kuda anyaman bambu yang menggambarkan pasukan berkuda, dengan unsur magis pada sebagian kelompok.',
                'content' => 'Jathilan merupakan kesenian tari tradisional Jawa yang menggunakan properti kuda tiruan dari anyaman bambu atau bahan lainnya. Para penari bergerak mengikuti iringan musik tradisional seperti kendang, gong, dan instrumen gamelan.'
                    ."\n\n"
                    .'Pertunjukan Jathilan biasanya menampilkan gerakan yang menggambarkan pasukan berkuda. Dalam beberapa kelompok kesenian, pertunjukan juga memiliki bagian yang berkaitan dengan unsur magis atau trance.'
                    ."\n\n"
                    .'Jathilan menjadi salah satu kesenian rakyat yang masih berkembang di berbagai wilayah Gunungkidul.',
            ],
            [
                'name' => 'Reog Gunungkidul',
                'slug' => 'reog-gunungkidul',
                'category' => 'Tarian Tradisional',
                'description' => 'Pertunjukan rakyat yang memadukan tari, musik, kostum, dan gerakan teatrikal; tampil saat Rasulan.',
                'content' => 'Reog merupakan kesenian tradisional yang menggabungkan tarian, musik, kostum, dan gerakan teatrikal. Di lingkungan masyarakat Gunungkidul, kesenian reog dapat ditemukan dalam berbagai bentuk pertunjukan rakyat.'
                    ."\n\n"
                    .'Reog biasanya ditampilkan dalam acara Rasulan, perayaan desa, dan kegiatan kebudayaan. Pertunjukan ini menjadi sarana hiburan sekaligus pelestarian kesenian tradisional.',
            ],
            [
                'name' => 'Gejog Lesung Gunungkidul',
                'slug' => 'gejog-lesung-gunungkidul',
                'category' => 'Alat Musik Tradisional',
                'description' => 'Musik dari lesung dan alu penumbuk padi — simbol kebersamaan masyarakat desa.',
                'content' => 'Gejog Lesung merupakan kesenian musik tradisional yang menggunakan lesung dan alu sebagai alat musik utama. Lesung merupakan alat tradisional untuk menumbuk padi, sedangkan alu digunakan untuk menumbuknya.'
                    ."\n\n"
                    .'Bunyi yang dihasilkan dari pukulan alu terhadap lesung membentuk irama tertentu. Kesenian ini dahulu berkaitan erat dengan aktivitas masyarakat agraris, khususnya pengolahan hasil panen.'
                    ."\n\n"
                    .'Gejog Lesung menjadi simbol kebersamaan, kerja sama, dan kehidupan masyarakat pedesaan.',
                'philosophy' => 'Alat menumbuk padi menjadi alat musik — kerja bersama berubah menjadi irama.',
            ],
            [
                'name' => 'Karawitan Gunungkidul',
                'slug' => 'karawitan-gunungkidul',
                'category' => 'Alat Musik Tradisional',
                'description' => 'Gamelan (gong, kendang, saron, bonang, kenong) untuk adat, wayang, dan acara kebudayaan.',
                'content' => 'Karawitan merupakan seni musik tradisional Jawa yang menggunakan seperangkat gamelan. Instrumennya meliputi gong, kendang, saron, bonang, kenong, dan berbagai alat musik tradisional lainnya.'
                    ."\n\n"
                    .'Karawitan sering dimainkan dalam kegiatan adat, pertunjukan wayang kulit, upacara tradisional, dan acara kebudayaan.',
            ],
            [
                'name' => 'Wayang Kulit Gunungkidul',
                'slug' => 'wayang-kulit-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Pertunjukan dalang dengan cerita Mahabharata dan Ramayana, sarat nilai kehidupan.',
                'content' => 'Wayang Kulit merupakan seni pertunjukan tradisional yang menggunakan boneka pipih dari kulit yang dimainkan oleh seorang dalang. Pertunjukan ini biasanya diiringi gamelan dan menggunakan cerita yang bersumber dari kisah Mahabharata, Ramayana, maupun cerita tradisional Jawa.'
                    ."\n\n"
                    .'Wayang Kulit memiliki nilai pendidikan dan filosofi kehidupan yang berkaitan dengan kejujuran, keberanian, tanggung jawab, serta perjuangan melawan kejahatan.',
                'philosophy' => 'Layar dan bayangan mengajarkan bahwa yang tampak belum tentu yang sebenarnya.',
            ],
            [
                'name' => 'Campursari',
                'slug' => 'campursari',
                'category' => 'Alat Musik Tradisional',
                'description' => 'Perpaduan musik tradisional Jawa dengan instrumen modern, populer di acara hiburan warga.',
                'content' => 'Campursari merupakan kesenian musik yang memadukan unsur musik tradisional Jawa dengan instrumen musik modern. Musik ini cukup populer di masyarakat Gunungkidul dan sering ditampilkan dalam berbagai acara hiburan, perayaan, maupun kegiatan masyarakat.',
            ],

            // ============================================================
            // KULINER
            // ============================================================
            [
                'name' => 'Tiwul',
                'slug' => 'tiwul',
                'category' => 'Kuliner Khas',
                'description' => 'Makanan pokok dari tepung gaplek; simbol ketangguhan pangan masyarakat Gunungkidul.',
                'content' => 'Tiwul merupakan makanan tradisional yang terbuat dari tepung gaplek atau singkong yang dikeringkan dan diolah menjadi makanan. Dahulu, tiwul menjadi makanan pokok masyarakat Gunungkidul, terutama ketika beras sulit diperoleh.'
                    ."\n\n"
                    .'Tiwul biasanya dikukus dan dapat disajikan dengan kelapa parut, gula merah, atau lauk-pauk. Saat ini, tiwul juga dikenal sebagai makanan khas dan oleh-oleh dari Gunungkidul.',
                'philosophy' => 'Tiwul lahir dari keterbatasan: ketika beras sulit, singkong menjelma makanan pokok yang menguatkan.',
            ],
            [
                'name' => 'Gatot',
                'slug' => 'gatot',
                'category' => 'Kuliner Khas',
                'description' => 'Olahan singkong kering yang dikukus, bertekstur kenyal, disajikan dengan kelapa parut.',
                'content' => 'Gatot merupakan makanan tradisional yang dibuat dari singkong yang telah dikeringkan melalui proses tertentu. Singkong tersebut kemudian direndam, dikukus, dan disajikan dengan kelapa parut serta sedikit garam.'
                    ."\n\n"
                    .'Gatot memiliki tekstur kenyal dan rasa khas. Makanan ini menjadi salah satu contoh pemanfaatan singkong sebagai bahan pangan tradisional masyarakat Gunungkidul.',
            ],
            [
                'name' => 'Gaplek',
                'slug' => 'gaplek-gunungkidul',
                'category' => 'Kuliner Khas',
                'description' => 'Singkong kupas yang dikeringkan; bahan dasar tiwul dan gatot, tahan disimpan lama.',
                'content' => 'Gaplek merupakan singkong yang telah dikupas, dipotong, dan dikeringkan. Produk ini dahulu sangat penting bagi masyarakat Gunungkidul karena dapat disimpan dalam waktu relatif lama.'
                    ."\n\n"
                    .'Gaplek dapat diolah menjadi tepung dan berbagai makanan tradisional, termasuk tiwul dan gatot.',
            ],
            [
                'name' => 'Walang Goreng',
                'slug' => 'walang-goreng',
                'category' => 'Kuliner Khas',
                'description' => 'Belalang berbumbu bawang putih, ketumbar, dan garam, digoreng renyah.',
                'content' => 'Walang Goreng merupakan salah satu kuliner unik yang terkenal dari Gunungkidul. Makanan ini terbuat dari belalang yang dibersihkan, diberi bumbu, kemudian digoreng hingga renyah.'
                    ."\n\n"
                    .'Walang Goreng biasanya menggunakan bumbu bawang putih, ketumbar, dan garam. Makanan ini menjadi salah satu kuliner yang menarik perhatian wisatawan karena bahan dasarnya tidak biasa bagi sebagian masyarakat.',
            ],
            [
                'name' => 'Sego Abang',
                'slug' => 'sego-abang',
                'category' => 'Kuliner Khas',
                'description' => 'Nasi merah dengan sayur lombok ijo, tempe, dan tahu — hasil pertanian lokal.',
                'content' => 'Sego Abang atau nasi merah merupakan makanan khas yang banyak dikenal di kawasan Gunungkidul. Nasi ini dibuat dari beras merah dan biasanya disajikan bersama sayur lombok ijo, tempe, tahu, serta lauk tradisional lainnya.'
                    ."\n\n"
                    .'Sego Abang mencerminkan kebiasaan masyarakat dalam memanfaatkan hasil pertanian lokal.',
            ],
            [
                'name' => 'Sayur Lombok Ijo',
                'slug' => 'sayur-lombok-ijo',
                'category' => 'Kuliner Khas',
                'description' => 'Masakan cabai hijau dengan tempe, santan, dan rempah; pelengkap Sego Abang.',
                'content' => 'Sayur Lombok Ijo merupakan masakan tradisional yang menggunakan cabai hijau sebagai salah satu bahan utamanya. Masakan ini biasanya diolah bersama tempe, santan, dan bumbu rempah.'
                    ."\n\n"
                    .'Sayur Lombok Ijo sering disajikan sebagai pelengkap Sego Abang dan menjadi salah satu hidangan yang dikenal dalam kuliner tradisional Gunungkidul.',
            ],
            [
                'name' => 'Bakmi Jawa',
                'slug' => 'bakmi-jawa-gunungkidul',
                'category' => 'Kuliner Khas',
                'description' => 'Mi dimasak dengan tungku arang dan racikan rempah, telur, serta ayam.',
                'content' => 'Bakmi Jawa merupakan makanan yang berkembang di berbagai wilayah Yogyakarta, termasuk Gunungkidul. Makanan ini biasanya dimasak menggunakan tungku arang dengan bumbu tradisional.'
                    ."\n\n"
                    .'Bakmi Jawa memiliki cita rasa khas karena menggunakan racikan rempah, telur, ayam, dan bahan pelengkap lainnya.',
            ],

            // ============================================================
            // KERAJINAN
            // ============================================================
            [
                'name' => 'Kerajinan Batu Alam',
                'slug' => 'kerajinan-batu-alam-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Batu alam khas kawasan karst diolah menjadi ornamen, hiasan, dan bahan bangunan.',
                'content' => 'Gunungkidul memiliki potensi batu alam yang dimanfaatkan untuk berbagai kebutuhan kerajinan dan bahan bangunan. Batu alam dapat diolah menjadi hiasan, ornamen, serta berbagai produk dekoratif.',
            ],
            [
                'name' => 'Kerajinan Kayu Gunungkidul',
                'slug' => 'kerajinan-kayu-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kayu diolah menjadi perabot, ukiran, dan hiasan oleh perajin lokal.',
                'content' => 'Kerajinan kayu berkembang di sejumlah wilayah Gunungkidul. Kayu diolah menjadi perabot rumah tangga, ukiran, hiasan, dan berbagai produk kerajinan lainnya.',
            ],
            [
                'name' => 'Kerajinan Anyaman',
                'slug' => 'kerajinan-anyaman-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Anyaman bambu dan pandan: keranjang, tempat penyimpanan, dan perlengkapan rumah tangga.',
                'content' => 'Masyarakat juga mengenal kerajinan anyaman yang memanfaatkan bahan alami seperti bambu dan pandan. Produk anyaman dapat berupa keranjang, tempat penyimpanan, perlengkapan rumah tangga, dan berbagai peralatan tradisional.',
            ],
            [
                'name' => 'Batik Gunungkidul',
                'slug' => 'batik-gunungkidul',
                'category' => 'Pakaian Adat',
                'description' => 'Motif batik yang terinspirasi alam karst, flora, fauna, dan kehidupan masyarakat setempat.',
                'content' => 'Batik menjadi salah satu bentuk pengembangan identitas budaya daerah. Motif batik yang dikembangkan oleh perajin lokal dapat mengambil inspirasi dari lingkungan alam, flora, fauna, dan kehidupan masyarakat Gunungkidul.',
            ],

            // ============================================================
            // KEARIFAN LOKAL
            // ============================================================
            [
                'name' => 'Gotong Royong Masyarakat Gunungkidul',
                'slug' => 'gotong-royong-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kerja bersama membangun fasilitas umum, membersihkan lingkungan, dan kegiatan adat.',
                'content' => 'Gotong royong merupakan nilai sosial yang masih kuat dalam kehidupan masyarakat Gunungkidul. Warga biasanya bekerja sama dalam membangun fasilitas umum, membersihkan lingkungan, memperbaiki jalan desa, dan melaksanakan kegiatan adat.',
            ],
            [
                'name' => 'Kehidupan Masyarakat Agraris',
                'slug' => 'masyarakat-agraris-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Pertanian lahan kering dengan singkong, jagung, padi, dan palawija.',
                'content' => 'Sebagian masyarakat Gunungkidul memiliki hubungan erat dengan kegiatan pertanian dan peternakan. Tanaman seperti singkong, jagung, padi, kacang tanah, dan berbagai tanaman palawija menjadi bagian dari kehidupan masyarakat.'
                    ."\n\n"
                    .'Pertanian lahan kering membentuk pola konsumsi pangan yang memanfaatkan singkong, jagung, dan tanaman palawija — yang kemudian melahirkan kuliner seperti tiwul, gatot, dan gaplek.',
            ],
            [
                'name' => 'Pelestarian Sumber Air',
                'slug' => 'pelestarian-sumber-air',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kearifan menjaga telaga dan sumber air di kawasan karst yang kering.',
                'content' => 'Karena kondisi alamnya yang didominasi kawasan karst, air menjadi sumber daya yang sangat penting. Masyarakat mengembangkan kebiasaan menjaga telaga, sumber air, dan lingkungan sekitar agar tetap dapat dimanfaatkan.'
                    ."\n\n"
                    .'Keterbatasan air di sejumlah wilayah mendorong masyarakat untuk mengembangkan kebiasaan menghemat dan menyimpan air — sebuah kearifan yang diwariskan antargenerasi.',
                'philosophy' => 'Hemat air bukan sekadar kebiasaan, melainkan pelajaran bertahan hidup di tanah karst.',
            ],
            [
                'name' => 'Bahasa Jawa Gunungkidul',
                'slug' => 'bahasa-jawa-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Bahasa sehari-hari dalam keluarga, upacara adat, dan kesenian; sarana pewarisan tata krama.',
                'content' => 'Bahasa Jawa merupakan bahasa yang digunakan dalam kehidupan sehari-hari masyarakat Gunungkidul. Penggunaannya terlihat dalam komunikasi keluarga, kegiatan sosial, upacara adat, dan kesenian tradisional.'
                    ."\n\n"
                    .'Bahasa Jawa juga menjadi sarana pewarisan nilai budaya dan tata krama kepada generasi muda.',
            ],

            // ============================================================
            // TEMPAT BERSEJARAH
            // ============================================================
            [
                'name' => 'Situs Megalitik Sokoliman',
                'slug' => 'situs-sokoliman',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Peninggalan prasejarah berupa batu-batu besar untuk ritual dan penguburan masa lampau.',
                'content' => 'Situs Sokoliman merupakan salah satu peninggalan kebudayaan prasejarah di Gunungkidul. Situs ini berkaitan dengan tradisi megalitik yang ditandai dengan penggunaan batu-batu besar sebagai bagian dari aktivitas ritual atau penguburan pada masa lampau.'
                    ."\n\n"
                    .'Peninggalan tersebut menjadi bukti bahwa wilayah Gunungkidul telah dihuni manusia sejak zaman prasejarah.',
            ],
            [
                'name' => 'Goa Pindul',
                'slug' => 'goa-pindul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Gua dengan aliran sungai bawah tanah, menampilkan karakter geologi karst Gunungkidul.',
                'content' => 'Goa Pindul merupakan gua yang terkenal dengan aliran sungai bawah tanahnya. Selain menjadi objek wisata alam, kawasan gua juga memperlihatkan karakter geologi karst Gunungkidul yang sangat khas.',
            ],
            [
                'name' => 'Goa Jomblang',
                'slug' => 'goa-jomblang',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Gua vertikal dengan fenomena "cahaya surga" dari celah atas gua.',
                'content' => 'Goa Jomblang merupakan gua vertikal yang terbentuk akibat proses geologi. Gua ini terkenal dengan fenomena cahaya matahari yang masuk melalui bagian atas gua dan menciptakan pemandangan yang unik.',
            ],
            [
                'name' => 'Pantai Parangtritis dan Pesisir Selatan',
                'slug' => 'parangtritis-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kehidupan nelayan pesisir selatan dengan tradisi syukuran hasil laut dan harapan keselamatan.',
                'content' => 'Kawasan pesisir selatan Gunungkidul memiliki hubungan erat dengan kehidupan masyarakat nelayan dan tradisi yang berkaitan dengan laut. Masyarakat pesisir mengembangkan berbagai kebiasaan dan kegiatan budaya sebagai bentuk rasa syukur atas hasil laut serta harapan akan keselamatan ketika melaut.'
                    ."\n\n"
                    .'Tradisi syukuran hasil laut menjadi salah satu bentuk hubungan masyarakat dengan lingkungan pesisir.',
            ],
            [
                'name' => 'Pantai Wediombo',
                'slug' => 'pantai-wediombo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kawasan pesisir dengan kegiatan perikanan, pariwisata, dan pemanfaatan sumber daya laut.',
                'content' => 'Pantai Wediombo merupakan salah satu kawasan pesisir Gunungkidul yang memiliki nilai penting bagi kehidupan masyarakat setempat. Kawasan pesisir ini juga berkaitan dengan kegiatan perikanan, pariwisata, dan pemanfaatan sumber daya laut.',
            ],
            [
                'name' => 'Hubungan Budaya Gunungkidul dengan Alam',
                'slug' => 'budaya-dan-alam-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Karst, telaga, gua, dan pantai selatan membentuk pola hidup serta identitas budaya warga.',
                'content' => 'Kebudayaan masyarakat Gunungkidul tidak dapat dipisahkan dari kondisi alamnya. Kawasan karst, perbukitan kapur, gua, telaga, dan pantai selatan memiliki pengaruh besar terhadap pola kehidupan masyarakat.'
                    ."\n\n"
                    .'Keterbatasan air di sejumlah wilayah mendorong masyarakat untuk mengembangkan kebiasaan menghemat dan menyimpan air. Pertanian lahan kering juga membentuk pola konsumsi pangan yang memanfaatkan singkong, jagung, dan tanaman palawija.'
                    ."\n\n"
                    .'Sementara itu, masyarakat pesisir memiliki kebudayaan yang berkaitan dengan laut dan kegiatan nelayan. Tradisi syukuran hasil laut menjadi salah satu bentuk hubungan masyarakat dengan lingkungan pesisir.'
                    ."\n\n"
                    .'Kondisi alam tersebut menjadikan Gunungkidul memiliki identitas budaya yang berbeda dari wilayah Yogyakarta lainnya.',
                'philosophy' => 'Alam yang keras tidak menindas, tetapi menempa — budaya Gunungkidul adalah jawaban manusia atas tanah kapur.',
            ],
            [
                'name' => 'Nilai-nilai Budaya Masyarakat Gunungkidul',
                'slug' => 'nilai-budaya-gunungkidul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tujuh nilai: syukur, gotong royong, hormat leluhur, peduli lingkungan, sederhana, kebersamaan, pelestarian.',
                'content' => 'Berbagai tradisi dan kesenian masyarakat Gunungkidul mengandung nilai-nilai budaya yang penting, antara lain:'
                    ."\n\n"
                    .'1. Rasa syukur, yang tercermin dalam tradisi Rasulan dan kegiatan setelah panen.'
                    ."\n"
                    .'2. Gotong royong, yang terlihat dalam kegiatan masyarakat dan pelaksanaan upacara adat.'
                    ."\n"
                    .'3. Penghormatan kepada leluhur, yang tercermin dalam tradisi Nyadran dan ziarah makam.'
                    ."\n"
                    .'4. Kepedulian terhadap lingkungan, yang terlihat dalam kegiatan menjaga telaga dan sumber air.'
                    ."\n"
                    .'5. Kesederhanaan, yang tercermin dalam kehidupan masyarakat dan pemanfaatan hasil alam.'
                    ."\n"
                    .'6. Kebersamaan, yang terlihat dalam kegiatan kenduri, perayaan desa, dan pertunjukan kesenian.'
                    ."\n"
                    .'7. Pelestarian budaya, yang dilakukan melalui pewarisan tradisi dan kesenian kepada generasi muda.',
                'philosophy' => 'Tujuh nilai ini bukan sekadar catatan, melainkan pedoman hidup yang diwariskan dari generasi ke generasi.',
            ],
        ];
    }
}
