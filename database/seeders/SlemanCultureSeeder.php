<?php

namespace Database\Seeders;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Regency;
use Illuminate\Database\Seeder;

/**
 * Materi budaya Kabupaten Sleman, DI Yogyakarta.
 *
 * Catatan penting:
 * Naskah sumber sendiri mengoreksi dua hal, dan koreksi itu diikuti di sini:
 *  - "Apem Wonolelo" berada di Pleret, Kabupaten Bantul — BUKAN Sleman,
 *    sehingga tidak dimasukkan sebagai kuliner Sleman.
 *  - "Tempe Pondoh" belum punya bukti asal-usul lokal yang memadai,
 *    sehingga tidak dicantumkan.
 *
 * Jalankan ulang kapan saja (idempoten):
 *     php artisan db:seed --class=SlemanCultureSeeder --force
 */
class SlemanCultureSeeder extends Seeder
{
    public function run(): void
    {
        $regency = Regency::where('slug', 'sleman')->first();

        if (! $regency) {
            $this->command?->warn('Kabupaten Sleman tidak ditemukan. Jalankan DatabaseSeeder dulu.');

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

        $this->command?->info("Sleman: {$created} materi baru, {$updated} diperbarui (total ".count($items).').');
    }

    /**
     * @return list<array{name: string, slug: string, category: string, description: string, content: string, philosophy?: ?string}>
     */
    private function items(): array
    {
        return [

            // ============================================================
            // SEJARAH
            // ============================================================
            [
                'name' => 'Sejarah Kabupaten Sleman',
                'slug' => 'sejarah-kabupaten-sleman',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Dari masa prasejarah, Hindu-Buddha, Mataram Islam, hingga ditetapkan 15 Mei 1916 sebagai Kabupaten Sleman.',
                'content' => 'Kabupaten Sleman merupakan salah satu kabupaten di Daerah Istimewa Yogyakarta yang memiliki kekayaan sejarah dan kebudayaan. Perkembangan budaya Sleman dipengaruhi oleh peradaban Hindu-Buddha, kerajaan Mataram Islam, Kesultanan Yogyakarta, kehidupan masyarakat agraris, serta kondisi geografis yang berada di lereng Gunung Merapi.'
                    ."\n\n"
                    .'Sejarah kebudayaan Sleman dapat ditelusuri sejak masa prasejarah melalui berbagai peninggalan arkeologis. Temuan benda-benda seperti batu lumpang, batu dakon, batu pipisan, dan gerabah menunjukkan bahwa kawasan ini telah dihuni manusia sejak masa lampau. Kesuburan tanah vulkanik dan ketersediaan sumber air turut mendukung perkembangan permukiman serta kegiatan pertanian.'
                    ."\n\n"
                    .'Pada masa Hindu-Buddha, wilayah Sleman menjadi salah satu kawasan penting dalam perkembangan peradaban Jawa kuno. Hal tersebut dibuktikan dengan keberadaan berbagai candi, seperti Candi Prambanan, Candi Kalasan, Candi Sari, Candi Sambisari, Candi Ijo, dan Candi Barong. Peninggalan tersebut menunjukkan perkembangan agama, arsitektur, seni pahat, dan kehidupan masyarakat pada masa kerajaan.'
                    ."\n\n"
                    .'Setelah masa Hindu-Buddha, wilayah Sleman turut mengalami perkembangan kebudayaan pada masa Mataram Islam dan Kesultanan Yogyakarta. Pengaruh kebudayaan Jawa dan Islam terlihat melalui keberadaan masjid bersejarah, makam tokoh agama, tradisi masyarakat, serta kesenian rakyat. Beberapa peninggalan penting antara lain Masjid Pathok Negara Plosokuning di Ngaglik, Masjid Pathok Negara Mlangi di Gamping, dan kompleks Masjid Sulthoni serta makam Pangeran Purboyo di Wotgaleh, Berbah.'
                    ."\n\n"
                    .'Secara administratif, Kabupaten Sleman ditetapkan pada 15 Mei 1916 berdasarkan Rijksblad Nomor 11 Tahun 1916. Pada awal pembentukannya, wilayah ini dikenal sebagai Kabupaten Sulaiman, yang kemudian berkembang menjadi Kabupaten Sleman. Tanggal 15 Mei diperingati sebagai Hari Jadi Kabupaten Sleman.'
                    ."\n\n"
                    .'Seiring perkembangan zaman, Sleman tumbuh menjadi wilayah yang menggabungkan kehidupan pertanian, pendidikan, perdagangan, pariwisata, industri, dan pelestarian budaya. Identitas budayanya terbentuk dari perpaduan peninggalan peradaban kuno, tradisi masyarakat desa, kesenian rakyat, kehidupan keagamaan, dan kebudayaan yang berkaitan dengan Gunung Merapi.',
                'philosophy' => 'Budaya Sleman adalah lapisan zaman: prasejarah, Hindu-Buddha, Islam, keraton, dan masyarakat agraris lereng Merapi menyatu dalam satu identitas.',
            ],
            [
                'name' => 'Ciri Khas Kebudayaan Sleman',
                'slug' => 'ciri-khas-budaya-sleman',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Enam penanda budaya Sleman: candi, Gunung Merapi, Saparan Bekakak, kesenian rakyat, kuliner, dan tradisi desa.',
                'content' => 'Kabupaten Sleman mempunyai sejumlah ciri khas budaya yang membedakannya dari wilayah lain di Daerah Istimewa Yogyakarta.'
                    ."\n\n"
                    .'Pertama, Sleman dikenal memiliki banyak peninggalan peradaban Hindu-Buddha, terutama candi-candi bersejarah yang tersebar di wilayahnya. Peninggalan tersebut menjadi bukti penting perkembangan kebudayaan Jawa kuno.'
                    ."\n\n"
                    .'Kedua, kebudayaan masyarakat Sleman dipengaruhi oleh keberadaan Gunung Merapi. Kehidupan masyarakat di kawasan lereng gunung berkaitan erat dengan pertanian, sumber air, kondisi alam, dan upaya beradaptasi dengan aktivitas vulkanik.'
                    ."\n\n"
                    .'Ketiga, Sleman memiliki tradisi Saparan Bekakak yang terkenal dari Ambarketawang, Gamping. Tradisi ini menggunakan sepasang boneka pengantin dari adonan ketan yang menjadi bagian utama prosesi adat.'
                    ."\n\n"
                    .'Keempat, Sleman memiliki kesenian tradisional yang memadukan seni pertunjukan dan pengaruh keagamaan, seperti Tari Badui, Kuntulan, Jathilan Lancur, Reog Keprajuritan, dan Sruntul.'
                    ."\n\n"
                    .'Kelima, Sleman mempunyai kuliner yang identik dengan beberapa wilayahnya, seperti Jadah Tempe dari kawasan Kaliurang, Ayam Goreng Kalasan, dan berbagai olahan pangan tradisional lainnya.'
                    ."\n\n"
                    .'Keenam, tradisi masyarakat desa masih menjadi bagian penting dari kehidupan budaya Sleman. Bersih desa, merti desa, nyadran, wiwitan, dan merti kali merupakan contoh tradisi yang memperkuat hubungan sosial dan menjaga ingatan budaya masyarakat.',
            ],

            // ============================================================
            // TRADISI & UPACARA
            // ============================================================
            [
                'name' => 'Saparan Bekakak Ambarketawang',
                'slug' => 'saparan-bekakak',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tradisi paling khas Sleman: sepasang boneka pengantin dari adonan ketan diarak setiap bulan Sapar.',
                'content' => 'Saparan Bekakak merupakan salah satu tradisi adat paling terkenal di Kabupaten Sleman. Tradisi ini berpusat di Ambarketawang, Kapanewon Gamping, dan dilaksanakan pada bulan Sapar dalam kalender Jawa.'
                    ."\n\n"
                    .'Ciri utama tradisi ini adalah sepasang bekakak, yaitu boneka berbentuk pengantin yang dibuat dari adonan tepung ketan dan diisi gula merah. Bekakak menjadi perlengkapan utama dalam rangkaian upacara adat.'
                    ."\n\n"
                    .'Tradisi ini memiliki cerita asal-usul yang berkaitan dengan masa pembangunan pesanggrahan Ambarketawang pada masa pemerintahan Sultan Hamengku Buwono I. Cerita yang berkembang di masyarakat menghubungkan tradisi ini dengan tokoh-tokoh lokal dan peristiwa yang dipercaya pernah terjadi di kawasan tersebut.'
                    ."\n\n"
                    .'Dalam pelaksanaannya, bekakak diarak melalui prosesi kirab bersama perlengkapan upacara lainnya. Kegiatan ini melibatkan masyarakat setempat dan dapat disertai pertunjukan kesenian serta kegiatan budaya.'
                    ."\n\n"
                    .'Saparan Bekakak memiliki makna sebagai bentuk penghormatan terhadap warisan leluhur, ungkapan harapan akan keselamatan, dan sarana memperkuat kebersamaan masyarakat.'
                    ."\n\n"
                    .'Ciri khas Saparan Bekakak adalah penggunaan sepasang boneka pengantin dari adonan ketan, pelaksanaan pada bulan Sapar, serta keterkaitannya dengan sejarah lokal Ambarketawang. Tradisi ini merupakan salah satu identitas budaya paling menonjol di Kabupaten Sleman.',
                'philosophy' => 'Bekakak dari ketan dan gula bukan sekadar sesaji — ia adalah doa berwujud makanan yang dibagi bersama.',
            ],
            [
                'name' => 'Jamasan Pusaka Tombak Kyai Turunsih',
                'slug' => 'jamasan-kyai-turunsih',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Perawatan pusaka daerah Sleman, tombak pemberian Sultan HB X pada Hari Jadi ke-83 (1999), setiap bulan Sura.',
                'content' => 'Jamasan Tombak Kyai Turunsih merupakan tradisi perawatan pusaka milik Pemerintah Kabupaten Sleman. Jamasan adalah kegiatan membersihkan dan merawat benda pusaka melalui tata cara tradisional tertentu.'
                    ."\n\n"
                    .'Tombak Kyai Turunsih merupakan pusaka yang diberikan oleh Sri Sultan Hamengku Buwono X sebagai penanda sejarah pada peringatan Hari Jadi Kabupaten Sleman ke-83 pada tahun 1999.'
                    ."\n\n"
                    .'Upacara jamasan dilaksanakan pada bulan Sura dalam kalender Jawa atau bertepatan dengan bulan Muharam dalam kalender Islam. Kegiatan ini melibatkan unsur pemerintah daerah dan abdi dalem Keraton Yogyakarta.'
                    ."\n\n"
                    .'Tradisi tersebut memperlihatkan hubungan antara pemerintah daerah, kebudayaan keraton, dan pemeliharaan benda pusaka. Jamasan juga menjadi sarana untuk mengenalkan sejarah daerah kepada masyarakat.'
                    ."\n\n"
                    .'Ciri khasnya adalah penggunaan Tombak Kyai Turunsih sebagai pusaka daerah dan pelaksanaan ritual perawatan sebagai bagian dari tradisi budaya Kabupaten Sleman.',
            ],
            [
                'name' => 'Nyadran Sleman',
                'slug' => 'nyadran-sleman',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Bersih makam, doa, dan kenduri untuk menghormati leluhur; waktunya berbeda-beda tiap desa.',
                'content' => 'Nyadran merupakan tradisi masyarakat Jawa yang juga berkembang di berbagai wilayah Kabupaten Sleman. Tradisi ini biasanya dilakukan untuk menghormati leluhur melalui kegiatan membersihkan makam, berdoa, mengunjungi tempat pemakaman, dan mengadakan kenduri atau makan bersama.'
                    ."\n\n"
                    .'Waktu pelaksanaannya dapat berbeda-beda sesuai kebiasaan masyarakat setempat. Sebagian masyarakat melaksanakan nyadran menjelang bulan Ramadan, sedangkan kelompok lain menyesuaikannya dengan kalender adat atau tradisi desa.'
                    ."\n\n"
                    .'Nyadran menjadi sarana mempererat hubungan sosial karena warga berkumpul, bekerja sama, dan mempertahankan ingatan mengenai leluhur atau tokoh yang dihormati.'
                    ."\n\n"
                    .'Ciri khas nyadran di setiap wilayah dapat terlihat dari tempat pelaksanaan, tokoh yang dihormati, susunan kegiatan, makanan yang disiapkan, dan cerita sejarah yang diwariskan. Tradisi ini bukan hanya milik Sleman, tetapi menjadi bagian dari kehidupan budaya masyarakatnya.',
            ],
            [
                'name' => 'Bersih Desa dan Merti Desa',
                'slug' => 'merti-desa-sleman',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Syukuran desa: kerja bakti, kenduri, kirab, dan pertunjukan seni sebagai ungkapan syukur.',
                'content' => 'Bersih desa atau merti desa merupakan tradisi yang banyak ditemukan di Kabupaten Sleman. Tradisi ini dilaksanakan sebagai ungkapan syukur atas keselamatan, kesehatan, hasil pertanian, dan keberlangsungan kehidupan masyarakat.'
                    ."\n\n"
                    .'Kegiatan yang dilakukan dapat meliputi kerja bakti, membersihkan lingkungan, kenduri, doa bersama, pertunjukan kesenian, kirab, dan kegiatan sosial lainnya. Di beberapa desa, kegiatan juga berkaitan dengan makam leluhur, sumber air, atau tempat yang memiliki sejarah khusus.'
                    ."\n\n"
                    .'Merti desa memiliki peran penting dalam memperkuat hubungan sosial. Masyarakat bekerja sama mempersiapkan kegiatan dan mempertahankan kebiasaan yang diwariskan dari generasi sebelumnya.'
                    ."\n\n"
                    .'Ciri khas tradisi ini terletak pada perpaduan rasa syukur, gotong royong, penghormatan terhadap sejarah desa, dan pemeliharaan lingkungan.',
            ],
            [
                'name' => 'Wiwitan Sleman',
                'slug' => 'wiwitan-sleman',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Syukuran panen masyarakat agraris sebagai penghargaan atas pangan dan kerja petani.',
                'content' => 'Wiwitan merupakan tradisi masyarakat agraris yang berkaitan dengan kegiatan panen. Tradisi ini dilakukan sebagai ungkapan syukur atas hasil pertanian dan harapan agar kehidupan masyarakat tetap sejahtera.'
                    ."\n\n"
                    .'Dalam pelaksanaannya, masyarakat dapat menyiapkan makanan, berdoa, dan berbagi hidangan sesuai kebiasaan setempat. Susunan kegiatan berbeda-beda di setiap desa.'
                    ."\n\n"
                    .'Di Sleman, tradisi semacam ini mencerminkan hubungan antara kebudayaan dan kehidupan pertanian. Tanah yang subur, ketersediaan air, dan hasil panen memiliki peranan penting dalam kehidupan masyarakat.'
                    ."\n\n"
                    .'Wiwitan juga mengajarkan penghargaan terhadap pangan, kerja petani, dan kebersamaan. Tradisi ini bukan tradisi eksklusif Sleman, tetapi menjadi bagian dari warisan budaya masyarakat agraris di wilayah tersebut.',
                'philosophy' => 'Menghargai sebutir padi berarti menghargai kerja, tanah, dan air yang menghidupinya.',
            ],
            [
                'name' => 'Merti Kali',
                'slug' => 'merti-kali',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Merawat sungai dan sumber air di kawasan hulu lereng Merapi melalui kerja bakti dan doa bersama.',
                'content' => 'Merti kali merupakan tradisi yang berkaitan dengan pemeliharaan dan penghormatan terhadap sungai atau sumber air. Air mempunyai peranan penting bagi masyarakat Sleman, terutama untuk kebutuhan rumah tangga, irigasi pertanian, dan kegiatan ekonomi.'
                    ."\n\n"
                    .'Kegiatan merti kali dapat berupa membersihkan lingkungan sungai, doa bersama, dan kegiatan adat yang disesuaikan dengan kebiasaan masyarakat setempat.'
                    ."\n\n"
                    .'Tradisi ini memiliki hubungan dengan kondisi geografis Sleman yang berada di kawasan hulu sejumlah sungai yang bersumber dari lereng Gunung Merapi.'
                    ."\n\n"
                    .'Ciri khas merti kali adalah perpaduan antara tradisi masyarakat, kepedulian terhadap sumber air, dan kesadaran menjaga lingkungan. Tradisi tersebut juga dapat menjadi sarana pendidikan lingkungan bagi generasi muda.',
                'philosophy' => 'Sleman adalah hulu; menjaga sungai di sini berarti menjaga kehidupan di hilir.',
            ],

            // ============================================================
            // KESENIAN
            // ============================================================
            [
                'name' => 'Tari Badui',
                'slug' => 'tari-badui',
                'category' => 'Tarian Tradisional',
                'description' => 'Tari berkelompok bernuansa Islam dari Sleman, diiringi musik dan nyanyian serempak.',
                'content' => 'Tari Badui merupakan kesenian tradisional yang berkembang di Kabupaten Sleman dan memiliki pengaruh kuat dari tradisi Islam.'
                    ."\n\n"
                    .'Kesenian ini menampilkan gerakan tari berkelompok yang diiringi musik dan nyanyian. Gerakannya dilakukan secara teratur dan serempak sehingga menghasilkan pertunjukan yang dinamis.'
                    ."\n\n"
                    .'Dalam perkembangannya, Tari Badui menjadi bagian dari kegiatan budaya masyarakat, perayaan desa, dan acara kesenian. Kesenian ini juga menjadi media untuk menyampaikan pesan keagamaan dan mempererat hubungan sosial.'
                    ."\n\n"
                    .'Ciri khas Tari Badui adalah perpaduan gerak tari berkelompok, iringan musik, dan nyanyian yang dipengaruhi tradisi Islam. Kesenian ini menunjukkan pertemuan antara kebudayaan Jawa dan perkembangan Islam di masyarakat.',
            ],
            [
                'name' => 'Kuntulan',
                'slug' => 'kuntulan',
                'category' => 'Tarian Tradisional',
                'description' => 'Tari berkelompok dengan gerakan serempak, namanya dikaitkan dengan burung kuntul.',
                'content' => 'Kuntulan merupakan kesenian tradisional yang berkembang di lingkungan masyarakat Sleman. Pertunjukan ini memadukan gerakan berkelompok, irama, dan unsur musik atau nyanyian yang berkaitan dengan tradisi setempat.'
                    ."\n\n"
                    .'Nama Kuntulan dalam penjelasan budaya lokal sering dihubungkan dengan burung kuntul yang dikenal bergerak secara berkelompok. Gerakan yang serempak menjadi salah satu karakter yang menonjol dalam pertunjukan.'
                    ."\n\n"
                    .'Kuntulan menjadi contoh kreativitas masyarakat desa dalam mengembangkan kesenian yang diwariskan dari generasi ke generasi.'
                    ."\n\n"
                    .'Ciri khas Kuntulan terletak pada gerakan kolektif, keserempakan penari, dan perpaduan seni gerak dengan unsur musik tradisional.',
                'philosophy' => 'Gerak serempak meniru burung kuntul — mengajarkan bahwa kekompakan adalah kekuatan.',
            ],
            [
                'name' => 'Jathilan Lancur',
                'slug' => 'jathilan-lancur',
                'category' => 'Tarian Tradisional',
                'description' => 'Jathilan gaya lokal Sleman; Warisan Budaya Takbenda DI Yogyakarta 2024.',
                'content' => 'Jathilan Lancur merupakan salah satu bentuk kesenian tradisional yang memiliki hubungan dengan kebudayaan Kabupaten Sleman.'
                    ."\n\n"
                    .'Jathilan secara umum menggunakan properti kuda tiruan yang dibuat dari anyaman bambu atau bahan lain. Para penari bergerak mengikuti iringan musik tradisional, seperti kendang dan gamelan.'
                    ."\n\n"
                    .'Setiap kelompok jathilan dapat mempunyai gaya gerak, kostum, musik, dan urutan pertunjukan yang berbeda. Perbedaan tersebut menjadi bagian dari identitas kelompok dan lingkungan budaya tempat kesenian berkembang.'
                    ."\n\n"
                    .'Jathilan Lancur telah mendapatkan pengakuan sebagai Warisan Budaya Takbenda tingkat Daerah Istimewa Yogyakarta pada 2024.'
                    ."\n\n"
                    .'Ciri khas Jathilan Lancur terletak pada identitas gaya lokal dan keberadaan kelompok pelestari yang mempertahankan kesenian tersebut.',
            ],
            [
                'name' => 'Reog Keprajuritan',
                'slug' => 'reog-keprajuritan',
                'category' => 'Tarian Tradisional',
                'description' => 'Reog bergaya keprajuritan khas Sleman; Warisan Budaya Takbenda Indonesia 2025.',
                'content' => 'Reog Keprajuritan merupakan kesenian pertunjukan tradisional yang berkembang di Sleman. Kesenian ini mempunyai karakter yang berkaitan dengan gambaran keprajuritan dan pertunjukan rakyat.'
                    ."\n\n"
                    .'Unsur gerak, kostum, musik, dan formasi pemain menjadi bagian penting dalam pertunjukannya. Kesenian ini memperlihatkan kreativitas masyarakat dalam mengembangkan seni pertunjukan yang berkaitan dengan identitas lokal.'
                    ."\n\n"
                    .'Reog Keprajuritan telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun penetapan 2025.'
                    ."\n\n"
                    .'Ciri khasnya adalah karakter pertunjukan keprajuritan dan gaya lokal yang membedakannya dari kesenian lain yang menggunakan nama reog.',
            ],
            [
                'name' => 'Sruntul',
                'slug' => 'sruntul',
                'category' => 'Tarian Tradisional',
                'description' => 'Pertunjukan rakyat Sleman yang memadukan tari, teater, dan musik; WBTb Indonesia 2025.',
                'content' => 'Sruntul merupakan kesenian pertunjukan rakyat yang tumbuh dan berkembang di lingkungan masyarakat Sleman.'
                    ."\n\n"
                    .'Kesenian ini memadukan unsur tari, teater rakyat, musik tradisional, dan cerita yang diangkat dari kehidupan masyarakat atau cerita lokal.'
                    ."\n\n"
                    .'Sebagai seni pertunjukan rakyat, Sruntul memiliki hubungan erat dengan lingkungan sosial tempat kesenian tersebut diwariskan. Pertunjukan tidak hanya berfungsi sebagai hiburan, tetapi juga menjadi sarana penyampaian cerita dan nilai kehidupan.'
                    ."\n\n"
                    .'Sruntul telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun penetapan 2025. Ciri khas Sruntul adalah perpaduan tari, teater rakyat, dan musik tradisional dalam bentuk pertunjukan yang dekat dengan kehidupan masyarakat.',
            ],
            [
                'name' => 'Karawitan dan Wayang Kulit Sleman',
                'slug' => 'karawitan-wayang-sleman',
                'category' => 'Alat Musik Tradisional',
                'description' => 'Gamelan dan wayang kulit yang hidup melalui kelompok seni lokal Sleman.',
                'content' => 'Karawitan merupakan seni musik tradisional Jawa yang menggunakan gamelan sebagai perangkat utama. Kesenian ini berkembang di berbagai wilayah Sleman dan digunakan untuk mengiringi pertunjukan wayang kulit, tari tradisional, upacara adat, serta kegiatan budaya masyarakat.'
                    ."\n\n"
                    .'Wayang kulit merupakan seni pertunjukan yang menggunakan tokoh-tokoh wayang dari kulit yang dimainkan oleh dalang. Pertunjukan biasanya diiringi gamelan dan disertai cerita yang bersumber dari Mahabharata, Ramayana, atau kisah lain dalam tradisi pewayangan Jawa.'
                    ."\n\n"
                    .'Wayang kulit tidak hanya berfungsi sebagai hiburan, tetapi juga menjadi media penyampaian nilai moral, kritik sosial, dan pandangan hidup.'
                    ."\n\n"
                    .'Ciri khas karawitan dan wayang kulit di Sleman terletak pada keberadaan kelompok seni lokal yang menjaga keberlanjutan tradisi Jawa. Namun, keduanya merupakan bagian dari kebudayaan Jawa yang lebih luas dan tidak hanya dimiliki Sleman.',
            ],

            // ============================================================
            // CANDI
            // ============================================================
            [
                'name' => 'Candi Prambanan',
                'slug' => 'candi-prambanan',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kompleks candi Hindu untuk Trimurti di perbatasan DIY–Jawa Tengah, dengan relief Ramayana.',
                'content' => 'Candi Prambanan merupakan kompleks percandian Hindu yang terletak di perbatasan Daerah Istimewa Yogyakarta dan Jawa Tengah. Kompleks ini merupakan salah satu peninggalan sejarah dan kebudayaan terpenting di kawasan Sleman.'
                    ."\n\n"
                    .'Candi Prambanan dibangun pada masa kerajaan Hindu di Jawa dan berkaitan dengan pemujaan terhadap Trimurti, yaitu Brahma sebagai pencipta, Wisnu sebagai pemelihara, dan Siwa sebagai pelebur.'
                    ."\n\n"
                    .'Kompleks ini memiliki bangunan utama yang menjulang tinggi serta dihiasi relief yang menggambarkan kisah-kisah dalam tradisi Hindu. Salah satu kisah yang terkenal adalah Ramayana.'
                    ."\n\n"
                    .'Candi Prambanan menunjukkan kemajuan teknologi bangunan, seni pahat, dan kehidupan keagamaan masyarakat Jawa kuno.'
                    ."\n\n"
                    .'Ciri khasnya adalah arsitektur candi Hindu yang megah, relief cerita Ramayana, dan kedudukannya sebagai salah satu pusat peninggalan peradaban Hindu-Buddha di kawasan Sleman.',
                'philosophy' => 'Tiga bangunan utama melambangkan Trimurti: penciptaan, pemeliharaan, dan peleburan — siklus kehidupan yang utuh.',
            ],
            [
                'name' => 'Candi Kalasan',
                'slug' => 'candi-kalasan',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Peninggalan Buddha di Kalasan dengan hiasan arsitektur khas masa Jawa kuno.',
                'content' => 'Candi Kalasan merupakan peninggalan sejarah yang terletak di Kapanewon Kalasan, Kabupaten Sleman.'
                    ."\n\n"
                    .'Candi ini berkaitan dengan perkembangan agama Buddha pada masa kerajaan Jawa kuno. Bangunannya memiliki ciri arsitektur dan hiasan yang menunjukkan kemampuan seni bangunan pada masa tersebut.'
                    ."\n\n"
                    .'Candi Kalasan juga dikenal karena penggunaan hiasan serta detail arsitektur yang memperlihatkan perkembangan seni pahat dan teknologi bangunan.'
                    ."\n\n"
                    .'Keberadaan candi ini menunjukkan bahwa kawasan Sleman memiliki hubungan erat dengan perkembangan agama Buddha, selain pengaruh Hindu yang terlihat pada Candi Prambanan.'
                    ."\n\n"
                    .'Ciri khas Candi Kalasan adalah nilai sejarahnya sebagai peninggalan Buddha dan keunikan arsitektur yang memperlihatkan kemajuan peradaban Jawa kuno.',
            ],
            [
                'name' => 'Candi Sambisari',
                'slug' => 'candi-sambisari',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Candi Hindu di Purwomartani yang unik karena berada di bawah permukaan tanah sekitarnya.',
                'content' => 'Candi Sambisari terletak di wilayah Purwomartani, Kapanewon Kalasan, Kabupaten Sleman.'
                    ."\n\n"
                    .'Candi ini merupakan peninggalan Hindu yang memiliki keunikan karena bangunan utamanya berada di bawah permukaan tanah di sekitarnya.'
                    ."\n\n"
                    .'Candi Sambisari ditemukan kembali pada abad ke-20 setelah lama tertimbun material tanah. Penemuan tersebut membuka kesempatan untuk mempelajari kembali sejarah arsitektur dan kehidupan keagamaan masyarakat Jawa kuno.'
                    ."\n\n"
                    .'Kompleks candi memiliki bangunan utama dan beberapa bangunan pendamping yang memperlihatkan tata ruang peribadatan pada masa lalu.'
                    ."\n\n"
                    .'Ciri khas Candi Sambisari adalah letaknya yang lebih rendah daripada permukaan tanah di sekelilingnya serta nilai arkeologisnya sebagai peninggalan Hindu.',
            ],
            [
                'name' => 'Candi Sari',
                'slug' => 'candi-sari',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Candi Buddha bertingkat di Kalasan, diduga pernah menjadi tempat tinggal atau pembelajaran biksu.',
                'content' => 'Candi Sari terletak di kawasan Kalasan, Kabupaten Sleman. Candi ini merupakan peninggalan Buddha yang memiliki bentuk bangunan bertingkat dengan sejumlah ruang di dalamnya.'
                    ."\n\n"
                    .'Candi Sari diperkirakan memiliki hubungan dengan kegiatan keagamaan Buddha dan kemungkinan fungsi sebagai tempat tinggal atau pembelajaran bagi para biksu.'
                    ."\n\n"
                    .'Bangunannya memperlihatkan teknik konstruksi dan hiasan arsitektur yang berkembang pada masa kerajaan Jawa kuno.'
                    ."\n\n"
                    .'Candi Sari menjadi bagian penting dari lanskap sejarah Kalasan, yang memiliki sejumlah peninggalan Hindu-Buddha dalam wilayah yang relatif berdekatan.'
                    ."\n\n"
                    .'Ciri khasnya adalah bentuk bangunan bertingkat, unsur arsitektur Buddha, dan kemungkinan fungsinya yang berkaitan dengan kehidupan keagamaan pada masa lampau.',
            ],
            [
                'name' => 'Candi Ijo',
                'slug' => 'candi-ijo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kompleks candi Hindu di perbukitan Sambirejo, Prambanan, dengan panorama dataran Yogyakarta.',
                'content' => 'Candi Ijo merupakan kompleks candi Hindu yang terletak di kawasan perbukitan Sambirejo, Kapanewon Prambanan, Kabupaten Sleman.'
                    ."\n\n"
                    .'Letaknya di kawasan yang relatif tinggi memberikan pemandangan ke arah dataran Yogyakarta. Posisi tersebut juga memperlihatkan hubungan antara pemilihan lokasi bangunan keagamaan dan lingkungan alam pada masa lalu.'
                    ."\n\n"
                    .'Kompleks Candi Ijo terdiri atas sejumlah bangunan yang memiliki hubungan dengan pemujaan Hindu. Relief dan unsur arsitekturnya menjadi sumber penting untuk mempelajari perkembangan seni bangunan Jawa kuno.'
                    ."\n\n"
                    .'Candi ini juga menunjukkan bahwa peninggalan arkeologi Sleman tidak hanya ditemukan di dataran rendah, tetapi juga di kawasan perbukitan.'
                    ."\n\n"
                    .'Ciri khas Candi Ijo adalah lokasinya di perbukitan, kompleks bangunan Hindu, serta perpaduan antara warisan arkeologi dan lanskap alam.',
            ],
            [
                'name' => 'Candi Barong',
                'slug' => 'candi-barong',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Candi Hindu bertingkat di perbukitan Prambanan, terkait pemujaan Wisnu dan Dewi Sri.',
                'content' => 'Candi Barong merupakan kompleks candi Hindu yang terletak di wilayah perbukitan Prambanan, Kabupaten Sleman.'
                    ."\n\n"
                    .'Kompleks ini memiliki bangunan dengan karakter arsitektur yang berbeda dari Candi Prambanan. Peninggalan tersebut memperlihatkan keragaman bentuk bangunan keagamaan pada masa kerajaan Jawa kuno.'
                    ."\n\n"
                    .'Candi Barong memiliki hubungan dengan tradisi keagamaan Hindu, termasuk pemujaan yang berkaitan dengan Wisnu dan Dewi Sri dalam penafsiran arkeologis terhadap kompleks tersebut.'
                    ."\n\n"
                    .'Keberadaan Candi Barong memperkaya pemahaman tentang perkembangan agama, arsitektur, dan kehidupan masyarakat pada masa lampau.'
                    ."\n\n"
                    .'Ciri khasnya adalah bentuk bangunan bertingkat di kawasan perbukitan dan hubungan sejarahnya dengan perkembangan kebudayaan Hindu di Sleman.',
            ],

            // ============================================================
            // KULINER
            // ============================================================
            [
                'name' => 'Jadah Tempe Kaliurang',
                'slug' => 'jadah-tempe-kaliurang',
                'category' => 'Kuliner Khas',
                'description' => 'Pasangan jadah ketan gurih dan tempe bacem manis, ikon kuliner Kaliurang.',
                'content' => 'Jadah Tempe merupakan kuliner yang sangat dikenal dari kawasan Kaliurang, Kabupaten Sleman.'
                    ."\n\n"
                    .'Jadah dibuat dari beras ketan yang diolah hingga menghasilkan tekstur padat dan lengket. Makanan ini biasanya disajikan bersama tempe yang dimasak dengan bumbu manis atau bacem.'
                    ."\n\n"
                    .'Perpaduan jadah yang gurih dengan tempe yang manis menghasilkan cita rasa khas. Hidangan ini menjadi salah satu makanan yang sering dicari wisatawan ketika mengunjungi kawasan Kaliurang.'
                    ."\n\n"
                    .'Jadah Tempe juga mempunyai hubungan dengan perkembangan usaha kuliner keluarga dan tradisi penyajian makanan lokal.'
                    ."\n\n"
                    .'Ciri khasnya terletak pada perpaduan jadah ketan dan tempe bacem yang dikenal sebagai pasangan makanan khas Kaliurang.',
                'philosophy' => 'Gurih dan manis disajikan bersama — kesederhanaan yang menjadi penanda perjalanan ke Kaliurang.',
            ],
            [
                'name' => 'Ayam Goreng Kalasan',
                'slug' => 'ayam-goreng-kalasan',
                'category' => 'Kuliner Khas',
                'description' => 'Ayam berbumbu yang dimasak dengan air kelapa lalu digoreng; gurih dan sedikit manis.',
                'content' => 'Ayam Goreng Kalasan merupakan salah satu kuliner yang sangat dikenal dari wilayah Kalasan, Kabupaten Sleman.'
                    ."\n\n"
                    .'Hidangan ini dibuat dari ayam yang dimasak menggunakan bumbu khas, kemudian digoreng hingga menghasilkan tekstur dan rasa yang khas. Dalam sejumlah resep, ayam terlebih dahulu dimasak bersama bumbu dan santan atau air kelapa sebelum digoreng.'
                    ."\n\n"
                    .'Ayam Goreng Kalasan umumnya memiliki cita rasa gurih dan sedikit manis. Hidangan ini sering disajikan bersama sambal, lalapan, dan nasi.'
                    ."\n\n"
                    .'Kuliner ini berkembang menjadi identitas gastronomi Kalasan dan menjadi salah satu makanan yang dikenal luas oleh wisatawan.'
                    ."\n\n"
                    .'Ciri khasnya adalah teknik pengolahan ayam berbumbu dengan rasa gurih-manis dan keterkaitannya dengan kawasan Kalasan di Sleman.',
            ],
            [
                'name' => 'Salak Pondoh',
                'slug' => 'salak-pondoh',
                'category' => 'Kuliner Khas',
                'description' => 'Buah manis dari lereng Merapi (Turi, Tempel, Pakem) yang menjadi identitas pertanian Sleman.',
                'content' => 'Salak Pondoh merupakan salah satu produk pertanian yang paling identik dengan Kabupaten Sleman.'
                    ."\n\n"
                    .'Buah ini dikenal memiliki rasa manis dengan tekstur daging yang khas. Budidayanya berkembang di kawasan lereng Gunung Merapi, terutama di wilayah Turi, Tempel, dan Pakem.'
                    ."\n\n"
                    .'Perkembangan salak Pondoh tidak hanya berkaitan dengan pertanian, tetapi juga membentuk identitas ekonomi dan pariwisata daerah. Kebun salak menjadi bagian dari wisata pertanian yang memungkinkan pengunjung mengenal proses budidaya dan memetik buah secara langsung.'
                    ."\n\n"
                    .'Salak Pondoh juga memiliki hubungan dengan pengembangan usaha pengolahan hasil pertanian, pemasaran buah, dan kegiatan ekonomi masyarakat desa.'
                    ."\n\n"
                    .'Ciri khasnya adalah keterkaitan kuat antara varietas salak Pondoh, kawasan pertanian lereng Merapi, dan identitas Kabupaten Sleman.',
                'philosophy' => 'Manisnya salak adalah hasil tanah vulkanik Merapi — alam memberi, manusia merawat.',
            ],
            [
                'name' => 'Bebek Bacem Nglengis',
                'slug' => 'bebek-bacem-nglengis',
                'category' => 'Kuliner Khas',
                'description' => 'Bebek dimasak bacem hingga bumbu meresap, dikaitkan dengan kawasan Nglengis, Sleman.',
                'content' => 'Bebek Bacem Nglengis dikenal sebagai salah satu kuliner yang dikaitkan dengan kawasan Nglengis di Kabupaten Sleman.'
                    ."\n\n"
                    .'Bacem merupakan teknik memasak dengan bumbu yang menghasilkan cita rasa manis dan gurih. Bahan makanan dimasak bersama bumbu hingga meresap, kemudian dapat digoreng atau diolah lebih lanjut sesuai resep.'
                    ."\n\n"
                    .'Pada hidangan bebek bacem, proses pembumbuan menjadi unsur penting untuk menghasilkan rasa dan tekstur yang khas.'
                    ."\n\n"
                    .'Kuliner ini menunjukkan bagaimana teknik memasak tradisional dapat membentuk identitas pangan lokal. Namun, sejarah asal-usul, resep asli, dan wilayah produksi perlu didokumentasikan berdasarkan sumber lokal agar tidak tercampur dengan variasi bebek bacem dari daerah lain.'
                    ."\n\n"
                    .'Ciri khasnya adalah perpaduan bahan bebek dengan teknik pembaceman yang dikaitkan dengan tradisi kuliner setempat.',
            ],

            // ============================================================
            // LINGKUNGAN & SOSIAL
            // ============================================================
            [
                'name' => 'Budaya Pertanian Lereng Gunung Merapi',
                'slug' => 'budaya-lereng-merapi',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tanah vulkanik, sumber air, dan pengetahuan lokal masyarakat lereng Merapi.',
                'content' => 'Gunung Merapi merupakan unsur alam yang sangat memengaruhi kehidupan masyarakat Sleman.'
                    ."\n\n"
                    .'Wilayah lereng Merapi memiliki tanah vulkanik yang mendukung pertanian, termasuk budidaya sayuran, buah-buahan, dan tanaman pangan. Sumber air dari kawasan gunung juga memiliki peran penting bagi kebutuhan rumah tangga dan irigasi.'
                    ."\n\n"
                    .'Kehidupan masyarakat di kawasan ini membentuk pengetahuan lokal mengenai musim, tanah, air, serta cara menghadapi perubahan kondisi alam.'
                    ."\n\n"
                    .'Dalam konteks budaya, hubungan dengan Merapi tercermin melalui kebiasaan masyarakat, pengetahuan lingkungan, tradisi desa, dan cerita yang diwariskan antargenerasi.'
                    ."\n\n"
                    .'Namun, tradisi yang berkaitan dengan Gunung Merapi dapat memiliki bentuk dan sejarah berbeda antara satu komunitas dengan komunitas lain. Oleh karena itu, dokumentasi budaya sebaiknya mencantumkan nama desa, pelaku tradisi, dan sumber informasi yang jelas.'
                    ."\n\n"
                    .'Ciri khas budaya lereng Merapi adalah hubungan erat antara lingkungan vulkanik, pertanian, pengetahuan lokal, dan kehidupan masyarakat.',
                'philosophy' => 'Hidup berdampingan dengan gunung berarti belajar membaca alam, bukan menaklukkannya.',
            ],
            [
                'name' => 'Bahasa Jawa dan Tata Krama Sleman',
                'slug' => 'bahasa-jawa-sleman',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tingkat tutur bahasa Jawa sebagai penanda kesopanan dalam kehidupan sosial Sleman.',
                'content' => 'Bahasa Jawa merupakan unsur penting dalam kehidupan masyarakat Sleman. Bahasa ini digunakan dalam percakapan sehari-hari, kesenian tradisional, upacara adat, cerita rakyat, dan kegiatan sosial.'
                    ."\n\n"
                    .'Penggunaan tingkat tutur bahasa Jawa dapat berbeda sesuai usia, hubungan sosial, situasi komunikasi, dan kebiasaan keluarga.'
                    ."\n\n"
                    .'Tata krama berbahasa menjadi bagian dari nilai budaya yang menekankan kesopanan dan penghormatan kepada orang lain.'
                    ."\n\n"
                    .'Bahasa Jawa juga menjadi sarana pelestarian kebudayaan karena berbagai istilah adat, tembang, cerita rakyat, dan ungkapan tradisional memiliki makna yang berkaitan erat dengan bahasa aslinya.'
                    ."\n\n"
                    .'Ciri khas bahasa masyarakat Sleman merupakan bagian dari ragam bahasa Jawa Yogyakarta. Bahasa tersebut bukan milik eksklusif Sleman, tetapi menjadi salah satu unsur penting identitas budaya masyarakatnya.',
            ],
            [
                'name' => 'Gotong Royong Masyarakat Sleman',
                'slug' => 'gotong-royong-sleman',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kerja bersama dalam upacara adat, kerja bakti, pertanian, dan kegiatan desa.',
                'content' => 'Gotong royong merupakan nilai sosial yang masih terlihat dalam berbagai kegiatan masyarakat Sleman.'
                    ."\n\n"
                    .'Nilai ini tercermin dalam persiapan upacara adat, kerja bakti, pembangunan fasilitas umum, kegiatan pertanian, pertunjukan seni, dan penyelenggaraan kegiatan desa.'
                    ."\n\n"
                    .'Dalam tradisi seperti Saparan Bekakak dan merti desa, masyarakat bekerja sama mempersiapkan perlengkapan, makanan, tempat kegiatan, serta berbagai kebutuhan lainnya.'
                    ."\n\n"
                    .'Gotong royong memperlihatkan bahwa kebudayaan tidak hanya berupa benda atau pertunjukan, tetapi juga mencakup nilai dan kebiasaan yang membentuk hubungan sosial.'
                    ."\n\n"
                    .'Nilai ini menjadi salah satu unsur penting dalam keberlanjutan tradisi karena masyarakat berperan langsung dalam mewariskan kebiasaan kepada generasi berikutnya.',
            ],
        ];
    }
}
