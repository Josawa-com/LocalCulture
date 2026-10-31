<?php

namespace Database\Seeders;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Regency;
use Illuminate\Database\Seeder;

/**
 * Materi budaya Kota Yogyakarta, DI Yogyakarta.
 *
 * Jalankan ulang kapan saja (idempoten):
 *     php artisan db:seed --class=KotaJogjaCultureSeeder --force
 */
class KotaJogjaCultureSeeder extends Seeder
{
    public function run(): void
    {
        $regency = Regency::where('slug', 'kota-jogja')->first();

        if (! $regency) {
            $this->command?->warn('Kota Jogja tidak ditemukan. Jalankan DatabaseSeeder dulu.');

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

        $this->command?->info("Kota Jogja: {$created} materi baru, {$updated} diperbarui (total ".count($items).').');
    }

    /**
     * @return list<array{name: string, slug: string, category: string, description: string, content: string, philosophy?: ?string}>
     */
    private function items(): array
    {
        return [

            // ============================================================
            // SEJARAH & IDENTITAS
            // ============================================================
            [
                'name' => 'Sejarah Kota Yogyakarta',
                'slug' => 'sejarah-kota-yogyakarta',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Dari Perjanjian Giyanti 1755, berdirinya Kesultanan, hingga pernah menjadi ibu kota RI 1946–1949.',
                'content' => 'Sejarah Kota Yogyakarta tidak dapat dipisahkan dari perkembangan Kerajaan Mataram Islam. Pada abad ke-16, Kerajaan Mataram Islam berkembang menjadi salah satu kerajaan besar di Pulau Jawa.'
                    ."\n\n"
                    .'Pada tahun 1755, terjadi Perjanjian Giyanti yang membagi kekuasaan Mataram menjadi dua bagian utama, yaitu Kasunanan Surakarta dan Kesultanan Yogyakarta.'
                    ."\n\n"
                    .'Pangeran Mangkubumi menjadi pendiri Kesultanan Yogyakarta dengan gelar Sri Sultan Hamengku Buwono I. Pembangunan Keraton Yogyakarta dimulai pada tahun 1755 dan mulai ditempati pada tahun 1756.'
                    ."\n\n"
                    .'Keberadaan keraton kemudian menjadi pusat perkembangan wilayah yang sekarang dikenal sebagai Kota Yogyakarta. Di sekitarnya berkembang permukiman, pasar, tempat ibadah, pusat perdagangan, dan berbagai fasilitas pemerintahan.'
                    ."\n\n"
                    .'Kota Yogyakarta kemudian mengalami perkembangan pada masa kolonial Belanda, masa perjuangan kemerdekaan, hingga masa Indonesia modern. Pada masa perjuangan kemerdekaan, Yogyakarta pernah menjadi ibu kota Republik Indonesia pada tahun 1946 hingga 1949. Peristiwa tersebut menjadikan kota ini memiliki kedudukan penting dalam sejarah nasional.',
                'philosophy' => 'Kota ini lahir dari sebuah perjanjian, tetapi tumbuh menjadi pusat budaya yang melampaui batas kerajaan.',
            ],
            [
                'name' => 'Ciri Khas Budaya Kota Yogyakarta',
                'slug' => 'ciri-khas-budaya-jogja',
                'category' => 'Tradisi & Kesenian',
                'description' => '15 penanda identitas: tata krama, kesenian klasik, tradisi keraton, batik, kuliner, dan kawasan bersejarah.',
                'content' => 'Kota Yogyakarta merupakan ibu kota Daerah Istimewa Yogyakarta sekaligus salah satu pusat kebudayaan Jawa yang memiliki sejarah panjang. Kota ini dikenal sebagai Kota Budaya karena masih mempertahankan berbagai tradisi, adat istiadat, kesenian, bahasa, filosofi kehidupan, tata krama, serta peninggalan sejarah kerajaan.'
                    ."\n\n"
                    .'Kebudayaan Kota Yogyakarta tidak hanya terlihat melalui bangunan bersejarah, pakaian adat, batik, tarian, gamelan, dan upacara tradisional. Kebudayaan juga tercermin dalam perilaku masyarakat sehari-hari, seperti cara berbicara, menghormati orang yang lebih tua, menjaga kesopanan, menghargai sesama, bergotong royong, dan mempertahankan kerukunan.'
                    ."\n\n"
                    .'Ciri khas budaya Kota Yogyakarta dapat dilihat melalui berbagai aspek kehidupan masyarakat dan tradisi keraton:'
                    ."\n\n"
                    .'1. Mengutamakan unggah-ungguh dan tata krama dalam berkomunikasi.'
                    ."\n"
                    .'2. Menjunjung tinggi sikap andhap asor atau rendah hati.'
                    ."\n"
                    .'3. Memiliki kebiasaan tepa slira atau tenggang rasa.'
                    ."\n"
                    .'4. Mengutamakan kerukunan dan hubungan sosial yang harmonis.'
                    ."\n"
                    .'5. Memiliki tradisi gotong royong, sambatan, rewang, dan sinoman.'
                    ."\n"
                    .'6. Menggunakan bahasa Jawa dengan tingkatan tutur sesuai situasi.'
                    ."\n"
                    .'7. Mempertahankan kesenian klasik seperti Bedhaya, Serimpi, Wayang Kulit, dan Karawitan.'
                    ."\n"
                    .'8. Memiliki tradisi keraton seperti Sekaten, Garebeg, Labuhan, dan Siraman Pusaka.'
                    ."\n"
                    .'9. Memiliki batik dengan motif dan filosofi tertentu.'
                    ."\n"
                    .'10. Mempertahankan tata busana tradisional seperti surjan, kebaya, jarik, dan blangkon.'
                    ."\n"
                    .'11. Memiliki hubungan sejarah yang kuat dengan Keraton Ngayogyakarta Hadiningrat.'
                    ."\n"
                    .'12. Menjunjung nilai kesederhanaan, pengendalian diri, dan tanggung jawab.'
                    ."\n"
                    .'13. Memiliki filosofi kehidupan yang berkaitan dengan hubungan manusia, Tuhan, dan alam.'
                    ."\n"
                    .'14. Memiliki tradisi kuliner seperti gudeg, bakpia, yangko, kipo, dan geplak.'
                    ."\n"
                    .'15. Memiliki kawasan bersejarah seperti Keraton, Tamansari, Kotagede, dan Malioboro.',
                'philosophy' => 'Identitas Yogyakarta adalah perpaduan budaya fisik (keraton, batik, gamelan) dan nonfisik (filosofi, tata krama, bahasa).',
            ],

            // ============================================================
            // NILAI & TATA KRAMA
            // ============================================================
            [
                'name' => 'Unggah-Ungguh, Andhap Asor, dan Tepa Slira',
                'slug' => 'unggah-ungguh-andhap-asor',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tiga pilar tata krama Jawa: kesopanan, kerendahan hati, dan tenggang rasa.',
                'content' => 'Budaya masyarakat Kota Yogyakarta terbentuk melalui perpaduan tradisi Jawa, pengaruh keraton, agama, pendidikan, perdagangan, dan kehidupan perkotaan.'
                    ."\n\n"
                    .'Unggah-ungguh merupakan tata krama atau aturan kesopanan dalam berperilaku dan berkomunikasi. Masyarakat Jawa mengenal unggah-ungguh sebagai pedoman untuk menempatkan diri ketika berhadapan dengan orang lain. Contohnya adalah berbicara menggunakan bahasa yang sopan kepada orang yang lebih tua, tidak memotong pembicaraan, menghormati orang yang sedang berbicara, dan menjaga sikap ketika berada di lingkungan masyarakat.'
                    ."\n\n"
                    .'Andhap asor berarti rendah hati dan tidak menyombongkan diri. Seseorang yang memiliki sikap andhap asor tidak merasa lebih tinggi daripada orang lain meskipun memiliki kekayaan, jabatan, pendidikan, atau kemampuan tertentu. Dalam kehidupan sehari-hari, sikap ini terlihat melalui kesederhanaan, tidak merendahkan orang lain, dan tetap menghormati siapa pun.'
                    ."\n\n"
                    .'Tepa slira merupakan sikap tenggang rasa atau kemampuan memahami keadaan dan perasaan orang lain. Contohnya adalah tidak berbicara terlalu keras di tempat umum, tidak mengganggu tetangga, menghargai perbedaan pendapat, serta mempertimbangkan perasaan orang lain sebelum bertindak.'
                    ."\n\n"
                    .'Selain itu dikenal ewuh pakewuh, yaitu perasaan sungkan atau segan ketika berhadapan dengan orang lain, terutama orang yang lebih tua atau dihormati. Sikap ini dapat mendorong seseorang menjaga kesopanan dan tidak bertindak sembarangan. Namun, jika berlebihan, ewuh pakewuh juga dapat membuat seseorang kesulitan menyampaikan pendapat.'
                    ."\n\n"
                    .'Nilai-nilai tersebut tetap dapat ditemukan dalam kehidupan masyarakat perkotaan Yogyakarta: menghormati orang yang lebih tua, memberikan tempat duduk kepada orang yang membutuhkan, menjaga ketertiban di ruang publik, tidak mengganggu lingkungan sekitar, dan menghargai keberagaman masyarakat.',
                'philosophy' => 'Unggah-ungguh mengatur letak diri, andhap asor menjaga hati, tepa slira merawat sesama — tiga pilar hidup bersama.',
            ],
            [
                'name' => 'Nilai Sosial: Rukun, Gotong Royong, Sepi ing Pamrih',
                'slug' => 'nilai-sosial-jogja',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Rukun agawe santosa, gotong royong, dan "sepi ing pamrih, rame ing gawe" dalam kehidupan warga.',
                'content' => 'Ungkapan "rukun agawe santosa" memiliki makna bahwa kerukunan dapat menciptakan kekuatan dan kesejahteraan. Masyarakat diajarkan untuk menjaga hubungan baik dengan keluarga, tetangga, dan lingkungan sekitar. Perselisihan sebaiknya diselesaikan melalui komunikasi dan musyawarah agar tidak merusak hubungan sosial.'
                    ."\n\n"
                    .'Gotong royong merupakan kebiasaan bekerja bersama untuk menyelesaikan kepentingan bersama. Contohnya adalah membersihkan lingkungan, membantu tetangga yang mengadakan hajatan, memperbaiki fasilitas umum, serta membantu masyarakat yang mengalami musibah.'
                    ."\n\n"
                    .'Ungkapan "sepi ing pamrih, rame ing gawe" memiliki makna bahwa seseorang sebaiknya banyak bekerja dan memberikan manfaat tanpa selalu mengharapkan pujian atau imbalan pribadi. Nilai tersebut mengajarkan ketulusan, tanggung jawab, dan semangat membantu sesama.',
                'philosophy' => 'Bekerja tanpa menuntut pujian — sumbangan yang tulus justru yang paling menguatkan masyarakat.',
            ],
            [
                'name' => 'Kebiasaan Sosial: Sambatan, Rewang, Sinoman',
                'slug' => 'sambatan-rewang-sinoman',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tiga bentuk kerja bersama: membantu tetangga, membantu hajatan, dan melayani tamu.',
                'content' => 'Sambatan merupakan kegiatan membantu tetangga atau masyarakat yang membutuhkan bantuan. Contohnya membantu membangun rumah, memperbaiki bangunan, atau menyelesaikan pekerjaan tertentu.'
                    ."\n\n"
                    .'Rewang merupakan kegiatan membantu tetangga yang sedang mengadakan hajatan, seperti pernikahan atau acara keluarga. Orang yang membantu biasanya ikut memasak, menyiapkan tempat, melayani tamu, dan membersihkan perlengkapan.'
                    ."\n\n"
                    .'Sinoman merupakan kegiatan membantu melayani tamu dalam suatu acara. Kegiatan ini biasanya dilakukan oleh para pemuda dan pemudi di lingkungan masyarakat.'
                    ."\n\n"
                    .'Selain itu, musyawarah merupakan kebiasaan menyelesaikan persoalan bersama melalui pembicaraan dan kesepakatan. Nilai ini penting dalam menjaga kerukunan masyarakat.'
                    ."\n\n"
                    .'Menghormati orang yang lebih tua merupakan bagian dari tata krama masyarakat Jawa. Hal tersebut terlihat melalui cara berbicara, penggunaan bahasa krama, sikap tubuh, serta perilaku ketika berinteraksi.',
            ],

            // ============================================================
            // BAHASA & FILOSOFI
            // ============================================================
            [
                'name' => 'Bahasa Jawa dan Tingkat Tutur',
                'slug' => 'bahasa-jawa-jogja',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Ngoko, krama, dan krama inggil beserta contoh pemakaiannya dalam percakapan.',
                'content' => 'Bahasa Jawa merupakan bagian penting dari identitas masyarakat Kota Yogyakarta. Dalam kehidupan sehari-hari, bahasa Jawa digunakan bersama bahasa Indonesia. Penggunaan bahasa Jawa memiliki tingkatan tutur yang disesuaikan dengan hubungan antara orang yang berbicara.'
                    ."\n\n"
                    .'Ngoko merupakan tingkatan bahasa Jawa yang digunakan dalam komunikasi akrab, misalnya antara teman sebaya atau anggota keluarga yang sudah dekat. Contoh: "Aku arep lunga menyang pasar." Artinya: Saya akan pergi ke pasar.'
                    ."\n\n"
                    .'Krama merupakan tingkatan bahasa Jawa yang lebih sopan. Bahasa ini biasanya digunakan ketika berbicara dengan orang yang lebih tua, orang yang dihormati, atau seseorang yang belum terlalu akrab. Contoh: "Kula badhe tindak dhateng pasar." Artinya: Saya akan pergi ke pasar.'
                    ."\n\n"
                    .'Krama inggil merupakan bentuk kosakata penghormatan dalam bahasa Jawa yang digunakan untuk menghormati orang lain. Contohnya: dhahar (makan, bentuk penghormatan), sare (tidur), rawuh (datang atau hadir), dan panjenengan (Anda atau sebutan hormat untuk orang lain).'
                    ."\n\n"
                    .'Masyarakat Kota Yogyakarta mengenal berbagai sapaan yang mencerminkan kesopanan: Mas (laki-laki), Mbak (perempuan), Pak (laki-laki lebih tua/dihormati), Bu (perempuan lebih tua/dihormati), Simbah (kakek/nenek), dan Panjenengan (sapaan sopan). Penggunaan sapaan tersebut merupakan bagian dari kebiasaan masyarakat dalam menjaga hubungan sosial.',
                'philosophy' => 'Tingkat tutur bukan sekadar tata bahasa — ia adalah cara bahasa mengajarkan hormat.',
            ],
            [
                'name' => 'Filosofi Hidup Jawa Yogyakarta',
                'slug' => 'filosofi-hidup-jogja',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Hamemayu Hayuning Bawana, Sangkan Paraning Dumadi, Nrimo ing Pandum, dan prinsip tari klasik.',
                'content' => 'Kota Yogyakarta memiliki berbagai filosofi Jawa yang memengaruhi cara masyarakat memandang kehidupan.'
                    ."\n\n"
                    .'Hamemayu Hayuning Bawana bermakna memperindah dan menjaga kehidupan dunia. Manusia diajarkan untuk menjaga keharmonisan hubungan dengan Tuhan, sesama manusia, dan alam.'
                    ."\n\n"
                    .'Sangkan Paraning Dumadi berkaitan dengan asal-usul dan tujuan kehidupan manusia. Manusia diajak memahami bahwa kehidupan memiliki asal dan tujuan sehingga harus dijalani dengan kesadaran, tanggung jawab, dan perilaku yang baik.'
                    ."\n\n"
                    .'Nrimo ing Pandum merupakan sikap menerima keadaan atau bagian yang diperoleh dengan lapang dada. Filosofi ini bukan berarti menyerah dan tidak berusaha, tetapi mengajarkan seseorang untuk bersyukur, mengendalikan keinginan, menerima kenyataan, dan tetap berusaha memperbaiki kehidupan.'
                    ."\n\n"
                    .'Alon-alon Waton Kelakon mengajarkan pentingnya kehati-hatian dalam bertindak. Seseorang sebaiknya tidak terburu-buru mengambil keputusan dan perlu mempertimbangkan akibat dari setiap tindakannya.'
                    ."\n\n"
                    .'Manunggaling Kawula Gusti berkaitan dengan hubungan manusia dengan Tuhan serta hubungan antara pemimpin dan masyarakat dalam tradisi pemikiran Jawa. Dalam kehidupan spiritual, konsep ini menggambarkan usaha manusia untuk mendekatkan diri kepada Tuhan melalui perilaku yang baik dan pengendalian diri.'
                    ."\n\n"
                    .'Sawiji, Greget, Sengguh, Ora Mingkuh adalah empat prinsip dalam filosofi seni tari klasik Yogyakarta: sawiji (menyatukan pikiran dan berkonsentrasi), greget (semangat dan kesungguhan), sengguh (percaya diri tanpa kehilangan kerendahan hati), dan ora mingkuh (tidak menghindar dari tanggung jawab). Keempat prinsip tersebut menggambarkan karakter yang diharapkan dalam berkesenian dan menjalani kehidupan.',
                'philosophy' => 'Nrimo ing pandum bukan pasrah, melainkan syukur yang tetap disertai usaha.',
            ],

            // ============================================================
            // ADAT & TRADISI KERATON
            // ============================================================
            [
                'name' => 'Sekaten',
                'slug' => 'sekaten',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Peringatan Maulid Nabi dengan menabuh gamelan pusaka di Masjid Gedhe Kauman.',
                'content' => 'Sekaten merupakan tradisi budaya dan keagamaan Keraton Yogyakarta untuk memperingati kelahiran Nabi Muhammad SAW. Tradisi ini dilaksanakan pada bulan Maulud dalam kalender Jawa-Islam.'
                    ."\n\n"
                    .'Salah satu ciri khas Sekaten adalah dibunyikannya gamelan pusaka Keraton Yogyakarta di lingkungan Masjid Gedhe Kauman.'
                    ."\n\n"
                    .'Sekaten juga berkembang menjadi kegiatan budaya dan keramaian masyarakat yang menghadirkan pedagang, makanan tradisional, serta berbagai hiburan rakyat.',
            ],
            [
                'name' => 'Garebeg',
                'slug' => 'garebeg',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tiga Garebeg (Syawal, Besar, Mulud) dengan kirab gunungan hasil bumi dari keraton ke Masjid Gedhe.',
                'content' => 'Garebeg merupakan upacara adat Keraton Yogyakarta yang diselenggarakan dalam rangka memperingati hari-hari besar Islam. Terdapat tiga jenis Garebeg yang dikenal dalam tradisi keraton:'
                    ."\n\n"
                    .'1. Garebeg Syawal, dilaksanakan dalam rangka Idulfitri.'
                    ."\n"
                    .'2. Garebeg Besar, dilaksanakan dalam rangka Iduladha.'
                    ."\n"
                    .'3. Garebeg Mulud, dilaksanakan dalam rangka Maulid Nabi Muhammad SAW.'
                    ."\n\n"
                    .'Salah satu ciri khas Garebeg adalah keluarnya gunungan dari keraton menuju Masjid Gedhe. Gunungan berisi hasil bumi dan makanan yang disusun dengan bentuk tertentu.'
                    ."\n\n"
                    .'Tradisi ini mengandung makna simbolis mengenai kemakmuran, sedekah, rasa syukur, dan hubungan antara keraton dengan masyarakat.',
                'philosophy' => 'Gunungan adalah sedekah raja kepada rakyat — kemakmuran yang dibagikan, bukan disimpan.',
            ],
            [
                'name' => 'Labuhan Keraton',
                'slug' => 'labuhan-keraton',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Penyerahan benda keraton ke tempat bermakna seperti Parangkusumo dan Merapi, memohon keselamatan.',
                'content' => 'Labuhan merupakan upacara adat Keraton Yogyakarta yang dilakukan dengan menyerahkan benda-benda tertentu ke lokasi yang memiliki makna penting dalam tradisi keraton.'
                    ."\n\n"
                    .'Salah satu lokasi yang terkenal adalah Pantai Parangkusumo. Tradisi Labuhan juga berkaitan dengan Gunung Merapi dan tempat lain yang ditentukan oleh keraton.'
                    ."\n\n"
                    .'Upacara ini memiliki makna permohonan keselamatan, ketenteraman, dan kesejahteraan bagi Sultan, keraton, serta masyarakat.',
            ],
            [
                'name' => 'Siraman Pusaka',
                'slug' => 'siraman-pusaka',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Memandikan keris, tombak, dan perlengkapan kerajaan setiap bulan Sura sebagai penghormatan leluhur.',
                'content' => 'Siraman pusaka merupakan tradisi membersihkan atau memandikan benda-benda pusaka milik Keraton Yogyakarta. Tradisi ini biasanya dilaksanakan pada bulan Sura atau Muharam.'
                    ."\n\n"
                    .'Pusaka yang dibersihkan dapat berupa keris, tombak, dan berbagai perlengkapan kerajaan lainnya.'
                    ."\n\n"
                    .'Tradisi ini mengandung makna penghormatan terhadap warisan leluhur, sejarah, dan tanggung jawab dalam menjaga benda-benda bersejarah.',
            ],
            [
                'name' => 'Nyadran, Tirakatan, dan Kenduri',
                'slug' => 'nyadran-tirakatan-kenduri',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Ziarah leluhur, malam renungan kemerdekaan, dan doa bersama dengan berbagi makanan.',
                'content' => 'Nyadran merupakan tradisi masyarakat Jawa untuk mendoakan leluhur dan keluarga yang telah meninggal dunia. Kegiatannya dapat berupa membersihkan makam, berziarah, membaca doa, mengadakan kenduri, dan makan bersama.'
                    ."\n\n"
                    .'Tirakatan merupakan kegiatan berkumpul untuk berdoa, merenung, dan melakukan kegiatan kebersamaan. Di Kota Yogyakarta, tirakatan sering dilaksanakan pada malam peringatan Hari Kemerdekaan Indonesia.'
                    ."\n\n"
                    .'Kenduri merupakan kegiatan berkumpul untuk berdoa dan memohon keselamatan serta keberkahan. Masyarakat biasanya menyiapkan makanan yang kemudian didoakan dan dibagikan kepada peserta.',
            ],
            [
                'name' => 'Tradisi Pernikahan Jawa',
                'slug' => 'pernikahan-jawa-jogja',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Siraman, midodareni, ijab kabul, panggih, balangan gantal, dan sungkeman beserta maknanya.',
                'content' => 'Masyarakat Kota Yogyakarta masih mengenal berbagai rangkaian pernikahan adat Jawa, seperti siraman, midodareni, ijab kabul, panggih, balangan gantal, dan sungkeman.'
                    ."\n\n"
                    .'Setiap rangkaian memiliki makna simbolis yang berkaitan dengan penyucian diri, penghormatan kepada orang tua, tanggung jawab, dan harapan kehidupan rumah tangga yang harmonis.',
                'philosophy' => 'Setiap tahap pernikahan Jawa adalah doa yang diucapkan lewat tindakan, bukan kata.',
            ],

            // ============================================================
            // KERATON
            // ============================================================
            [
                'name' => 'Keraton Ngayogyakarta Hadiningrat',
                'slug' => 'keraton-yogyakarta',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Didirikan HB I tahun 1755, pusat kebudayaan Jawa: bangsal, museum, pusaka, dan gamelan.',
                'content' => 'Keraton Ngayogyakarta Hadiningrat merupakan pusat kebudayaan dan sejarah Kesultanan Yogyakarta. Keraton didirikan oleh Sri Sultan Hamengku Buwono I pada tahun 1755 dan mulai ditempati pada tahun 1756.'
                    ."\n\n"
                    .'Keraton tidak hanya berfungsi sebagai tempat tinggal Sultan dan keluarga kerajaan, tetapi juga sebagai pusat pelestarian budaya Jawa. Di dalam keraton terdapat bangunan tradisional, bangsal, halaman, museum, koleksi pusaka, perangkat gamelan, dan berbagai perlengkapan upacara adat.'
                    ."\n\n"
                    .'Sultan merupakan gelar raja yang memimpin Kesultanan Yogyakarta. Dalam tradisi keraton, Sultan memiliki kedudukan penting dalam kehidupan kerajaan dan pelestarian kebudayaan. Sultan juga memiliki peran dalam berbagai upacara adat dan kegiatan kebudayaan yang masih dilaksanakan hingga sekarang.'
                    ."\n\n"
                    .'Keluarga keraton merupakan bagian dari lingkungan kehidupan istana. Dalam sejarahnya, keluarga Sultan memiliki hubungan dengan pelaksanaan tradisi, kegiatan kebudayaan, dan berbagai upacara kerajaan.'
                    ."\n\n"
                    .'Keraton Yogyakarta juga memiliki berbagai benda pusaka yang dianggap memiliki nilai sejarah dan budaya tinggi: keris, tombak, gamelan pusaka, kereta kerajaan, perlengkapan upacara, dan berbagai benda peninggalan sejarah kerajaan. Pusaka-pusaka tersebut dirawat sebagai warisan sejarah dan identitas Keraton Yogyakarta.',
                'philosophy' => 'Keraton bukan sekadar bangunan — ia adalah pusat yang menjaga denyut kebudayaan Jawa tetap berdenyut.',
            ],
            [
                'name' => 'Abdi Dalem dan Tata Krama Keraton',
                'slug' => 'abdi-dalem',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Pengabdian abdi dalem: merawat bangunan, pusaka, upacara, dengan tata krama dan busana khusus.',
                'content' => 'Abdi dalem merupakan orang-orang yang mengabdikan diri kepada Keraton Yogyakarta. Tugas mereka beragam, mulai dari membantu pelaksanaan upacara adat, merawat bangunan, menjaga benda pusaka, mengurus administrasi, hingga menjalankan tugas kebudayaan.'
                    ."\n\n"
                    .'Abdi dalem memiliki tata cara berpakaian, aturan, dan tata krama tertentu dalam lingkungan keraton. Pengabdian abdi dalem mengandung nilai kesetiaan, tanggung jawab, penghormatan, kesederhanaan, dan pelestarian tradisi.'
                    ."\n\n"
                    .'Lingkungan keraton memiliki tata krama yang sangat diperhatikan. Penggunaan bahasa yang sopan, sikap tubuh, cara berjalan, cara berbicara, serta penghormatan kepada Sultan dan keluarga keraton merupakan bagian dari aturan kehidupan istana. Tata krama tersebut mencerminkan nilai penghormatan dan struktur sosial dalam tradisi kerajaan Jawa.'
                    ."\n\n"
                    .'Kehidupan abdi dalem berkaitan erat dengan pengabdian kepada keraton. Mereka menjalankan tugas berdasarkan aturan dan tata cara yang telah diwariskan. Dalam lingkungan keraton, pengabdian tidak hanya dipahami sebagai pekerjaan, tetapi juga sebagai bentuk tanggung jawab untuk menjaga warisan budaya.'
                    ."\n\n"
                    .'Bahasa Jawa dengan tingkatan tutur yang sopan memiliki kedudukan penting dalam kehidupan keraton. Penggunaan bahasa disesuaikan dengan lawan bicara, kedudukan, dan situasi. Tata bahasa dan pilihan kata menjadi bagian dari penghormatan terhadap orang lain.',
                'philosophy' => 'Ngabdi bukan bekerja untuk upah, melainkan menjaga warisan agar tidak putus.',
            ],
            [
                'name' => 'Sumbu Filosofis dan Tata Ruang Kota',
                'slug' => 'sumbu-filosofis',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Garis Merapi–Tugu–Keraton–Alun-Alun Selatan–Parangkusumo sebagai perjalanan hidup manusia.',
                'content' => 'Tata ruang Kota Yogyakarta memiliki konsep filosofis yang berkaitan dengan hubungan manusia, alam, dan Tuhan.'
                    ."\n\n"
                    .'Salah satu konsep penting adalah Sumbu Filosofis Yogyakarta yang menghubungkan Gunung Merapi di utara, Keraton Yogyakarta di bagian tengah, dan Laut Selatan di bagian selatan. Konsep ini memiliki makna simbolis mengenai perjalanan kehidupan manusia dan hubungan antara alam dengan kehidupan spiritual.'
                    ."\n\n"
                    .'Tugu Pal Putih merupakan salah satu simbol utama Kota Yogyakarta. Tugu ini berada di bagian utara pusat kota dan menjadi bagian dari Sumbu Filosofis Yogyakarta.'
                    ."\n\n"
                    .'Malioboro merupakan kawasan jalan bersejarah yang memiliki hubungan dengan perkembangan Kota Yogyakarta. Kawasan ini dikenal sebagai pusat perdagangan, wisata, kuliner, dan aktivitas masyarakat.'
                    ."\n\n"
                    .'Keraton menjadi pusat dari tata ruang filosofis Yogyakarta, dengan makna penting sebagai pusat kebudayaan, sejarah, dan pemerintahan Kesultanan.'
                    ."\n\n"
                    .'Alun-Alun Utara terletak di sebelah utara Keraton. Tempat ini dahulu digunakan untuk berbagai kegiatan kerajaan dan kegiatan masyarakat, termasuk upacara adat dan perayaan tertentu.'
                    ."\n\n"
                    .'Alun-Alun Selatan merupakan ruang terbuka di bagian selatan keraton. Kawasan ini terkenal dengan dua pohon beringin di bagian tengahnya. Tradisi berjalan melewati celah kedua pohon tersebut dikenal dengan sebutan Masangin.'
                    ."\n\n"
                    .'Panggung Krapyak berada di bagian selatan Keraton Yogyakarta. Bangunan ini merupakan salah satu bagian dari Sumbu Filosofis dan memiliki hubungan simbolis dengan perjalanan kehidupan manusia.',
                'philosophy' => 'Sumbu Merapi–Keraton–Laut adalah peta kehidupan: dari gunung tempat asal, melalui manusia, menuju laut tempat kembali.',
            ],

            // ============================================================
            // KESENIAN
            // ============================================================
            [
                'name' => 'Tari Bedhaya',
                'slug' => 'tari-bedhaya',
                'category' => 'Tarian Tradisional',
                'description' => 'Tarian sakral keraton dengan gerak halus dan penuh penghayatan filosofis.',
                'content' => 'Tari Bedhaya merupakan salah satu tarian klasik yang memiliki kedudukan penting dalam tradisi Keraton Yogyakarta.'
                    ."\n\n"
                    .'Tarian ini dikenal dengan gerakan yang halus, teratur, penuh penghayatan, dan memiliki makna filosofis.',
            ],
            [
                'name' => 'Tari Serimpi',
                'slug' => 'tari-serimpi',
                'category' => 'Tarian Tradisional',
                'description' => 'Tarian klasik lembut dan anggun dengan gerak teratur serta ekspresi terkendali.',
                'content' => 'Tari Serimpi merupakan tarian klasik Jawa yang dikenal dengan gerakan lembut dan anggun.'
                    ."\n\n"
                    .'Tarian ini memiliki ciri khas gerakan yang teratur, ekspresi terkendali, dan penggunaan busana tradisional.',
            ],
            [
                'name' => 'Tari Golek',
                'slug' => 'tari-golek',
                'category' => 'Tarian Tradisional',
                'description' => 'Tarian klasik Yogyakarta yang menggambarkan karakter perempuan dengan gerak halus.',
                'content' => 'Tari Golek merupakan salah satu jenis tari klasik Yogyakarta.'
                    ."\n\n"
                    .'Tarian ini menggambarkan karakter perempuan dengan gerakan yang halus dan ekspresif.',
            ],
            [
                'name' => 'Wayang Kulit dan Wayang Wong',
                'slug' => 'wayang-jogja',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Wayang kulit oleh dalang, serta wayang wong yang diperankan manusia dengan kostum pewayangan.',
                'content' => 'Wayang Kulit merupakan seni pertunjukan menggunakan boneka pipih dari kulit yang dimainkan oleh seorang dalang. Cerita yang dibawakan umumnya berasal dari kisah Ramayana, Mahabharata, dan cerita tradisional Jawa.'
                    ."\n\n"
                    .'Wayang Wong merupakan pertunjukan drama tari yang dimainkan oleh manusia dengan menggunakan kostum dan karakter tokoh pewayangan. Pertunjukan ini memadukan seni tari, dialog, musik gamelan, dan cerita tradisional.',
            ],
            [
                'name' => 'Karawitan dan Macapat',
                'slug' => 'karawitan-macapat',
                'category' => 'Alat Musik Tradisional',
                'description' => 'Gamelan lengkap (gong, kendang, saron, bonang, gender) dan tembang Macapat berisi nasihat.',
                'content' => 'Karawitan merupakan seni musik tradisional Jawa yang menggunakan seperangkat gamelan. Instrumennya meliputi gong, kendang, saron, bonang, kenong, gender, dan berbagai alat musik lainnya.'
                    ."\n\n"
                    .'Macapat merupakan bentuk tembang atau puisi tradisional Jawa yang memiliki aturan jumlah suku kata dan bunyi akhir tertentu. Tembang Macapat mengandung berbagai nasihat mengenai kehidupan, pendidikan, hubungan manusia, dan nilai moral.',
                'philosophy' => 'Macapat mengikat kata dengan aturan guru gatra dan guru lagu — kebebasan yang tetap berdisiplin.',
            ],
            [
                'name' => 'Ketoprak',
                'slug' => 'ketoprak-jogja',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Drama tradisional dengan dialog, gamelan, dan cerita sejarah atau kehidupan masyarakat.',
                'content' => 'Ketoprak merupakan seni pertunjukan tradisional yang menggabungkan drama, dialog, musik gamelan, dan cerita kehidupan masyarakat Jawa.'
                    ."\n\n"
                    .'Cerita yang ditampilkan dapat mengambil latar sejarah kerajaan, legenda, maupun kehidupan sehari-hari.',
            ],
            [
                'name' => 'Jemparingan',
                'slug' => 'jemparingan-jogja',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Panahan tradisional dengan posisi duduk bersila, menekankan ketenangan batin.',
                'content' => 'Jemparingan merupakan seni panahan tradisional yang memiliki hubungan erat dengan budaya Keraton Yogyakarta.'
                    ."\n\n"
                    .'Ciri khas jemparingan adalah posisi memanah yang dilakukan dengan duduk bersila.'
                    ."\n\n"
                    .'Jemparingan tidak hanya menekankan ketepatan mengenai sasaran, tetapi juga pengendalian diri, konsentrasi, kesabaran, dan ketenangan batin.',
                'philosophy' => 'Memanah sambil duduk bersila: yang dilatih bukan otot, melainkan ketenangan.',
            ],

            // ============================================================
            // BATIK & PAKAIAN
            // ============================================================
            [
                'name' => 'Batik dan Motif Khas Yogyakarta',
                'slug' => 'batik-jogja',
                'category' => 'Pakaian Adat',
                'description' => 'Parang, Kawung, Ceplok, Truntum, Sido Mukti — masing-masing dengan makna filosofis.',
                'content' => 'Batik merupakan salah satu warisan budaya penting di Kota Yogyakarta. Batik tradisional Yogyakarta memiliki berbagai motif yang mengandung makna filosofis.'
                    ."\n\n"
                    .'Motif Parang memiliki bentuk garis diagonal yang menyerupai gelombang. Motif ini sering dikaitkan dengan semangat, keberanian, keteguhan, dan perjuangan.'
                    ."\n\n"
                    .'Motif Kawung memiliki bentuk menyerupai irisan buah aren atau kolang-kaling yang tersusun secara teratur. Motif ini sering dimaknai sebagai simbol kesucian, pengendalian diri, dan keseimbangan.'
                    ."\n\n"
                    .'Motif Ceplok memiliki susunan bentuk geometris yang teratur, menggambarkan keteraturan dan keselarasan.'
                    ."\n\n"
                    .'Motif Truntum memiliki bentuk bunga kecil yang tersusun berulang. Motif ini sering dikaitkan dengan cinta yang tumbuh kembali, kesetiaan, dan kasih sayang.'
                    ."\n\n"
                    .'Motif Sido Mukti sering digunakan dalam busana pernikahan adat Jawa. Motif ini mengandung harapan akan kehidupan yang bahagia dan sejahtera.'
                    ."\n\n"
                    .'Dalam lingkungan keraton, penggunaan motif batik tertentu dapat memiliki aturan tersendiri sesuai tradisi dan kedudukan pemakainya.',
                'philosophy' => 'Kawung dari irisan kolang-kaling: keteraturan alam yang mengajarkan pengendalian diri.',
            ],
            [
                'name' => 'Pakaian Adat Yogyakarta',
                'slug' => 'pakaian-adat-jogja',
                'category' => 'Pakaian Adat',
                'description' => 'Surjan, kebaya, jarik, blangkon (dengan mondolan), beskap, dan Paes Ageng pengantin.',
                'content' => 'Pakaian adat Yogyakarta memiliki ciri khas berupa bentuk, warna, motif, dan perlengkapan yang mencerminkan kebudayaan Jawa.'
                    ."\n\n"
                    .'Surjan merupakan pakaian tradisional laki-laki Jawa yang memiliki bentuk menyerupai baju panjang dengan kerah khas. Surjan sering dipadukan dengan jarik dan blangkon.'
                    ."\n\n"
                    .'Kebaya merupakan pakaian tradisional perempuan yang berkembang di Yogyakarta. Kebaya biasanya dipadukan dengan kain jarik bermotif batik.'
                    ."\n\n"
                    .'Jarik merupakan kain panjang bermotif batik yang digunakan sebagai bawahan. Penggunaannya dalam busana tradisional memiliki aturan tertentu, terutama dalam lingkungan keraton.'
                    ."\n\n"
                    .'Blangkon merupakan penutup kepala tradisional laki-laki Jawa. Blangkon Yogyakarta memiliki bentuk khas pada bagian belakang yang dikenal dengan mondolan.'
                    ."\n\n"
                    .'Beskap merupakan pakaian tradisional laki-laki yang biasanya digunakan dalam kegiatan resmi dan upacara adat tertentu.'
                    ."\n\n"
                    .'Paes Ageng merupakan salah satu tata rias dan busana pengantin tradisional Yogyakarta. Busana ini dikenal dengan hiasan kepala, tata rias wajah, kain batik, serta berbagai perlengkapan tradisional yang memiliki makna simbolis.',
            ],

            // ============================================================
            // KULINER
            // ============================================================
            [
                'name' => 'Gudeg',
                'slug' => 'gudeg',
                'category' => 'Kuliner Khas',
                'description' => 'Nangka muda dimasak santan berjam-jam; disajikan dengan areh, ayam, telur, dan krecek.',
                'content' => 'Gudeg merupakan makanan khas Yogyakarta yang terbuat dari nangka muda yang dimasak bersama santan dan berbagai bumbu dalam waktu cukup lama.'
                    ."\n\n"
                    .'Gudeg biasanya disajikan bersama nasi, ayam, telur, tahu, tempe, dan sambal krecek. Cita rasanya cenderung manis dan menjadi salah satu identitas kuliner Yogyakarta.',
                'philosophy' => 'Gudeg butuh berjam-jam dimasak — kesabaran yang menghasilkan rasa manis yang khas.',
            ],
            [
                'name' => 'Bakpia',
                'slug' => 'bakpia',
                'category' => 'Kuliner Khas',
                'description' => 'Kue bundar berkulit tipis, awalnya isi kacang hijau, kini beragam rasa.',
                'content' => 'Bakpia merupakan makanan berbentuk bulat dengan kulit tipis dan isian tertentu. Bakpia awalnya dikenal dengan isian kacang hijau, tetapi sekarang memiliki berbagai variasi rasa.',
            ],
            [
                'name' => 'Yangko',
                'slug' => 'yangko-jogja',
                'category' => 'Kuliner Khas',
                'description' => 'Kue ketan kenyal berisi kacang, dibalut tepung agar tidak lengket.',
                'content' => 'Yangko merupakan makanan tradisional yang terbuat dari tepung ketan dengan tekstur kenyal. Makanan ini biasanya memiliki isian kacang dan dibalut tepung agar tidak lengket.',
            ],
            [
                'name' => 'Kipo',
                'slug' => 'kipo',
                'category' => 'Kuliner Khas',
                'description' => 'Jajanan kecil khas Kotagede dari ketan isi kelapa dan gula merah, dibakar sebelum disajikan.',
                'content' => 'Kipo merupakan makanan tradisional khas Kotagede yang terbuat dari tepung ketan dengan isian kelapa dan gula merah. Makanan ini memiliki ukuran kecil dan biasanya dibakar sebelum disajikan.',
            ],
            [
                'name' => 'Kopi Joss',
                'slug' => 'kopi-joss',
                'category' => 'Kuliner Khas',
                'description' => 'Kopi dengan arang panas dimasukkan ke gelas, khas kawasan Stasiun Tugu.',
                'content' => 'Kopi Joss merupakan minuman kopi yang disajikan dengan memasukkan arang panas ke dalam gelas kopi. Minuman ini dikenal sebagai salah satu kuliner khas kawasan sekitar Stasiun Tugu Yogyakarta.',
            ],
            [
                'name' => 'Brongkos',
                'slug' => 'brongkos',
                'category' => 'Kuliner Khas',
                'description' => 'Masakan daging dan kacang tolo dengan kuah gelap berempah.',
                'content' => 'Brongkos merupakan masakan tradisional berbahan dasar daging, kacang tolo, dan bumbu rempah yang dimasak menggunakan kuah berwarna gelap.',
            ],

            // ============================================================
            // TEMPAT BERSEJARAH
            // ============================================================
            [
                'name' => 'Tamansari',
                'slug' => 'tamansari',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kompleks kolam dan lorong bawah tanah, dahulu tempat peristirahatan keluarga Sultan.',
                'content' => 'Tamansari merupakan kompleks bangunan bersejarah yang dahulu digunakan sebagai tempat peristirahatan dan rekreasi keluarga Sultan.'
                    ."\n\n"
                    .'Kawasan ini memiliki kolam, bangunan, lorong, dan arsitektur yang memperlihatkan perkembangan seni bangunan pada masa Kesultanan Yogyakarta.',
            ],
            [
                'name' => 'Masjid Gedhe Kauman',
                'slug' => 'masjid-gedhe-kauman',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Masjid kerajaan, pusat kegiatan keagamaan dan sejarah Islam di Yogyakarta.',
                'content' => 'Masjid Gedhe Kauman merupakan masjid bersejarah yang memiliki hubungan erat dengan Keraton Yogyakarta.'
                    ."\n\n"
                    .'Masjid ini menjadi pusat kegiatan keagamaan dan memiliki peranan penting dalam sejarah perkembangan Islam di Yogyakarta.',
            ],
            [
                'name' => 'Kotagede',
                'slug' => 'kotagede',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kawasan awal Mataram Islam: makam kerajaan, masjid, rumah tradisional, dan kerajinan perak.',
                'content' => 'Kotagede merupakan kawasan bersejarah yang berkaitan dengan perkembangan awal Kerajaan Mataram Islam.'
                    ."\n\n"
                    .'Kawasan ini memiliki peninggalan berupa makam kerajaan, masjid, rumah tradisional, dan berbagai bangunan bersejarah.',
            ],
            [
                'name' => 'Museum Sonobudoyo',
                'slug' => 'museum-sonobudoyo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Museum kebudayaan Jawa: wayang, keris, batik, dan benda arkeologi.',
                'content' => 'Museum Sonobudoyo merupakan museum kebudayaan yang menyimpan berbagai koleksi sejarah dan budaya Jawa.'
                    ."\n\n"
                    .'Koleksinya meliputi wayang, keris, batik, benda arkeologi, senjata tradisional, dan berbagai benda bersejarah lainnya.',
            ],
            [
                'name' => 'Benteng Vredeburg',
                'slug' => 'benteng-vredeburg',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Benteng kolonial yang kini menjadi museum sejarah perjuangan Indonesia.',
                'content' => 'Benteng Vredeburg merupakan bangunan bersejarah peninggalan masa kolonial yang sekarang menjadi museum sejarah perjuangan Indonesia.',
            ],
            [
                'name' => 'Pasar Beringharjo dan Malioboro',
                'slug' => 'beringharjo-malioboro',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Pusat dagang batik dan rempah, serta jalan legendaris identitas Kota Yogyakarta.',
                'content' => 'Pasar Beringharjo merupakan pasar tradisional yang memiliki hubungan erat dengan sejarah perkembangan perdagangan Kota Yogyakarta. Pasar ini dikenal sebagai tempat perdagangan batik, pakaian tradisional, makanan, rempah-rempah, dan berbagai produk lokal.'
                    ."\n\n"
                    .'Malioboro merupakan kawasan jalan bersejarah yang menjadi salah satu identitas Kota Yogyakarta. Kawasan ini dikenal dengan aktivitas perdagangan, wisata, kuliner, kesenian jalanan, dan interaksi masyarakat dari berbagai daerah.',
            ],
        ];
    }
}
