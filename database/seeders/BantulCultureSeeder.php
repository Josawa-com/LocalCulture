<?php

namespace Database\Seeders;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Regency;
use Illuminate\Database\Seeder;

/**
 * Materi budaya Kabupaten Bantul, DI Yogyakarta.
 *
 * Jalankan ulang kapan saja (idempoten, memakai updateOrCreate):
 *     php artisan db:seed --class=BantulCultureSeeder --force
 */
class BantulCultureSeeder extends Seeder
{
    public function run(): void
    {
        $regency = Regency::where('slug', 'bantul')->first();

        if (! $regency) {
            $this->command?->warn('Kabupaten Bantul tidak ditemukan. Jalankan DatabaseSeeder dulu.');

            return;
        }

        $categories = CultureCategory::pluck('id', 'name');

        $created = 0;
        $updated = 0;

        foreach ($this->items() as $item) {
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

        $this->command?->info("Bantul: {$created} materi baru, {$updated} diperbarui (total ".count($this->items()).').');
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
                'name' => 'Sejarah dan Identitas Budaya Bantul',
                'slug' => 'sejarah-budaya-bantul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Perjalanan budaya Bantul: dari masa kerajaan Jawa, Mataram Islam, perjuangan Diponegoro, hingga terbentuknya kabupaten.',
                'content' => 'Kabupaten Bantul merupakan salah satu kabupaten di Daerah Istimewa Yogyakarta yang memiliki kekayaan budaya berupa tradisi masyarakat, peninggalan sejarah kerajaan, kesenian tradisional, kerajinan, kuliner, serta kebiasaan hidup masyarakat agraris dan pesisir.'
                    ."\n\n"
                    .'Kebudayaan Bantul berkembang melalui perjalanan sejarah yang panjang, terutama karena wilayahnya memiliki hubungan erat dengan Kesultanan Yogyakarta, sejarah perjuangan melawan penjajahan, perkembangan Islam, serta kehidupan masyarakat pedesaan.'
                    ."\n\n"
                    .'Identitas budaya Bantul dibentuk oleh beberapa unsur utama. Pertama, pengaruh kuat kebudayaan Jawa Yogyakarta terutama dalam bahasa, seni pertunjukan, tata upacara, karawitan, wayang kulit, serta nilai kesopanan dan gotong royong.'
                    ."\n\n"
                    .'Kedua, tradisi agraris yang berkaitan dengan pertanian, hasil panen, kesuburan tanah, dan rasa syukur masyarakat — salah satu contohnya Kupatan Jolosutro.'
                    ."\n\n"
                    .'Ketiga, kebudayaan pesisir selatan yang berkaitan dengan laut, kehidupan nelayan, keselamatan melaut, serta ungkapan syukur atas hasil laut seperti Pisungsung Jaladri dan sedekah laut.'
                    ."\n\n"
                    .'Keempat, sentra kerajinan yang dikenal luas: gerabah Kasongan, kerajinan kulit Manding, batik Giriloyo, dan tatah sungging wayang kulit Wukirsari.'
                    ."\n\n"
                    .'Kelima, kuliner yang berakar pada sejarah dan keterampilan masyarakat setempat, seperti Mie Lethek dari Srandakan dan Sate Klathak dari Jejeran di Pleret.'
                    ."\n\n"
                    .'Keenam, peninggalan sejarah dan tradisi keagamaan di kawasan Imogiri, Pleret, Selarong, dan Parangkusumo.'
                    ."\n\n"
                    .'Meskipun beberapa kesenian dan tradisinya juga ditemukan di daerah lain di Jawa, setiap wilayah memiliki bentuk pelaksanaan, sejarah lokal, tokoh pelestari, dan ciri khasnya sendiri.',
                'philosophy' => 'Budaya Bantul tidak dapat dijelaskan lewat satu tradisi saja; kekhasannya justru terletak pada keberagaman budaya di berbagai wilayahnya.',
            ],
            [
                'name' => 'Masa Kerajaan Mataram Islam dan Kawasan Pleret',
                'slug' => 'mataram-islam-pleret',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Pleret menyimpan jejak pusat pemerintahan Mataram Islam pada masa Sultan Agung dan penerusnya.',
                'content' => 'Sebelum Kabupaten Bantul terbentuk secara administratif, kawasan yang sekarang termasuk wilayah Bantul telah menjadi bagian dari lingkungan sejarah dan kebudayaan kerajaan-kerajaan Jawa. Wilayah ini berada dalam kawasan budaya Yogyakarta bagian selatan yang memiliki hubungan dengan perkembangan Mataram Islam dan kemudian Kesultanan Yogyakarta.'
                    ."\n\n"
                    .'Pengaruh kebudayaan kerajaan dapat dilihat dari berkembangnya bahasa Jawa dengan tingkat tutur, tata kehidupan masyarakat, seni pertunjukan, gamelan, wayang kulit, tradisi penghormatan kepada leluhur, serta berbagai upacara adat. Pengaruh tersebut tidak hanya berkembang di lingkungan keraton, tetapi juga menyebar ke desa-desa melalui hubungan antara penguasa, tokoh agama, seniman, petani, dan masyarakat umum.'
                    ."\n\n"
                    .'Salah satu kawasan penting dalam sejarah kerajaan di Bantul adalah Pleret. Kawasan ini memiliki peninggalan yang berkaitan dengan sejarah kerajaan Mataram Islam, termasuk jejak pusat pemerintahan pada masa Sultan Agung dan penerusnya.'
                    ."\n\n"
                    .'Keberadaan peninggalan sejarah tersebut memperlihatkan bahwa wilayah Bantul bukan sekadar kawasan pertanian dan permukiman, melainkan juga bagian dari lanskap politik dan kebudayaan kerajaan Jawa. Warisan masa kerajaan masih dapat ditemukan melalui situs sejarah, tradisi yang berhubungan dengan makam raja, seni pertunjukan tradisional, serta tata upacara masyarakat yang mendapat pengaruh dari kebudayaan Jawa dan lingkungan keraton.',
            ],
            [
                'name' => 'Perjuangan Pangeran Diponegoro dan Gua Selarong',
                'slug' => 'gua-selarong',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Situs sejarah Perang Jawa 1825–1830 di Pajangan, saksi perjuangan Pangeran Diponegoro melawan Belanda.',
                'content' => 'Salah satu bagian paling penting dalam sejarah Bantul adalah keterkaitannya dengan Perang Jawa atau Perang Diponegoro yang berlangsung pada 1825–1830.'
                    ."\n\n"
                    .'Pangeran Diponegoro memimpin perlawanan terhadap pemerintah kolonial Belanda. Dalam perjuangan tersebut, kawasan Selarong di wilayah yang sekarang termasuk Kapanewon Pajangan menjadi salah satu pusat penting pergerakan pasukannya. Gua Selarong kemudian dikenal sebagai situs sejarah perjuangan Pangeran Diponegoro.'
                    ."\n\n"
                    .'Kawasan ini menjadi pengingat mengenai perlawanan masyarakat Jawa terhadap kekuasaan kolonial dan memiliki kedudukan penting dalam ingatan sejarah masyarakat Bantul. Peristiwa tersebut memengaruhi pembentukan identitas sejarah Bantul — daerah ini tidak hanya dikenal karena kebudayaan tradisionalnya, tetapi juga karena hubungannya dengan perjuangan kemerdekaan dan perlawanan terhadap penjajahan.'
                    ."\n\n"
                    .'Dalam konteks budaya, sejarah perjuangan tersebut dapat dipelajari melalui cerita sejarah lokal, kunjungan ke situs Gua Selarong, kegiatan pendidikan sejarah, serta pelestarian peninggalan yang berkaitan dengan perjuangan Diponegoro. Situs ini memperkuat identitas Bantul sebagai wilayah yang memiliki warisan sejarah perjuangan.',
                'philosophy' => 'Gua Selarong mengingatkan bahwa kebudayaan juga lahir dari keberanian mempertahankan tanah dan martabat.',
            ],
            [
                'name' => 'Pembentukan Kabupaten Bantul (1831)',
                'slug' => 'pembentukan-kabupaten-bantul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Pada 20 Juli 1831 Bantulkarang resmi menjadi Kabupaten Bantul; tanggal itu kini diperingati sebagai hari jadi.',
                'content' => 'Setelah berakhirnya Perang Diponegoro, pemerintahan kolonial Belanda melakukan penataan wilayah administratif di lingkungan Kasultanan Yogyakarta.'
                    ."\n\n"
                    .'Pada 20 Juli 1831, wilayah yang sebelumnya dikenal sebagai Bantulkarang secara resmi ditetapkan menjadi Kabupaten Bantul. Pembentukan tersebut berlangsung pada masa pemerintahan Sultan Hamengku Buwono V. Raden Tumenggung Mangun Negoro kemudian dipercaya menjadi bupati pertama. Tanggal 20 Juli diperingati sebagai hari jadi Kabupaten Bantul.'
                    ."\n\n"
                    .'Pembentukan kabupaten menjadi bagian penting dari sejarah pemerintahan daerah ini. Sejak saat itu, perkembangan masyarakat Bantul berlangsung melalui hubungan antara pemerintahan, pertanian, perdagangan, kehidupan keagamaan, pendidikan, kesenian, dan kegiatan ekonomi masyarakat.'
                    ."\n\n"
                    .'Seiring waktu, kebudayaan Bantul berkembang dengan ciri berupa perpaduan antara kebudayaan Jawa Yogyakarta, tradisi pedesaan, warisan sejarah kerajaan, kehidupan masyarakat pesisir selatan, dan keterampilan kerajinan.',
            ],

            // ============================================================
            // TRADISI & UPACARA
            // ============================================================
            [
                'name' => 'Kupatan Jolosutro',
                'slug' => 'kupatan-jolosutro',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tradisi unggulan masyarakat Jolosutro, Srimulyo, Piyungan: syukuran panen dengan ketupat raksasa.',
                'content' => 'Kupatan Jolosutro merupakan salah satu tradisi yang memiliki hubungan kuat dengan identitas budaya Kabupaten Bantul. Tradisi ini dilaksanakan oleh masyarakat Jolosutro, Kalurahan Srimulyo, Kapanewon Piyungan.'
                    ."\n\n"
                    .'Tradisi tersebut dikenal sebagai ungkapan rasa syukur masyarakat kepada Tuhan atas hasil pertanian dan berkah yang diterima. Pelaksanaannya berkaitan dengan kehidupan masyarakat agraris yang menggantungkan penghidupan pada pertanian.'
                    ."\n\n"
                    .'Dalam sejarah lisan masyarakat setempat, tradisi Rasulan atau Kupatan Jolosutro dikaitkan dengan masa Sunan Geseng. Kisah tersebut menjadi bagian dari ingatan budaya masyarakat, meskipun rincian sejarah awalnya perlu dibedakan antara tradisi tutur dan bukti sejarah tertulis.'
                    ."\n\n"
                    .'Salah satu ciri yang menarik adalah ketupat yang digunakan dalam upacara ini, yang ukurannya dapat lebih besar daripada ketupat biasa. Selain ketupat, masyarakat menyiapkan berbagai hidangan dan perlengkapan upacara.'
                    ."\n\n"
                    .'Dalam pelaksanaannya, masyarakat dari lingkungan setempat menyiapkan perlengkapan bersama. Hidangan dapat ditempatkan dalam jodhang atau wadah yang dibawa dalam rangkaian kegiatan tradisi. Setelah itu, masyarakat mengikuti rangkaian upacara dan kegiatan bersama sesuai kebiasaan setempat.'
                    ."\n\n"
                    .'Kupatan Jolosutro juga memperlihatkan pentingnya gotong royong. Persiapan kegiatan melibatkan masyarakat dan lingkungan setempat sehingga tradisi ini bukan hanya kegiatan seremonial, melainkan juga sarana memperkuat hubungan sosial. Tradisi ini telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia.',
                'philosophy' => 'Ketupat sebagai simbol syukur; kebersamaan warga menjadi jiwa dari tradisi ini.',
            ],
            [
                'name' => 'Nguras Enceh Imogiri',
                'slug' => 'nguras-enceh',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Menguras dan membersihkan empat enceh pusaka di kompleks Makam Raja-raja Imogiri setiap bulan Sura.',
                'content' => 'Nguras Enceh merupakan tradisi yang berkaitan dengan kompleks makam raja-raja di Imogiri. Kawasan ini memiliki kedudukan penting dalam sejarah Kesultanan Yogyakarta dan Kasunanan Surakarta.'
                    ."\n\n"
                    .'Tradisi Nguras Enceh dilaksanakan dengan membersihkan atau menguras air dari empat enceh, yaitu gentong besar yang berada di lingkungan makam. Kegiatan ini dilaksanakan pada bulan Sura dalam penanggalan Jawa, dengan hari pelaksanaan mengikuti ketentuan tradisi setempat.'
                    ."\n\n"
                    .'Enceh tersebut merupakan benda pusaka yang memiliki hubungan dengan sejarah kompleks pemakaman kerajaan. Pelaksanaan tradisi melibatkan abdi dalem dan pihak yang bertanggung jawab terhadap pemeliharaan kawasan makam.'
                    ."\n\n"
                    .'Setelah air dikeluarkan, enceh dibersihkan dan diisi kembali. Air yang telah diambil kemudian dibagikan atau diambil oleh masyarakat yang datang. Sebagian masyarakat mempercayai air tersebut memiliki nilai keberkahan, yang merupakan bagian dari kepercayaan dan praktik budaya setempat.'
                    ."\n\n"
                    .'Makna budaya Nguras Enceh berkaitan dengan penghormatan terhadap leluhur, kesinambungan tradisi kerajaan, serta hubungan antara masyarakat dengan warisan sejarahnya. Tradisi tersebut memperlihatkan bahwa warisan budaya Bantul tidak hanya berasal dari kehidupan desa, tetapi juga dari lingkungan kerajaan. Nguras Enceh telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia.',
            ],
            [
                'name' => 'Labuhan Parangkusumo',
                'slug' => 'labuhan-parangkusumo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Upacara adat Keraton Yogyakarta di Pantai Parangkusumo, Kretek, dalam rangkaian peringatan kenaikan takhta.',
                'content' => 'Pantai Parangkusumo di Kapanewon Kretek memiliki hubungan penting dengan tradisi keraton Yogyakarta. Salah satu tradisi yang dikenal di kawasan ini adalah Labuhan Keraton Yogyakarta.'
                    ."\n\n"
                    .'Labuhan merupakan upacara adat yang melibatkan penyerahan dan pelarungan benda-benda atau perlengkapan tertentu sesuai tata cara tradisi keraton. Pelaksanaannya berkaitan dengan agenda adat kerajaan, termasuk peringatan kenaikan takhta Sultan.'
                    ."\n\n"
                    .'Dalam pelaksanaannya, perlengkapan upacara disiapkan dan diserahkan melalui rangkaian prosesi yang melibatkan pihak keraton serta unsur pemerintahan atau masyarakat yang terkait. Perlengkapan tersebut kemudian dibawa menuju kawasan pantai untuk menjalani prosesi labuhan.'
                    ."\n\n"
                    .'Tradisi ini memiliki hubungan dengan pandangan kosmologis dalam kebudayaan Jawa, penghormatan terhadap warisan leluhur, dan keberlanjutan tata upacara keraton.'
                    ."\n\n"
                    .'Parangkusumo juga dikenal dalam tradisi tutur masyarakat sebagai kawasan yang memiliki hubungan spiritual dengan sejarah keraton Yogyakarta. Cerita mengenai kawasan tersebut merupakan bagian dari kepercayaan dan tradisi setempat, sehingga perlu dipahami sebagai warisan budaya, bukan semata-mata sebagai fakta sejarah yang seluruh rinciannya telah terbukti secara tertulis.'
                    ."\n\n"
                    .'Ciri khas Labuhan Parangkusumo adalah pelaksanaan tradisi keraton di lingkungan pantai selatan. Hal ini mempertemukan kebudayaan kerajaan dengan lanskap alam pesisir yang memiliki kedudukan penting dalam tradisi Jawa. Labuhan Keraton Yogyakarta telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia.',
            ],
            [
                'name' => 'Pisungsung Jaladri',
                'slug' => 'pisungsung-jaladri',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Ungkapan syukur masyarakat pesisir Parangtritis atas laut dan hasilnya, melalui kirab gunungan dan pelarungan hasil bumi.',
                'content' => 'Pisungsung Jaladri merupakan salah satu tradisi yang berkaitan dengan masyarakat pesisir selatan Bantul, khususnya kawasan Parangtritis dan sekitarnya. Istilah jaladri merujuk pada laut.'
                    ."\n\n"
                    .'Tradisi ini menjadi bentuk ungkapan syukur kepada Tuhan atas keberadaan laut dan manfaatnya bagi kehidupan masyarakat. Bagi masyarakat pesisir, laut bukan sekadar tempat menangkap ikan — laut juga berhubungan dengan mata pencaharian, perdagangan, kehidupan sosial, dan risiko yang harus dihadapi nelayan ketika melaut.'
                    ."\n\n"
                    .'Rangkaian Pisungsung Jaladri dapat mencakup kegiatan merti dusun, kenduri, doa bersama, kirab gunungan, dan pelarungan hasil bumi atau perlengkapan upacara ke laut. Susunan kegiatan dapat mengikuti ketentuan masyarakat penyelenggara.'
                    ."\n\n"
                    .'Gunungan yang dibawa dalam prosesi menjadi simbol hasil bumi dan rasa syukur. Pelaksanaan tradisi memperlihatkan keterlibatan warga dalam mempersiapkan kegiatan, menyediakan perlengkapan, dan menjaga keberlanjutan adat.'
                    ."\n\n"
                    .'Ciri khas Pisungsung Jaladri adalah hubungan langsung antara upacara adat, hasil bumi, kehidupan pesisir, dan laut selatan. Tradisi ini berbeda dalam konteks pelaksanaannya dari upacara syukuran pertanian di pedalaman Bantul. Pisungsung Jaladri juga tercatat sebagai Warisan Budaya Takbenda di tingkat Kabupaten Bantul.',
                'philosophy' => 'Laut adalah sumber penghidupan sekaligus sumber risiko; mensyukurinya berarti menjaga dan menghormatinya.',
            ],
            [
                'name' => 'Merti Dusun',
                'slug' => 'merti-dusun-bantul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Syukuran desa yang bentuknya beragam di tiap dusun: kerja bakti, kenduri, kirab, dan pertunjukan seni.',
                'content' => 'Merti dusun merupakan tradisi yang dijalankan di berbagai desa di Bantul. Istilah merti berkaitan dengan pemeliharaan atau penghormatan, sedangkan dusun merujuk pada lingkungan permukiman masyarakat.'
                    ."\n\n"
                    .'Tradisi ini biasanya dilaksanakan sebagai ungkapan syukur kepada Tuhan atas keselamatan, kesehatan, hasil pertanian, dan keberlangsungan kehidupan masyarakat. Pelaksanaannya dapat berhubungan dengan sejarah dusun, sumber air, tempat yang dianggap penting, atau kebiasaan yang diwariskan oleh leluhur.'
                    ."\n\n"
                    .'Kegiatan merti dusun dapat melibatkan kerja bakti, kenduri, doa bersama, pertunjukan kesenian, kirab, atau kegiatan sosial lainnya. Tidak semua desa melaksanakan rangkaian yang sama karena tata cara setiap dusun berkembang melalui sejarah dan kesepakatan masyarakatnya.'
                    ."\n\n"
                    .'Tradisi ini memiliki peran sosial yang penting. Warga bekerja bersama, menyiapkan makanan, membersihkan lingkungan, dan menyelenggarakan kegiatan yang mempertemukan berbagai kelompok usia.'
                    ."\n\n"
                    .'Ciri khas merti dusun di Bantul bukan terletak pada satu bentuk upacara yang seragam, melainkan pada keberadaan tradisi tersebut dalam kehidupan masyarakat desa. Pelaksanaannya memperlihatkan hubungan antara adat, lingkungan, gotong royong, dan rasa syukur, sekaligus mempertahankan identitas lokal di tengah perubahan zaman.',
            ],
            [
                'name' => 'Rabu Pungkasan Wonokromo',
                'slug' => 'rabu-pungkasan-wonokromo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tradisi Rabu terakhir bulan Sapar di Wonokromo, Pleret, yang dikenal lewat lemper berukuran besar.',
                'content' => 'Rabu Pungkasan merupakan tradisi yang dikenal di Wonokromo, Kapanewon Pleret. Tradisi ini berhubungan dengan bulan Sapar dalam kalender Jawa dan berkembang melalui kepercayaan serta kebiasaan masyarakat setempat.'
                    ."\n\n"
                    .'Salah satu unsur yang dikenal dalam tradisi ini adalah pembuatan lemper dalam ukuran besar. Lemper merupakan makanan yang dibuat dari beras ketan dengan isian tertentu. Dalam pelaksanaan tradisi, lemper menjadi bagian dari identitas kegiatan budaya masyarakat Wonokromo.'
                    ."\n\n"
                    .'Tradisi Rabu Pungkasan juga memiliki hubungan dengan cerita mengenai tokoh agama dan perkembangan kehidupan keagamaan masyarakat setempat. Rincian cerita asal-usulnya dapat berbeda dalam penuturan masyarakat sehingga perlu didokumentasikan berdasarkan sumber sejarah lokal.'
                    ."\n\n"
                    .'Dalam perkembangannya, kegiatan ini menjadi kesempatan untuk menyelenggarakan upacara adat, pertemuan masyarakat, serta kegiatan kebudayaan. Ciri khasnya adalah keterkaitan antara penanggalan Jawa, tradisi masyarakat Pleret, dan lemper yang digunakan dalam kegiatan budaya.',
            ],
            [
                'name' => 'Cembengan',
                'slug' => 'cembengan',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Upacara tradisi industri gula menjelang produksi, mempertemukan dunia pabrik dengan adat Jawa.',
                'content' => 'Cembengan merupakan tradisi yang berkaitan dengan kegiatan pabrik gula di wilayah Yogyakarta, termasuk sejarah industri gula yang berhubungan dengan kawasan Bantul.'
                    ."\n\n"
                    .'Tradisi ini berkembang dari kebiasaan masyarakat dan lingkungan pabrik yang menyelenggarakan upacara menjelang atau dalam rangkaian kegiatan produksi gula. Upacara tersebut berkaitan dengan harapan agar proses produksi berlangsung lancar dan memberikan hasil yang baik.'
                    ."\n\n"
                    .'Dalam sejarahnya, industri gula memiliki hubungan dengan perubahan ekonomi dan kehidupan masyarakat pada masa kolonial hingga periode berikutnya. Keberadaan pabrik dan perkebunan memengaruhi pola pekerjaan, perdagangan, transportasi, serta kehidupan sosial di wilayah sekitarnya.'
                    ."\n\n"
                    .'Cembengan memperlihatkan pertemuan antara dunia industri dan tradisi masyarakat Jawa. Dalam praktiknya, upacara dapat melibatkan doa, sesaji, dan perlengkapan tertentu sesuai tradisi penyelenggara.'
                    ."\n\n"
                    .'Ciri khasnya adalah hubungan dengan siklus produksi gula, bukan semata-mata dengan pertanian rumah tangga. Karena itu, Cembengan memiliki nilai sejarah yang berkaitan dengan perkembangan industri dan kehidupan masyarakat. Cembengan Yogyakarta telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia, dan tradisi ini memiliki persebaran lebih luas daripada satu kabupaten saja.',
            ],
            [
                'name' => 'Sholawat Maulud Jawi',
                'slug' => 'sholawat-maulud-jawi',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Ekspresi keagamaan berbahasa Jawa: pujian kepada Nabi yang dipadukan dengan tembang dan irama Jawa.',
                'content' => 'Sholawat Maulud Jawi merupakan bentuk ekspresi keagamaan yang menggunakan unsur bahasa dan tradisi Jawa. Tradisi ini menjadi bagian dari kekayaan ekspresi lisan yang berkembang di lingkungan masyarakat Yogyakarta, termasuk Bantul.'
                    ."\n\n"
                    .'Sholawat merupakan pujian dan doa kepada Nabi Muhammad, sedangkan penggunaan bahasa Jawa memperlihatkan cara masyarakat setempat mengungkapkan nilai keagamaan melalui bahasa budaya mereka sendiri.'
                    ."\n\n"
                    .'Dalam tradisi semacam ini, unsur keagamaan dapat berpadu dengan tembang, irama, dan gaya pelafalan yang dekat dengan kebudayaan Jawa. Bentuk penyajian dan kebiasaan pelaksanaannya dapat berbeda sesuai lingkungan masyarakat yang mewariskannya.'
                    ."\n\n"
                    .'Kehadiran Sholawat Maulud Jawi menunjukkan bahwa kebudayaan Bantul tidak hanya terdiri atas upacara yang berkaitan dengan pertanian atau kerajaan — budaya keagamaan juga menjadi bagian penting dari kehidupan sosial masyarakat.'
                    ."\n\n"
                    .'Nilai yang menonjol adalah penghormatan kepada Nabi, penguatan hubungan sosial, serta pewarisan ekspresi keagamaan melalui bahasa dan tradisi lokal.',
            ],
            [
                'name' => 'Wiwitan dan Kehidupan Agraris',
                'slug' => 'wiwitan-bantul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Ungkapan syukur menjelang panen padi, menegaskan hubungan manusia, tanah, air, dan kerja bersama.',
                'content' => 'Bantul memiliki kawasan pertanian yang sejak lama membentuk pola kehidupan masyarakat. Aktivitas menanam, merawat, dan memanen padi turut memengaruhi kebiasaan sosial serta tradisi masyarakat desa.'
                    ."\n\n"
                    .'Wiwitan merupakan tradisi yang dikenal dalam kebudayaan Jawa sebagai ungkapan syukur menjelang atau berkaitan dengan panen. Dalam pelaksanaannya, masyarakat dapat menyiapkan makanan, berdoa, dan berbagi hidangan sesuai kebiasaan setempat.'
                    ."\n\n"
                    .'Tradisi ini memperlihatkan hubungan antara kehidupan manusia dan lingkungan pertanian. Panen tidak hanya dipandang sebagai hasil kerja individu, tetapi juga berkaitan dengan kondisi alam, air, tanah, dan kerja bersama masyarakat.'
                    ."\n\n"
                    .'Wiwitan bukan tradisi eksklusif Bantul. Namun, tradisi ini menjadi bagian dari kehidupan budaya masyarakat agraris Bantul dan dapat memiliki bentuk pelaksanaan yang berbeda di setiap desa. Ciri khas budaya agraris Bantul adalah keterkaitan antara kegiatan pertanian, rasa syukur, dan gotong royong.',
            ],

            // ============================================================
            // KESENIAN
            // ============================================================
            [
                'name' => 'Tari Sholawat Montro',
                'slug' => 'tari-sholawat-montro',
                'category' => 'Tarian Tradisional',
                'description' => 'Kesenian dari Kauman, Pleret, yang memadukan sholawat, gerak tari, dan irama bersemangat.',
                'content' => 'Tari Sholawat Montro merupakan kesenian tradisional yang berasal dari Kauman, Kapanewon Pleret, Kabupaten Bantul.'
                    ."\n\n"
                    .'Kesenian ini memadukan unsur sholawat, gerak tari, dan irama yang bersemangat. Tari Sholawat Montro memiliki hubungan dengan perkembangan kebudayaan Islam di lingkungan masyarakat Jawa.'
                    ."\n\n"
                    .'Dalam pertunjukannya, gerakan tubuh, iringan, dan syair membentuk satu kesatuan ekspresi. Kesenian ini bukan hanya sarana hiburan, tetapi juga media untuk menyampaikan pesan keagamaan dan nilai kehidupan.'
                    ."\n\n"
                    .'Kekhasan Tari Sholawat Montro terletak pada asal-usul lokalnya di Kauman, Pleret, serta perpaduan antara seni pertunjukan dan ekspresi keagamaan. Kesenian ini memperlihatkan bahwa masyarakat Bantul memiliki tradisi seni yang berkembang melalui pertemuan kebudayaan Jawa dan Islam.'
                    ."\n\n"
                    .'Pelestariannya dapat dilakukan melalui kelompok seni, pertunjukan pada acara budaya, pendidikan seni di sekolah, dan dokumentasi sejarah para pelakunya.',
            ],
            [
                'name' => 'Jathilan Bantul',
                'slug' => 'jathilan-bantul',
                'category' => 'Tarian Tradisional',
                'description' => 'Tarian berkuda dengan kuda anyaman bambu; di Bantul kekhasannya terletak pada kelompok dan gaya lokal.',
                'content' => 'Jathilan merupakan salah satu kesenian tradisional yang berkembang di berbagai wilayah Yogyakarta, termasuk Bantul.'
                    ."\n\n"
                    .'Kesenian ini umumnya menggunakan properti kuda tiruan yang dibuat dari anyaman bambu atau bahan lainnya. Para penari bergerak mengikuti iringan musik tradisional, seperti kendang, gong, dan instrumen perkusi.'
                    ."\n\n"
                    .'Pertunjukan jathilan dapat melibatkan beberapa penari dan memiliki susunan gerak yang disesuaikan dengan kelompok atau tradisi setempat. Dalam sebagian bentuk pertunjukan, terdapat unsur dramatik dan adegan yang berkaitan dengan kisah keprajuritan atau tradisi lokal.'
                    ."\n\n"
                    .'Di Bantul, jathilan menjadi bagian dari kegiatan seni masyarakat desa, perayaan tertentu, dan pertunjukan budaya. Namun, jathilan bukan kesenian yang hanya dimiliki Bantul karena juga berkembang di wilayah lain di Yogyakarta dan Jawa. Kekhasan Bantul dapat ditelusuri melalui kelompok kesenian lokal, sejarah kelompok, gaya pertunjukan, dan tokoh yang mempertahankannya.',
            ],
            [
                'name' => 'Gejog Lesung',
                'slug' => 'gejog-lesung',
                'category' => 'Alat Musik Tradisional',
                'description' => 'Kesenian bunyi dari lesung penumbuk padi, lahir dari aktivitas agraris masyarakat desa.',
                'content' => 'Gejog lesung merupakan kesenian tradisional yang berhubungan dengan aktivitas pengolahan padi.'
                    ."\n\n"
                    .'Lesung adalah alat tradisional yang digunakan untuk menumbuk padi atau bahan pangan. Dalam kesenian gejog lesung, lesung dipukul dengan alat penumbuk sehingga menghasilkan pola bunyi yang berirama.'
                    ."\n\n"
                    .'Kesenian ini berangkat dari benda yang memiliki fungsi praktis dalam kehidupan masyarakat. Melalui kreativitas warga, aktivitas menumbuk padi berkembang menjadi bentuk pertunjukan yang mengandung nilai seni.'
                    ."\n\n"
                    .'Gejog lesung mencerminkan kehidupan masyarakat agraris, terutama ketika kegiatan pertanian masih sangat bergantung pada tenaga manusia dan peralatan tradisional. Di Bantul, kesenian ini dapat ditemukan dalam kegiatan budaya dan pertunjukan masyarakat.'
                    ."\n\n"
                    .'Akan tetapi, keberadaannya juga dikenal di berbagai daerah Jawa sehingga tidak tepat jika dianggap sebagai kesenian eksklusif Bantul. Ciri pentingnya adalah hubungan antara alat pertanian, bunyi tradisional, dan kehidupan kolektif masyarakat desa.',
                'philosophy' => 'Alat kerja diubah menjadi alat musik — bukti bahwa seni tumbuh dari keseharian.',
            ],
            [
                'name' => 'Karawitan dan Wayang Kulit',
                'slug' => 'karawitan-wayang-bantul',
                'category' => 'Alat Musik Tradisional',
                'description' => 'Gamelan dan wayang kulit gaya Yogyakarta yang hidup di Bantul, didukung dalang, pengrawit, dan perajin.',
                'content' => 'Karawitan merupakan seni musik tradisional yang menggunakan gamelan sebagai perangkat utama. Kesenian ini berkembang kuat dalam kebudayaan Jawa, termasuk di Bantul.'
                    ."\n\n"
                    .'Karawitan dapat mengiringi wayang kulit, tari tradisional, upacara adat, dan kegiatan kebudayaan lainnya. Dalam kehidupan masyarakat, kelompok karawitan sering menjadi wadah pembelajaran seni bagi berbagai kelompok usia.'
                    ."\n\n"
                    .'Wayang kulit juga menjadi bagian penting dari kebudayaan yang berkembang di Bantul. Pertunjukan ini menggunakan boneka kulit yang dimainkan oleh dalang, dengan iringan gamelan dan narasi yang disampaikan melalui bahasa Jawa. Cerita wayang dapat bersumber dari kisah Mahabharata, Ramayana, maupun cerita lain yang berkembang dalam tradisi pewayangan Jawa.'
                    ."\n\n"
                    .'Selain sebagai hiburan, wayang kulit dapat digunakan untuk menyampaikan nilai moral, kritik sosial, ajaran kehidupan, dan refleksi mengenai hubungan antarmanusia.'
                    ."\n\n"
                    .'Bantul memiliki lingkungan budaya yang mendukung keberadaan dalang, pengrawit, pengrajin wayang, dan kelompok seni tradisional. Salah satu kekhasan lokalnya adalah keberadaan sentra tatah sungging wayang kulit di Wukirsari, Imogiri. Karawitan dan wayang kulit merupakan warisan budaya Jawa yang lebih luas, tetapi keduanya menjadi bagian penting dari kehidupan kebudayaan Bantul.',
            ],

            // ============================================================
            // KERAJINAN
            // ============================================================
            [
                'name' => 'Gerabah Kasongan',
                'slug' => 'gerabah-kasongan',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Sentra kerajinan tanah liat di Kasihan yang menjadi identitas ekonomi kreatif Bantul.',
                'content' => 'Kasongan di Kapanewon Kasihan dikenal luas sebagai sentra kerajinan gerabah dan keramik.'
                    ."\n\n"
                    .'Kerajinan ini menggunakan tanah liat yang dibentuk menjadi berbagai benda, seperti pot, vas, wadah, patung, hiasan rumah, dan karya seni dekoratif.'
                    ."\n\n"
                    .'Dalam proses pembuatannya, perajin mengolah tanah liat, membentuknya sesuai rancangan, mengeringkan benda, membakarnya, lalu memberikan penyelesaian akhir. Pada produk tertentu, proses dapat dilanjutkan dengan pelapisan, pengecatan, atau pemberian glasir.'
                    ."\n\n"
                    .'Kasongan berkembang menjadi pusat kerajinan yang menghubungkan keterampilan tradisional dengan kebutuhan pasar. Produk yang dahulu lebih dekat dengan kebutuhan rumah tangga dan benda fungsional berkembang menjadi karya dekorasi dan kerajinan seni.'
                    ."\n\n"
                    .'Keberadaan sentra gerabah juga membentuk identitas ekonomi masyarakat. Keterampilan membuat benda dari tanah liat diwariskan melalui pengalaman, pembelajaran dalam keluarga, dan kegiatan usaha. Kasongan bukan sekadar tempat produksi barang, melainkan ruang tempat keterampilan dan pengetahuan tradisional terus berkembang.',
            ],
            [
                'name' => 'Kerajinan Kulit Manding',
                'slug' => 'kerajinan-kulit-manding',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Sentra kerajinan kulit di Bantul yang dirintis tiga pemuda pada 1947; kini penghasil tas, dompet, dan jaket.',
                'content' => 'Manding merupakan sentra kerajinan kulit yang terletak di wilayah Kapanewon Bantul.'
                    ."\n\n"
                    .'Perkembangan kerajinan kulit di Manding sering dikaitkan dengan perintisan usaha oleh tiga pemuda pada 1947. Dari kegiatan awal tersebut, Manding berkembang menjadi kawasan produksi berbagai barang kulit.'
                    ."\n\n"
                    .'Produk yang dihasilkan meliputi tas, dompet, ikat pinggang, jaket, dan berbagai aksesori. Dalam proses pembuatannya, bahan kulit dipotong, dibentuk, dijahit, dan diselesaikan menjadi produk yang memiliki fungsi serta nilai jual.'
                    ."\n\n"
                    .'Perkembangan Manding memperlihatkan bagaimana keterampilan mengolah bahan dapat tumbuh menjadi kegiatan ekonomi yang melibatkan banyak pelaku usaha.'
                    ."\n\n"
                    .'Ciri khas Manding terletak pada konsentrasi usaha kerajinan kulit di satu kawasan. Pengunjung dapat mengenal berbagai produk sekaligus melihat hubungan antara keterampilan perajin, proses produksi, dan pemasaran. Manding menjadi salah satu identitas ekonomi kreatif Kabupaten Bantul karena kerajinan kulitnya dikenal luas dan menjadi bagian dari daya tarik wisata daerah.',
            ],
            [
                'name' => 'Batik Giriloyo',
                'slug' => 'batik-giriloyo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Batik tulis di Wukirsari, Imogiri, yang tumbuh dari lingkungan budaya keraton Yogyakarta.',
                'content' => 'Giriloyo, yang berada di wilayah Wukirsari, Kapanewon Imogiri, dikenal sebagai salah satu sentra batik di Bantul.'
                    ."\n\n"
                    .'Tradisi membatik di kawasan ini memiliki hubungan dengan lingkungan budaya keraton Yogyakarta. Perkembangan batik juga berkaitan dengan kebutuhan busana, penggunaan motif tertentu, dan penyebaran keterampilan membatik di masyarakat.'
                    ."\n\n"
                    .'Dalam proses batik tulis, perajin menggunakan canting untuk menerapkan malam atau lilin batik pada kain. Kain kemudian melalui proses pewarnaan dan penghilangan malam sesuai tahapan yang digunakan.'
                    ."\n\n"
                    .'Pembuatan batik membutuhkan ketelitian, kesabaran, dan penguasaan teknik. Motif yang dihasilkan dapat mengambil inspirasi dari ragam hias Jawa, tumbuhan, bentuk geometris, serta pengembangan motif sesuai kreativitas perajin.'
                    ."\n\n"
                    .'Di Giriloyo, kegiatan membatik juga menjadi sarana pendidikan budaya dan pengembangan ekonomi masyarakat. Pengunjung dapat mempelajari proses membatik serta mengenal keterampilan yang diwariskan antargenerasi. Ciri khas Giriloyo adalah hubungan antara batik tulis, keterampilan perajin, dan lingkungan sejarah Imogiri.',
                'philosophy' => 'Setiap goresan canting adalah kesabaran yang diwariskan — batik sebagai doa yang ditulis perlahan.',
            ],
            [
                'name' => 'Tatah Sungging Wayang Kulit Wukirsari',
                'slug' => 'tatah-sungging-wukirsari',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kerajinan membuat wayang kulit di Wukirsari, Imogiri: menatah pola lalu menyungging warna.',
                'content' => 'Wukirsari di Kapanewon Imogiri juga dikenal sebagai kawasan yang memiliki tradisi kerajinan wayang kulit.'
                    ."\n\n"
                    .'Pembuatan wayang kulit membutuhkan keterampilan mengolah kulit menjadi bentuk tokoh dengan rincian yang rumit. Prosesnya dapat mencakup pemilihan bahan, pengeringan, penggambaran pola, penatahan, penghalusan, dan pewarnaan.'
                    ."\n\n"
                    .'Tatah adalah proses membentuk pola melalui pahatan pada kulit, sedangkan sungging berkaitan dengan teknik pewarnaan dan pemberian detail pada wayang.'
                    ."\n\n"
                    .'Setiap tokoh wayang memiliki bentuk dan karakter visual yang berbeda. Posisi tubuh, bentuk wajah, ornamen, serta warna dapat membantu membedakan karakter dalam dunia pewayangan.'
                    ."\n\n"
                    .'Kerajinan ini memiliki nilai penting karena berkaitan langsung dengan keberlanjutan seni pertunjukan wayang kulit. Tanpa perajin yang mampu membuat dan memperbaiki wayang, salah satu unsur penting dalam pertunjukan tradisional akan semakin sulit dipertahankan.',
            ],

            // ============================================================
            // KULINER
            // ============================================================
            [
                'name' => 'Mie Lethek Srandakan',
                'slug' => 'mie-lethek',
                'category' => 'Kuliner Khas',
                'description' => 'Mi berbahan tapioka dan gaplek berwarna kecokelatan; WBTb Indonesia, khas Srandakan.',
                'content' => 'Mie Lethek merupakan salah satu kuliner yang sangat erat dengan identitas Kabupaten Bantul, terutama wilayah Kapanewon Srandakan.'
                    ."\n\n"
                    .'Nama lethek dalam bahasa Jawa mengacu pada tampilan yang keruh atau tidak cerah. Sebutan ini berkaitan dengan warna mi yang cenderung kecokelatan atau keabu-abuan, berbeda dari mi yang tampak putih atau kuning terang.'
                    ."\n\n"
                    .'Mie Lethek secara tradisional dibuat menggunakan bahan berbasis tepung tapioka dan gaplek, yaitu singkong kering yang diolah menjadi bahan pangan. Proses produksinya memiliki ciri tersendiri dan pada sebagian proses tradisional dikenal penggunaan tenaga sapi untuk menggerakkan alat penggiling.'
                    ."\n\n"
                    .'Penggunaan teknik tersebut menjadi bagian menarik dari sejarah produksi Mie Lethek. Proses tradisional memperlihatkan hubungan antara pengetahuan mengolah singkong, peralatan produksi, dan keterampilan yang diwariskan oleh para pembuatnya.'
                    ."\n\n"
                    .'Mie Lethek dapat diolah menjadi berbagai hidangan, misalnya mi goreng atau mi rebus dengan tambahan sayuran dan bumbu. Cita rasanya sangat dipengaruhi cara memasak serta bahan pelengkap yang digunakan.'
                    ."\n\n"
                    .'Ciri khas Mie Lethek terletak pada warna, bahan baku, proses pengolahan tradisional, dan keterkaitannya dengan sentra produksi di Srandakan. Mie Lethek telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia.',
                'philosophy' => 'Nama "lethek" yang berarti keruh justru menjadi kebanggaan — bukti bahwa kesederhanaan bisa menjadi identitas.',
            ],
            [
                'name' => 'Sate Klathak Jejeran',
                'slug' => 'sate-klathak',
                'category' => 'Kuliner Khas',
                'description' => 'Sate kambing ditusuk jeruji besi dari Jejeran, Pleret; WBTb Indonesia kategori kemahiran tradisional.',
                'content' => 'Sate Klathak dikenal sebagai salah satu kuliner yang lekat dengan Kabupaten Bantul, khususnya kawasan Jejeran di Kapanewon Pleret.'
                    ."\n\n"
                    .'Sate ini umumnya dibuat dari daging kambing yang dipotong, diberi bumbu sederhana, lalu ditusuk dan dibakar. Salah satu ciri yang paling dikenal adalah penggunaan tusuk dari jeruji besi pada penyajian tradisional tertentu.'
                    ."\n\n"
                    .'Penggunaan jeruji besi membedakannya dari sate yang lazim menggunakan tusuk bambu. Bahan logam dapat membantu menghantarkan panas ke bagian dalam potongan daging selama proses pembakaran.'
                    ."\n\n"
                    .'Bumbunya cenderung sederhana sehingga rasa daging tetap menonjol. Hidangan dapat disajikan bersama kuah atau pelengkap lain sesuai kebiasaan rumah makan.'
                    ."\n\n"
                    .'Sate Klathak berkembang menjadi bagian dari identitas kuliner Bantul dan menarik pengunjung dari berbagai daerah. Ciri khasnya bukan hanya rasa, melainkan juga teknik penusukan dan pembakaran daging yang dikenal luas melalui kawasan Jejeran.'
                    ."\n\n"
                    .'Sate Klathak telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia dalam kategori kemahiran dan kerajinan tradisional. Penetapan tersebut berkaitan dengan pengetahuan dan keterampilan kuliner yang diwariskan dalam masyarakat.',
            ],
            [
                'name' => 'Geplak Bantul',
                'slug' => 'geplak-bantul',
                'category' => 'Kuliner Khas',
                'description' => 'Oleh-oleh khas Bantul dari kelapa dan gula, manis dengan tekstur khas.',
                'content' => 'Geplak merupakan makanan tradisional yang sangat dikenal sebagai oleh-oleh khas Bantul.'
                    ."\n\n"
                    .'Bahan yang digunakan secara umum meliputi kelapa dan gula. Kelapa diolah bersama gula hingga menghasilkan makanan manis dengan tekstur khas. Dalam perkembangannya, tersedia berbagai variasi rasa dan warna.'
                    ."\n\n"
                    .'Kehadiran geplak berkaitan dengan pemanfaatan bahan pangan lokal serta keterampilan membuat makanan yang dapat disimpan dan dipasarkan.'
                    ."\n\n"
                    .'Sebagai produk kuliner, geplak memiliki kedudukan penting dalam citra Bantul. Makanan ini sering dibeli pengunjung sebagai buah tangan setelah mengunjungi kawasan wisata atau pusat perdagangan.'
                    ."\n\n"
                    .'Ciri khas Geplak Bantul terletak pada penggunaan kelapa, rasa manis, bentuk dan teksturnya, serta hubungannya dengan tradisi oleh-oleh daerah. Geplak tidak hanya menjadi makanan konsumsi sehari-hari, tetapi juga bagian dari ekonomi masyarakat melalui produksi rumahan dan usaha pangan.',
            ],
            [
                'name' => 'Gudeg Manggar',
                'slug' => 'gudeg-manggar',
                'category' => 'Kuliner Khas',
                'description' => 'Variasi gudeg berbahan bunga kelapa (manggar), bukan nangka muda.',
                'content' => 'Gudeg Manggar merupakan variasi gudeg yang menggunakan manggar, yaitu bunga kelapa, sebagai bahan utama.'
                    ."\n\n"
                    .'Gudeg yang banyak dikenal masyarakat umumnya menggunakan nangka muda, sedangkan Gudeg Manggar memiliki ciri berbeda karena bahan utamanya adalah manggar.'
                    ."\n\n"
                    .'Bahan tersebut diolah dengan bumbu dan santan hingga menghasilkan hidangan dengan karakter rasa dan tekstur tersendiri. Penyajiannya dapat dilengkapi lauk sesuai kebiasaan setempat.'
                    ."\n\n"
                    .'Gudeg Manggar memiliki hubungan dengan pemanfaatan sumber daya kelapa dan keterampilan kuliner masyarakat. Hidangan ini juga dikenal sebagai bagian dari kekayaan kuliner Bantul dan Yogyakarta.'
                    ."\n\n"
                    .'Ciri khasnya terletak pada penggunaan bunga kelapa sebagai pengganti nangka muda. Namun, gudeg sendiri merupakan kuliner yang dikenal luas di Yogyakarta, sehingga Gudeg Manggar lebih tepat dipahami sebagai salah satu variasi kuliner yang memiliki keterkaitan kuat dengan Bantul, bukan makanan yang hanya ditemukan di daerah ini.',
            ],
            [
                'name' => 'Miedes Pundong',
                'slug' => 'miedes-pundong',
                'category' => 'Kuliner Khas',
                'description' => 'Mi tradisional khas Pundong dengan karakter bahan dan tekstur yang berbeda dari mi umumnya.',
                'content' => 'Miedes merupakan salah satu hidangan mi tradisional yang dikenal dari kawasan Pundong, Bantul.'
                    ."\n\n"
                    .'Miedes memiliki karakter berbeda dari mi pada umumnya karena bahan dan tekstur mi yang digunakan. Hidangan ini biasanya dimasak dengan bumbu serta tambahan bahan pelengkap sesuai kebiasaan penjual.'
                    ."\n\n"
                    .'Keberadaan Miedes memperlihatkan keragaman kuliner Bantul yang tidak hanya terdiri atas makanan manis atau hidangan berbahan daging, tetapi juga olahan mi dengan identitas lokal.'
                    ."\n\n"
                    .'Ciri khas Miedes adalah keterkaitannya dengan Pundong dan karakter mi yang digunakan. Pengetahuan mengenai bahan, proses pembuatan, dan resep merupakan bagian penting dari warisan kuliner yang perlu didokumentasikan.'
                    ."\n\n"
                    .'Untuk menggambarkan Miedes secara akurat, riwayat setiap pembuat atau warung legendaris sebaiknya ditelusuri melalui sumber lokal karena cerita asal-usul kuliner dapat berbeda antara satu keluarga dan keluarga lainnya.',
            ],
            [
                'name' => 'Wedang Uwuh Imogiri',
                'slug' => 'wedang-uwuh',
                'category' => 'Kuliner Khas',
                'description' => 'Minuman rempah dari Imogiri: jahe, kayu secang, dan daun rempah yang memberi warna merah.',
                'content' => 'Wedang uwuh merupakan minuman tradisional yang dikenal dari kawasan Imogiri dan telah menjadi salah satu produk kuliner yang diasosiasikan dengan Bantul.'
                    ."\n\n"
                    .'Nama uwuh dalam bahasa Jawa berarti sampah. Sebutan tersebut berkaitan dengan penampilan campuran rempah dan bahan tumbuhan di dalam minuman yang terlihat seperti tumpukan daun.'
                    ."\n\n"
                    .'Bahan yang digunakan dapat meliputi jahe, kayu secang, daun rempah, dan bahan aromatik lainnya. Campuran tersebut diseduh atau direbus sehingga menghasilkan minuman beraroma rempah dengan warna kemerahan dari kayu secang.'
                    ."\n\n"
                    .'Wedang uwuh dapat disajikan hangat dan sering dikonsumsi sebagai minuman tradisional. Komposisi bahan dapat berbeda sesuai resep pembuatnya.'
                    ."\n\n"
                    .'Ciri khasnya terletak pada campuran rempah yang terlihat jelas, aroma yang kuat, dan hubungan kuliner tersebut dengan kawasan Imogiri. Wedang uwuh memperlihatkan pengetahuan masyarakat mengenai pemanfaatan bahan tumbuhan sebagai minuman tradisional. Klaim mengenai khasiat kesehatan tertentu tetap perlu dibedakan dari fungsi kulinernya dan tidak seharusnya dianggap sebagai pengganti pengobatan medis.',
            ],
            [
                'name' => 'Kuliner Pesisir Bantul',
                'slug' => 'kuliner-pesisir-bantul',
                'category' => 'Kuliner Khas',
                'description' => 'Tradisi pangan masyarakat nelayan di Samas, Depok, dan Pandansimo, termasuk hidangan karangan.',
                'content' => 'Kawasan pesisir Bantul memiliki tradisi pangan yang berkaitan dengan hasil laut, bahan pangan lokal, dan aktivitas masyarakat nelayan.'
                    ."\n\n"
                    .'Salah satu nama kuliner lokal yang dikenal di kawasan pesisir adalah karangan. Sebutan dan bentuk hidangan dapat mengikuti kebiasaan masyarakat setempat, sehingga dokumentasi mengenai bahan, cara pembuatan, dan wilayah asalnya perlu dilakukan secara spesifik.'
                    ."\n\n"
                    .'Secara umum, kuliner pesisir berkembang karena masyarakat memanfaatkan hasil laut dan bahan pangan yang tersedia. Pengolahan ikan, hasil tangkapan, serta makanan pendamping menjadi bagian dari kehidupan sehari-hari dan ekonomi lokal.'
                    ."\n\n"
                    .'Kawasan seperti Samas, Depok, dan Pandansimo memperlihatkan hubungan antara budaya pangan dengan aktivitas nelayan dan pariwisata pesisir.'
                    ."\n\n"
                    .'Ciri khas kuliner pesisir Bantul terletak pada hubungan antara makanan, sumber daya laut, mata pencaharian masyarakat, dan kebiasaan setempat. Namun, tidak semua hidangan yang dijual di kawasan pantai merupakan makanan asli Bantul, sehingga asal-usul masing-masing makanan perlu ditelusuri sebelum dicatat sebagai warisan kuliner daerah.',
            ],

            // ============================================================
            // SITUS SEJARAH
            // ============================================================
            [
                'name' => 'Makam Raja-raja Imogiri',
                'slug' => 'makam-imogiri',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kompleks pemakaman raja Mataram dan penerusnya; pusat warisan sejarah dan tradisi Bantul.',
                'content' => 'Kompleks Makam Raja-Raja Imogiri merupakan salah satu peninggalan sejarah terpenting di Kabupaten Bantul.'
                    ."\n\n"
                    .'Kompleks ini berkaitan dengan sejarah kerajaan Mataram dan dinasti penerusnya. Kawasan tersebut menjadi tempat pemakaman raja-raja serta tokoh penting dalam lingkungan kerajaan.'
                    ."\n\n"
                    .'Selain nilai sejarahnya, Imogiri juga mempunyai nilai budaya karena menjadi tempat berlangsungnya tradisi, tata penghormatan, dan kegiatan yang melibatkan abdi dalem serta masyarakat.'
                    ."\n\n"
                    .'Keberadaan makam raja menunjukkan hubungan antara sejarah pemerintahan, arsitektur tradisional, tata ruang, dan tradisi penghormatan kepada leluhur.'
                    ."\n\n"
                    .'Kawasan ini juga berkaitan dengan berbagai aturan kunjungan dan tata krama. Pengunjung diharapkan menghormati lingkungan makam serta mengikuti ketentuan yang berlaku.'
                    ."\n\n"
                    .'Ciri khas Imogiri adalah keberadaan kompleks pemakaman kerajaan yang masih menjadi bagian dari kehidupan budaya hingga saat ini. Nguras Enceh merupakan salah satu tradisi yang memperkuat hubungan antara kawasan tersebut dan identitas budaya Bantul.',
            ],
            [
                'name' => 'Watu Gilang dan Peninggalan Pleret',
                'slug' => 'watu-gilang',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Peninggalan sejarah di kawasan Pleret yang membantu menjelaskan lanskap kerajaan Jawa.',
                'content' => 'Watu Gilang merupakan salah satu nama peninggalan sejarah yang dikenal dalam konteks sejarah kawasan Pleret dan lingkungan kerajaan Jawa.'
                    ."\n\n"
                    .'Peninggalan sejarah seperti ini memiliki arti penting karena dapat membantu menjelaskan perkembangan kekuasaan, kehidupan masyarakat, dan ingatan sejarah lokal.'
                    ."\n\n"
                    .'Dalam mempelajari situs tersebut, perlu dibedakan antara keterangan arkeologis, informasi yang tercatat dalam sumber sejarah, dan cerita rakyat yang berkembang di masyarakat.'
                    ."\n\n"
                    .'Ciri khas peninggalan sejarah di Pleret adalah hubungannya dengan lanskap sejarah kerajaan yang pernah berkembang di wilayah Bantul. Pelestarian situs perlu disertai dokumentasi, penelitian, penyediaan informasi yang akurat, dan pendidikan sejarah bagi masyarakat.',
            ],

            // ============================================================
            // BAHASA, SOSIAL, EKONOMI
            // ============================================================
            [
                'name' => 'Bahasa Jawa dan Tata Krama',
                'slug' => 'bahasa-jawa-bantul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tingkat tutur bahasa Jawa sebagai cermin tata krama dan penghormatan dalam masyarakat Bantul.',
                'content' => 'Bahasa Jawa merupakan salah satu unsur penting dalam kebudayaan Bantul. Penggunaannya dapat ditemukan dalam percakapan sehari-hari, kesenian tradisional, upacara adat, cerita rakyat, dan kegiatan masyarakat.'
                    ."\n\n"
                    .'Dalam lingkungan sosial tertentu, masyarakat menggunakan tingkat tutur bahasa Jawa yang berbeda sesuai hubungan antarpembicara dan situasi komunikasi.'
                    ."\n\n"
                    .'Penggunaan bahasa yang halus kepada orang yang lebih tua atau dalam situasi resmi menunjukkan pentingnya tata krama dalam kebudayaan Jawa. Namun, praktik berbahasa dapat berbeda berdasarkan usia, lingkungan, dan kebiasaan keluarga.'
                    ."\n\n"
                    .'Bahasa Jawa juga berperan dalam pelestarian tradisi karena banyak istilah adat, nama perlengkapan upacara, tembang, dan ungkapan tradisional memiliki makna yang sulit dipisahkan dari bahasa aslinya.'
                    ."\n\n"
                    .'Ciri khas bahasa masyarakat Bantul merupakan bagian dari ragam bahasa Jawa Yogyakarta. Oleh sebab itu, bahasa Jawa bukan milik eksklusif Bantul, tetapi menjadi unsur penting dalam identitas budaya masyarakatnya.',
                'philosophy' => 'Bahasa bukan sekadar alat komunikasi, tetapi cermin penghormatan kepada orang lain.',
            ],
            [
                'name' => 'Gotong Royong Masyarakat Bantul',
                'slug' => 'gotong-royong-bantul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Nilai sosial dalam persiapan upacara adat, kerja bakti, pertunjukan seni, dan kegiatan desa.',
                'content' => 'Gotong royong merupakan nilai sosial yang terlihat dalam berbagai kegiatan masyarakat Bantul.'
                    ."\n\n"
                    .'Nilai ini dapat ditemukan dalam persiapan upacara adat, pembangunan fasilitas bersama, kegiatan kebersihan lingkungan, pertunjukan kesenian, dan penyelenggaraan kegiatan desa.'
                    ."\n\n"
                    .'Dalam tradisi seperti Kupatan Jolosutro dan merti dusun, keterlibatan warga menjadi unsur penting. Masyarakat tidak hanya hadir sebagai penonton, tetapi juga membantu menyiapkan perlengkapan, makanan, tempat kegiatan, serta kebutuhan lainnya.'
                    ."\n\n"
                    .'Gotong royong memperlihatkan hubungan antara kebudayaan dan kehidupan sosial. Tradisi dapat terus berlangsung karena ada kerja sama dan kesediaan masyarakat untuk mewariskan kebiasaan kepada generasi berikutnya.'
                    ."\n\n"
                    .'Nilai ini tidak hanya terdapat di Bantul, tetapi menjadi salah satu unsur penting kebudayaan masyarakat Jawa yang hidup di wilayah tersebut.',
            ],
            [
                'name' => 'Budaya Pesisir dan Laut Selatan',
                'slug' => 'budaya-pesisir-bantul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Hubungan masyarakat Bantul dengan Samudra Hindia: mata pencaharian, tradisi, dan tantangan pelestarian.',
                'content' => 'Wilayah selatan Bantul berbatasan dengan Samudra Hindia. Keberadaan pantai memengaruhi mata pencaharian, kebiasaan masyarakat, kuliner, dan tradisi setempat.'
                    ."\n\n"
                    .'Masyarakat pesisir memiliki hubungan erat dengan laut melalui kegiatan menangkap ikan, perdagangan hasil laut, dan jasa wisata. Hubungan tersebut juga tercermin dalam tradisi yang mengungkapkan rasa syukur dan harapan akan keselamatan.'
                    ."\n\n"
                    .'Pisungsung Jaladri dan berbagai bentuk sedekah laut merupakan contoh tradisi yang berkaitan dengan kehidupan masyarakat pesisir.'
                    ."\n\n"
                    .'Di sisi lain, kawasan pantai juga memiliki fungsi sebagai ruang ekonomi, rekreasi, dan kegiatan budaya. Perubahan pariwisata membawa peluang sekaligus tantangan, misalnya kebutuhan menjaga kebersihan, kelestarian ekosistem, dan keberlanjutan mata pencaharian masyarakat.'
                    ."\n\n"
                    .'Ciri khas budaya pesisir Bantul adalah pertemuan antara tradisi lokal, kegiatan ekonomi laut, dan kondisi alam pantai selatan.',
            ],
            [
                'name' => 'Ekonomi Kreatif dan Pelestarian Budaya Bantul',
                'slug' => 'ekonomi-kreatif-bantul',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kerajinan sebagai penggerak ekonomi, serta tantangan pelestarian keterampilan tradisional.',
                'content' => 'Perkembangan ekonomi kreatif memberikan kesempatan bagi budaya Bantul untuk terus bertahan.'
                    ."\n\n"
                    .'Sentra gerabah Kasongan, kerajinan kulit Manding, batik Giriloyo, dan tatah sungging wayang kulit Wukirsari merupakan contoh bagaimana keterampilan tradisional dapat berkembang menjadi kegiatan ekonomi.'
                    ."\n\n"
                    .'Dalam perkembangannya, perajin perlu menyesuaikan produk dengan kebutuhan pasar tanpa menghilangkan pengetahuan dan teknik penting yang menjadi identitas kerajinan tersebut.'
                    ."\n\n"
                    .'Kegiatan wisata edukasi dapat membantu masyarakat memperkenalkan proses produksi kepada pengunjung. Sekolah, kelompok pemuda, dan komunitas seni juga dapat terlibat dalam kegiatan pembelajaran dan dokumentasi budaya.'
                    ."\n\n"
                    .'Pelestarian tidak hanya berarti mempertahankan bentuk lama, tetapi juga memastikan keterampilan tersebut dapat dipelajari dan diteruskan kepada generasi berikutnya. Tantangan yang dihadapi antara lain berkurangnya minat generasi muda terhadap pekerjaan tradisional, persaingan produk industri, perubahan bahan baku, dan kebutuhan pemasaran. Karena itu, pelestarian budaya Bantul perlu menggabungkan pendidikan, dokumentasi, dukungan ekonomi, dan keterlibatan masyarakat.',
                'philosophy' => 'Melestarikan bukan berarti membekukan; tradisi hidup harus mampu menyesuaikan diri tanpa kehilangan jati diri.',
            ],
        ];
    }
}
