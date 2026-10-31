<?php

namespace Database\Seeders;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Regency;
use Illuminate\Database\Seeder;

/**
 * Materi budaya Kabupaten Kulon Progo, DI Yogyakarta.
 *
 * Jalankan ulang kapan saja (idempoten, memakai updateOrCreate):
 *     php artisan db:seed --class=KulonProgoCultureSeeder --force
 */
class KulonProgoCultureSeeder extends Seeder
{
    public function run(): void
    {
        $regency = Regency::where('slug', 'kulon-progo')->first();

        if (! $regency) {
            $this->command?->warn('Kabupaten Kulon Progo tidak ditemukan. Jalankan DatabaseSeeder dulu.');

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

        $this->command?->info("Kulon Progo: {$created} materi baru, {$updated} diperbarui (total ".count($items).').');
    }

    /**
     * @return list<array{name: string, slug: string, category: string, description: string, content: string, philosophy?: ?string}>
     */
    private function items(): array
    {
        return [

            // ============================================================
            // gambaran umum
            // ============================================================
            [
                'name' => 'Budaya dan Tradisi Kulon Progo',
                'slug' => 'budaya-dan-tradisi-kulon-progo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Gambaran menyeluruh kebudayaan Kabupaten Kulon Progo: akar sejarah, karakter agraris, dan identitas budayanya.',
                'content' => 'Kabupaten Kulon Progo merupakan salah satu wilayah di Daerah Istimewa Yogyakarta yang memiliki kekayaan budaya, tradisi, adat istiadat, kesenian, dan pengetahuan tradisional yang berkembang secara turun-temurun. Kebudayaan masyarakat Kulon Progo terbentuk melalui perjalanan sejarah panjang serta interaksi masyarakat dengan lingkungan alam, kehidupan sosial, kepercayaan, dan kebudayaan daerah sekitarnya.'
                    ."\n\n"
                    .'Sebagai bagian dari wilayah kebudayaan Jawa, Kulon Progo memiliki hubungan erat dengan kebudayaan Yogyakarta, terutama dalam penggunaan bahasa Jawa, tata krama, kesenian tradisional, upacara adat, serta berbagai kebiasaan masyarakat. Selain pengaruh kebudayaan Yogyakarta, perkembangan budaya Kulon Progo juga mempunyai keterkaitan historis dengan kawasan Bagelen dan Kedu yang berada di wilayah Jawa Tengah.'
                    ."\n\n"
                    .'Karakteristik kebudayaan Kulon Progo sangat dipengaruhi oleh kehidupan masyarakat agraris. Sebagian tradisi yang berkembang di wilayah ini berkaitan dengan pertanian, perkebunan, sumber mata air, lingkungan pedesaan, dan hasil bumi. Hubungan antara manusia dengan alam menjadi salah satu unsur penting dalam kehidupan masyarakat. Hal tersebut terlihat melalui tradisi wiwitan sebelum panen, merti desa, baritan, bersih dusun, nawu sendang, serta berbagai upacara adat yang dilaksanakan sebagai ungkapan rasa syukur atas hasil pertanian dan keselamatan lingkungan.'
                    ."\n\n"
                    .'Selain itu, masyarakat Kulon Progo juga mempertahankan nilai gotong royong, penghormatan kepada leluhur, kebersamaan, dan kepedulian terhadap lingkungan. Kebudayaan Kulon Progo tidak hanya berbentuk kesenian dan upacara adat, tetapi juga mencakup pengetahuan tradisional, keterampilan kerajinan, makanan khas, permainan rakyat, olahraga tradisional, cerita rakyat, bahasa, dan berbagai kebiasaan masyarakat.'
                    ."\n\n"
                    .'Keberagaman tersebut menjadikan Kulon Progo sebagai salah satu wilayah di Yogyakarta yang mempunyai identitas budaya tersendiri — perpaduan kebudayaan Jawa Yogyakarta, pengaruh historis kawasan Bagelen dan Kedu, kehidupan agraris, serta perkembangan masyarakat lokal.',
                'philosophy' => 'Kebudayaan Kulon Progo dibangun atas nilai gotong royong, rasa syukur atas hasil bumi, penghormatan kepada leluhur, keharmonisan dengan alam, kebersamaan, dan kreativitas masyarakat dalam mengolah potensi lokal.',
            ],

            // ============================================================
            // upacara adat & tradisi
            // ============================================================
            [
                'name' => 'Merti Desa',
                'slug' => 'merti-desa-kulon-progo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Kegiatan masyarakat sebagai ungkapan rasa syukur atas keselamatan dan kesejahteraan, disertai kirab gunungan hasil bumi.',
                'content' => 'Merti Desa atau Merti Dusun merupakan kegiatan masyarakat untuk mengungkapkan rasa syukur atas keselamatan, kesejahteraan, dan kehidupan yang telah diberikan.'
                    ."\n\n"
                    .'Pelaksanaan Merti Desa biasanya melibatkan seluruh masyarakat dalam kegiatan kebersihan lingkungan, doa bersama, kenduri, kirab budaya, pertunjukan kesenian tradisional, dan pembagian makanan. Dalam beberapa wilayah, kegiatan tersebut juga disertai kirab gunungan yang berisi hasil pertanian, sayuran, buah-buahan, dan berbagai hasil bumi lainnya.'
                    ."\n\n"
                    .'Gunungan menjadi simbol kemakmuran serta ungkapan rasa syukur masyarakat terhadap hasil alam. Merti Desa juga berfungsi mempererat hubungan antawarga, mempertahankan identitas desa, dan mengenalkan tradisi kepada generasi muda.',
                'philosophy' => 'Gunungan hasil bumi melambangkan kemakmuran dan syukur; kebersamaan warga meneguhkan identitas desa.',
            ],
            [
                'name' => 'Gondang Ho',
                'slug' => 'gondang-ho',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Upacara adat masyarakat Jatimulyo, Girimulyo, yang berkaitan dengan kehidupan agraris dan hubungan dengan lingkungan.',
                'content' => 'Tradisi Gondang Ho merupakan salah satu upacara adat yang dikenal di wilayah Jatimulyo, Kapanewon Girimulyo. Tradisi ini menjadi bagian dari kekayaan budaya masyarakat setempat dan berkaitan dengan kehidupan agraris serta hubungan masyarakat dengan lingkungan.'
                    ."\n\n"
                    .'Pelaksanaannya mengandung nilai kebersamaan, rasa syukur, penghormatan terhadap warisan leluhur, dan kepedulian terhadap sumber daya alam. Tradisi tersebut memperlihatkan bagaimana masyarakat mempertahankan hubungan antara kehidupan sosial, lingkungan, dan kebudayaan lokal.',
            ],
            [
                'name' => 'Nawu Sendang',
                'slug' => 'nawu-sendang',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tradisi membersihkan sumber mata air (sendang) sebagai wujud tanggung jawab menjaga alam.',
                'content' => 'Nawu Sendang merupakan tradisi membersihkan sumber mata air atau sendang yang memiliki peranan penting bagi kehidupan masyarakat. Sendang merupakan sumber air yang secara tradisional dimanfaatkan untuk memenuhi kebutuhan masyarakat dan mendukung kegiatan pertanian.'
                    ."\n\n"
                    .'Dalam pelaksanaannya, masyarakat berkumpul untuk membersihkan kawasan sumber air, melakukan kegiatan bersama, serta menyelenggarakan doa atau ungkapan rasa syukur sesuai kebiasaan setempat.'
                    ."\n\n"
                    .'Tradisi ini mengandung pesan penting mengenai pemeliharaan lingkungan, kebersihan sumber air, dan tanggung jawab masyarakat dalam menjaga alam.',
                'philosophy' => 'Air adalah sumber kehidupan; menjaganya berarti menjaga kehidupan bersama.',
            ],
            [
                'name' => 'Nyadran',
                'slug' => 'nyadran-kulon-progo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Ziarah dan bersih makam leluhur, dilanjutkan doa serta kenduri bersama.',
                'content' => 'Nyadran merupakan tradisi masyarakat Jawa yang dilaksanakan sebagai bentuk penghormatan kepada leluhur melalui kegiatan ziarah makam, membersihkan lingkungan pemakaman, berdoa, dan mengadakan kenduri bersama.'
                    ."\n\n"
                    .'Di Kulon Progo, Nyadran dilaksanakan di berbagai wilayah dengan bentuk dan waktu yang dapat berbeda-beda. Salah satu tradisi yang dikenal adalah Nyadran Makam Joyokusumo di Kalirejo, Kapanewon Kokap. Tradisi ini menjadi bagian dari identitas sejarah masyarakat setempat.'
                    ."\n\n"
                    .'Selain sebagai kegiatan penghormatan terhadap leluhur, Nyadran juga menjadi sarana mempererat hubungan keluarga dan masyarakat, terutama ketika warga yang merantau kembali ke kampung halaman.',
            ],
            [
                'name' => 'Nyadran Agung',
                'slug' => 'nyadran-agung',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Nyadran bersama menjelang Ramadan yang mempertemukan warga Kulon Progo dengan perantau.',
                'content' => 'Tradisi Nyadran Agung menjadi salah satu kegiatan budaya yang dilestarikan oleh Pemerintah Kabupaten Kulon Progo. Kegiatan ini biasanya dilaksanakan menjelang bulan Ramadan dan menjadi kesempatan bagi masyarakat untuk berkumpul, berdoa, serta memperkuat hubungan sosial.'
                    ."\n\n"
                    .'Nyadran Agung mempunyai nilai kebersamaan karena dapat mempertemukan warga yang tinggal di Kulon Progo dengan masyarakat yang merantau ke berbagai daerah.',
            ],
            [
                'name' => 'Saparan',
                'slug' => 'saparan-kulon-progo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tradisi bulan Sapar: kenduri, doa bersama, kirab budaya, dan pertunjukan kesenian.',
                'content' => 'Saparan merupakan tradisi masyarakat Jawa yang berkaitan dengan bulan Sapar dalam kalender Jawa. Di Kulon Progo, tradisi Saparan ditemukan di sejumlah wilayah dan memiliki bentuk pelaksanaan yang beragam.'
                    ."\n\n"
                    .'Kegiatannya dapat berupa kenduri, doa bersama, pertunjukan kesenian, kirab budaya, serta kegiatan sosial masyarakat. Salah satu tradisi yang dikenal adalah Saparan Bendung Kayangan di Pendoworejo, Kapanewon Girimulyo.'
                    ."\n\n"
                    .'Tradisi ini mempunyai hubungan dengan sejarah dan identitas masyarakat setempat. Pelaksanaannya menjadi sarana mempertahankan warisan leluhur sekaligus memperkuat hubungan masyarakat dengan lingkungan dan sejarah wilayahnya.',
            ],
            [
                'name' => 'Rebo Pungkasan',
                'slug' => 'rebo-pungkasan',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Peringatan Rabu terakhir bulan Sapar, dikenal di kawasan Bendung Kayangan, Pendoworejo.',
                'content' => 'Rebo Pungkasan merupakan tradisi yang berkaitan dengan hari Rabu terakhir pada bulan Sapar. Di Kulon Progo, salah satu tradisi Rebo Pungkasan dikenal di kawasan Bendung Kayangan, Kalurahan Pendoworejo, Kapanewon Girimulyo.'
                    ."\n\n"
                    .'Tradisi ini menjadi bagian dari kebudayaan masyarakat setempat yang diwariskan secara turun-temurun. Pelaksanaannya mencerminkan kepercayaan dan kebiasaan masyarakat Jawa dalam memperingati waktu-waktu tertentu berdasarkan kalender tradisional.',
            ],
            [
                'name' => 'Baritan',
                'slug' => 'baritan',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Ungkapan syukur dan permohonan keselamatan atas hasil bumi, dikenal di Galur, Sentolo, dan Samigaluh.',
                'content' => 'Baritan merupakan tradisi masyarakat yang berkaitan dengan kehidupan pertanian, rasa syukur, dan permohonan keselamatan. Tradisi ini dapat dilaksanakan oleh masyarakat desa sebagai bentuk ungkapan terima kasih atas hasil bumi dan perlindungan selama menjalankan aktivitas pertanian.'
                    ."\n\n"
                    .'Di Kulon Progo, Baritan dikenal di beberapa wilayah, termasuk Banaran di Kapanewon Galur, Ngrandu di Kapanewon Sentolo, serta sejumlah wilayah di Samigaluh dan sekitarnya. Pelaksanaannya dapat melibatkan kenduri, doa bersama, pembagian makanan, dan kegiatan kebersamaan.'
                    ."\n\n"
                    .'Tradisi ini memperlihatkan kuatnya hubungan masyarakat Kulon Progo dengan sektor pertanian.',
            ],
            [
                'name' => 'Wiwitan',
                'slug' => 'wiwitan-kulon-progo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Syukuran menjelang panen padi sebagai penghormatan terhadap alam dan hasil bumi.',
                'content' => 'Wiwitan merupakan tradisi yang dilaksanakan menjelang panen padi sebagai ungkapan rasa syukur atas hasil pertanian. Dalam tradisi ini, petani dan masyarakat mengadakan kegiatan simbolis sebelum memulai panen.'
                    ."\n\n"
                    .'Pelaksanaannya dapat melibatkan penyediaan makanan tradisional, doa, serta kegiatan bersama di area persawahan. Wiwitan mengandung nilai penghormatan terhadap alam, rasa syukur atas hasil bumi, dan kesadaran bahwa pertanian merupakan bagian penting dari kehidupan masyarakat.',
            ],
            [
                'name' => 'Jamasan Pusaka',
                'slug' => 'jamasan-pusaka-kulon-progo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Membersihkan keris, tombak, dan benda pusaka sebagai bentuk pelestarian sejarah dan penghormatan leluhur.',
                'content' => 'Jamasan Pusaka merupakan tradisi membersihkan benda-benda pusaka yang mempunyai nilai sejarah, budaya, dan penghormatan bagi masyarakat. Benda yang dijamas dapat berupa keris, tombak, pedang, atau berbagai benda peninggalan leluhur lainnya.'
                    ."\n\n"
                    .'Di Kulon Progo, tradisi ini dikenal melalui Jamasan Pusaka Suroloyo di Kapanewon Samigaluh, Jamasan Pusaka Kyai Landoh di Kapanewon Lendah, serta berbagai tradisi jamasan lainnya.'
                    ."\n\n"
                    .'Selain membersihkan benda pusaka secara fisik, kegiatan ini mempunyai makna pelestarian sejarah dan penghormatan terhadap warisan budaya yang diwariskan oleh generasi terdahulu.',
            ],
            [
                'name' => 'Larakan Kyai Depok',
                'slug' => 'larakan-kyai-depok',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tradisi di Plono Barat, Pagerharjo, Samigaluh; Warisan Budaya Takbenda Indonesia 2026.',
                'content' => 'Larakan Kyai Depok merupakan salah satu tradisi yang berkembang di Plono Barat, Kalurahan Pagerharjo, Kapanewon Samigaluh. Tradisi ini berkaitan dengan tokoh Kyai Depok yang memiliki tempat dalam cerita sejarah dan ingatan budaya masyarakat setempat.'
                    ."\n\n"
                    .'Pelaksanaannya menjadi bagian dari upaya masyarakat dalam mempertahankan hubungan dengan sejarah lokal dan warisan leluhur. Larakan Kyai Depok juga telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2026.',
            ],
            [
                'name' => 'Ki Daruna dan Ni Daruni',
                'slug' => 'ki-daruna-ni-daruni',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Upacara Adat Suran di Bugel, Panjatan, untuk menghormati tokoh yang dihormati masyarakat setempat.',
                'content' => 'Tradisi Ki Daruna dan Ni Daruni merupakan bagian dari kekayaan budaya masyarakat Bugel, Kapanewon Panjatan. Tradisi ini berkaitan dengan tokoh yang dihormati dalam ingatan budaya masyarakat setempat dan dikenal melalui Upacara Adat Suran Ki Daruna Ni Daruni.'
                    ."\n\n"
                    .'Pelaksanaannya menjadi sarana mempertahankan cerita sejarah lokal, penghormatan terhadap tokoh terdahulu, serta memperkuat identitas budaya masyarakat.',
            ],

            // ============================================================
            // kesenian
            // ============================================================
            [
                'name' => 'Tari Angguk',
                'slug' => 'tari-angguk',
                'category' => 'Tarian Tradisional',
                'description' => 'Ikon budaya Kulon Progo: tarian dengan gerakan kepala mengangguk, Warisan Budaya Takbenda Indonesia 2016.',
                'content' => 'Tari Angguk merupakan salah satu kesenian paling terkenal dan menjadi identitas budaya Kabupaten Kulon Progo. Kesenian ini diperkirakan mulai berkembang pada awal abad ke-20 dan mempunyai keterkaitan dengan perkembangan kesenian rakyat di wilayah Yogyakarta dan Bagelen.'
                    ."\n\n"
                    .'Tari Angguk dikenal melalui gerakan kepala penari yang mengangguk mengikuti irama musik. Nama Angguk berasal dari gerakan tersebut. Penarinya menggunakan kostum khas yang menyerupai seragam militer dengan hiasan kepala, atribut dekoratif, dan pakaian berwarna mencolok.'
                    ."\n\n"
                    .'Pada masa awal perkembangannya, Tari Angguk banyak dimainkan oleh penari laki-laki. Seiring perkembangan zaman, muncul berbagai kelompok Tari Angguk dengan penari perempuan yang dikenal sebagai Angguk Putri.'
                    ."\n\n"
                    .'Pertunjukan Tari Angguk memadukan gerakan tari, musik pengiring, nyanyian, dan kekompakan kelompok. Kesenian ini juga mempunyai hubungan dengan penyebaran nilai-nilai sosial dan keagamaan melalui syair atau tembang yang dibawakan selama pertunjukan. Tari Angguk telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2016 dan menjadi salah satu kebanggaan masyarakat Kulon Progo.',
                'philosophy' => 'Gerakan mengangguk menyimbolkan kerendahan hati; syair-syairnya membawa pesan moral dan keagamaan.',
            ],
            [
                'name' => 'Jathilan',
                'slug' => 'jathilan-kulon-progo',
                'category' => 'Tarian Tradisional',
                'description' => 'Tarian prajurit berkuda dengan properti kuda anyaman bambu, kerap diiringi unsur trance.',
                'content' => 'Jathilan merupakan kesenian tari tradisional Jawa yang menggunakan properti kuda tiruan dari anyaman bambu atau bahan lainnya. Penari memainkan gerakan yang menggambarkan prajurit berkuda dengan iringan musik tradisional.'
                    ."\n\n"
                    .'Pertunjukan Jathilan biasanya dilakukan secara berkelompok dan menampilkan gerakan yang dinamis. Dalam beberapa kelompok, pertunjukan juga mempunyai unsur trance atau keadaan kesurupan yang menjadi bagian dari tradisi pertunjukan mereka.'
                    ."\n\n"
                    .'Jathilan berkembang di berbagai wilayah Kulon Progo dan sering ditampilkan dalam kegiatan desa, perayaan adat, serta festival kebudayaan.',
            ],
            [
                'name' => 'Incling',
                'slug' => 'incling',
                'category' => 'Tarian Tradisional',
                'description' => 'Tarian rakyat dengan properti kuda, dinamis dan mengutamakan kekompakan; WBTb Indonesia 2021.',
                'content' => 'Incling merupakan kesenian tradisional yang mempunyai kemiripan dengan pertunjukan tari berkuda. Kesenian ini menampilkan penari yang menggunakan properti kuda dan melakukan gerakan dinamis mengikuti irama musik.'
                    ."\n\n"
                    .'Incling menjadi bagian dari kesenian rakyat yang berkembang melalui kelompok seni lokal. Pertunjukannya mempunyai unsur hiburan, kekompakan, dan identitas budaya masyarakat. Kesenian Incling telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2021.',
            ],
            [
                'name' => 'Lengger Tapeng',
                'slug' => 'lengger-tapeng',
                'category' => 'Tarian Tradisional',
                'description' => 'Seni pertunjukan rakyat Kulon Progo; Warisan Budaya Takbenda Indonesia 2019.',
                'content' => 'Lengger Tapeng merupakan kesenian tradisional yang termasuk dalam kekayaan seni pertunjukan Kulon Progo. Kesenian ini mempunyai karakteristik tersendiri dan menjadi bagian dari tradisi pertunjukan rakyat.'
                    ."\n\n"
                    .'Lengger Tapeng telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2019. Keberadaannya memperlihatkan bahwa kesenian Kulon Progo tidak hanya terdiri atas Tari Angguk dan Jathilan, tetapi juga memiliki berbagai bentuk seni pertunjukan lain yang perlu didokumentasikan.',
            ],
            [
                'name' => 'Oglek',
                'slug' => 'oglek',
                'category' => 'Tarian Tradisional',
                'description' => 'Seni pertunjukan rakyat yang hidup di lingkungan desa; WBTb Indonesia 2019.',
                'content' => 'Oglek merupakan salah satu kesenian tradisional yang berkembang di Kulon Progo. Kesenian ini termasuk seni pertunjukan rakyat yang diwariskan melalui kelompok masyarakat.'
                    ."\n\n"
                    .'Oglek mempunyai nilai budaya karena menjadi bagian dari aktivitas kesenian tradisional yang hidup di lingkungan masyarakat desa. Kesenian ini juga tercatat sebagai salah satu warisan budaya takbenda Kulon Progo yang ditetapkan pada tahun 2019.',
            ],
            [
                'name' => 'Tari Sri Kayun',
                'slug' => 'tari-sri-kayun',
                'category' => 'Tarian Tradisional',
                'description' => 'Tari kreasi baru Kulon Progo: perpaduan Tari Klasik Gaya Yogyakarta dengan Tari Angguk.',
                'content' => 'Salah satu perkembangan kesenian yang lebih baru adalah Tari Sri Kayun, yaitu karya tari kreasi dari Kulon Progo yang memadukan unsur Tari Klasik Gaya Yogyakarta dengan Tari Angguk.'
                    ."\n\n"
                    .'Tari ini menjadi bentuk inovasi budaya yang berusaha mempertahankan identitas lokal melalui pengembangan karya seni baru.',
            ],
            [
                'name' => 'Krumpyung',
                'slug' => 'krumpyung',
                'category' => 'Alat Musik Tradisional',
                'description' => 'Musik tradisional berbahan bambu, identitas musik Kulon Progo; WBTb Indonesia 2017.',
                'content' => 'Krumpyung merupakan kesenian musik tradisional yang mempunyai ciri khas penggunaan alat musik berbahan bambu. Instrumen tersebut menghasilkan bunyi yang khas dan digunakan untuk memainkan berbagai lagu tradisional.'
                    ."\n\n"
                    .'Keunikan Krumpyung terletak pada pemanfaatan bahan alam yang tersedia di lingkungan masyarakat menjadi alat musik dengan fungsi artistik. Kesenian ini memperlihatkan kreativitas masyarakat dalam mengolah bambu menjadi instrumen musik yang mempunyai nilai budaya.'
                    ."\n\n"
                    .'Krumpyung juga menjadi salah satu identitas musik tradisional Kulon Progo dan telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2017.',
                'philosophy' => 'Bambu dari lingkungan sekitar diolah menjadi bunyi — bukti bahwa alam adalah sumber daya sekaligus sumber seni.',
            ],
            [
                'name' => 'Keroncong Kulon Progo',
                'slug' => 'keroncong-kulon-progo',
                'category' => 'Alat Musik Tradisional',
                'description' => 'Musik keroncong dengan instrumen petik, bagian dari keragaman musik masyarakat Kulon Progo.',
                'content' => 'Keroncong merupakan salah satu bentuk kesenian musik yang berkembang dalam kehidupan masyarakat Kulon Progo. Musik ini memperlihatkan perpaduan pengaruh musik dari luar dengan perkembangan budaya musik masyarakat Indonesia.'
                    ."\n\n"
                    .'Pertunjukan keroncong biasanya menggunakan instrumen petik dan alat musik pengiring lainnya untuk menghasilkan irama yang khas. Kesenian ini menjadi bagian dari keragaman musik yang hidup di lingkungan masyarakat.',
            ],
            [
                'name' => 'Panjidur',
                'slug' => 'panjidur',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Seni pertunjukan yang memadukan gerak, musik, dan ekspresi kelompok; WBTb Indonesia 2017.',
                'content' => 'Panjidur merupakan kesenian tradisional yang berkembang di lingkungan masyarakat Kulon Progo. Kesenian ini termasuk seni pertunjukan yang memadukan unsur gerakan, musik, dan ekspresi kelompok.'
                    ."\n\n"
                    .'Panjidur menjadi bagian dari kekayaan kesenian rakyat yang dipertahankan melalui kelompok seni lokal. Keberadaannya memperlihatkan keragaman bentuk pertunjukan tradisional yang berkembang di wilayah Kulon Progo. Panjidur juga telah memperoleh penetapan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2017.',
            ],
            [
                'name' => 'Ketoprak',
                'slug' => 'ketoprak-kulon-progo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Teater tradisional Jawa berbahasa Jawa dengan iringan gamelan, mengangkat sejarah dan legenda.',
                'content' => 'Ketoprak merupakan seni teater tradisional Jawa yang berkembang dalam kehidupan masyarakat Kulon Progo. Pertunjukan Ketoprak biasanya mengangkat cerita sejarah, legenda, kehidupan kerajaan, dan kisah masyarakat.'
                    ."\n\n"
                    .'Dialog menggunakan bahasa Jawa dengan iringan gamelan serta kostum yang disesuaikan dengan karakter tokoh. Ketoprak menjadi media hiburan sekaligus penyampaian pesan moral, nilai kehidupan, dan pengetahuan sejarah kepada masyarakat.',
            ],
            [
                'name' => 'Reog Kulon Progo',
                'slug' => 'reog-kulon-progo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Seni pertunjukan kelompok dengan penari, musik pengiring, dan properti khas.',
                'content' => 'Reog juga menjadi bagian dari kekayaan seni pertunjukan yang berkembang di Kulon Progo. Pertunjukan ini melibatkan kelompok penari, musik pengiring, serta properti khas sesuai dengan tradisi kelompok yang membawakannya.'
                    ."\n\n"
                    .'Selain menjadi hiburan, kesenian Reog memperlihatkan kekompakan, keterampilan, dan kreativitas para pelaku seni tradisional.',
            ],

            // ============================================================
            // kuliner
            // ============================================================
            [
                'name' => 'Geblek',
                'slug' => 'geblek-kulon-progo',
                'category' => 'Kuliner Khas',
                'description' => 'Makanan ikonik dari pati singkong berbentuk angka delapan; Warisan Budaya Takbenda Indonesia 2018.',
                'content' => 'Geblek merupakan makanan khas yang sangat identik dengan Kabupaten Kulon Progo. Makanan ini terbuat dari tepung tapioka atau pati singkong yang dicampur dengan bawang putih, garam, dan bahan lainnya.'
                    ."\n\n"
                    .'Adonan dibentuk menjadi lingkaran atau menyerupai angka delapan, kemudian digoreng hingga matang. Geblek mempunyai warna putih, tekstur kenyal, dan rasa gurih. Makanan ini biasanya dinikmati bersama tempe besengek, sambal, atau minuman tradisional.'
                    ."\n\n"
                    .'Geblek telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2018 dan menjadi salah satu identitas kuliner utama Kulon Progo.',
            ],
            [
                'name' => 'Growol',
                'slug' => 'growol',
                'category' => 'Kuliner Khas',
                'description' => 'Olahan singkong terfermentasi, tekstur padat dan rasa khas; WBTb Indonesia 2019.',
                'content' => 'Growol merupakan makanan tradisional berbahan dasar singkong yang melalui proses perendaman atau fermentasi sebelum dimasak.'
                    ."\n\n"
                    .'Proses pembuatannya dimulai dengan mengupas singkong, merendamnya selama waktu tertentu, mencuci bahan, kemudian mengukusnya hingga matang. Growol mempunyai tekstur padat dan rasa khas hasil proses pengolahan tradisional.'
                    ."\n\n"
                    .'Makanan ini dahulu menjadi salah satu sumber pangan masyarakat karena singkong mudah ditemukan dan dapat diolah menjadi makanan yang tahan untuk dikonsumsi. Growol telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2019.',
            ],
            [
                'name' => 'Besengek Tempe Benguk',
                'slug' => 'besengek-tempe-benguk',
                'category' => 'Kuliner Khas',
                'description' => 'Tempe dari biji benguk dimasak santan dan rempah; WBTb Indonesia 2020. Disajikan dengan geblek.',
                'content' => 'Besengek Tempe Benguk merupakan makanan tradisional khas Kulon Progo yang menggunakan tempe dari biji benguk. Biji benguk berasal dari tanaman kara benguk yang telah lama dimanfaatkan masyarakat sebagai bahan pangan.'
                    ."\n\n"
                    .'Tempe benguk kemudian dimasak menggunakan santan dan berbagai bumbu seperti bawang merah, bawang putih, ketumbar, lengkuas, daun salam, serta rempah lainnya. Hasilnya berupa masakan dengan cita rasa gurih dan tekstur khas.'
                    ."\n\n"
                    .'Besengek Tempe Benguk biasanya disajikan bersama geblek atau makanan tradisional lainnya. Makanan ini telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2020.',
            ],
            [
                'name' => 'Dawet Sambel',
                'slug' => 'dawet-sambel',
                'category' => 'Kuliner Khas',
                'description' => 'Dawet dengan paduan rasa manis, gurih, dan pedas yang tak biasa; WBTb Indonesia 2019.',
                'content' => 'Dawet Sambel merupakan kuliner tradisional khas Kulon Progo yang mempunyai kombinasi rasa berbeda dari minuman dawet pada umumnya. Kuliner ini dikenal melalui perpaduan unsur manis, gurih, dan pedas.'
                    ."\n\n"
                    .'Keunikan tersebut menjadikannya salah satu identitas kuliner yang menarik karena memperlihatkan kreativitas masyarakat dalam mengolah bahan pangan lokal menjadi hidangan khas. Dawet Sambel juga telah tercatat sebagai Warisan Budaya Takbenda Indonesia pada tahun 2019.',
            ],
            [
                'name' => 'Adrem',
                'slug' => 'adrem-kulon-progo',
                'category' => 'Kuliner Khas',
                'description' => 'Jajanan dari tepung beras dan gula merah, manis dan berwarna kecokelatan.',
                'content' => 'Adrem merupakan jajanan tradisional yang dikenal di lingkungan masyarakat Kulon Progo dan wilayah Yogyakarta. Makanan ini dibuat menggunakan tepung beras dan gula merah.'
                    ."\n\n"
                    .'Adonan diolah kemudian digoreng hingga menghasilkan bentuk serta tekstur khas. Adrem memiliki rasa manis dengan warna kecokelatan dan menjadi salah satu jajanan tradisional yang sering ditemukan dalam kegiatan masyarakat maupun pasar tradisional.',
            ],
            [
                'name' => 'Gula Kelapa (Gulo Klopo)',
                'slug' => 'gula-kelapa-kulon-progo',
                'category' => 'Kuliner Khas',
                'description' => 'Gula dari nira bunga kelapa, dibuat dengan keterampilan turun-temurun; WBTb Indonesia 2024.',
                'content' => 'Gula Kelapa atau Gulo Klopo merupakan produk tradisional yang dibuat dari nira bunga kelapa. Masyarakat mengambil nira dengan cara menyadap bunga kelapa yang telah memenuhi syarat.'
                    ."\n\n"
                    .'Nira kemudian disaring dan dimasak menggunakan tungku hingga mengental. Setelah mencapai kekentalan tertentu, bahan diaduk dan dicetak menjadi gula. Proses tersebut membutuhkan keterampilan serta pengalaman agar menghasilkan gula dengan warna, tekstur, dan rasa yang baik.'
                    ."\n\n"
                    .'Produksi gula kelapa menjadi bagian dari mata pencaharian masyarakat sekaligus pengetahuan tradisional yang diwariskan antargenerasi. Gula Kelapa Kulon Progo telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2024.',
            ],
            [
                'name' => 'Kethak',
                'slug' => 'kethak-kulon-progo',
                'category' => 'Kuliner Khas',
                'description' => 'Produk olahan kelapa dari proses pembuatan minyak kelapa; WBTb Indonesia 2024.',
                'content' => 'Kethak merupakan produk tradisional yang berkaitan dengan pengolahan kelapa. Produk ini dihasilkan melalui proses pengolahan bahan kelapa secara tradisional dan mempunyai hubungan erat dengan kegiatan produksi minyak kelapa.'
                    ."\n\n"
                    .'Keberadaan Kethak memperlihatkan pengetahuan masyarakat dalam memanfaatkan hasil perkebunan kelapa secara maksimal. Kethak Kulon Progo juga telah mendapatkan penetapan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2024.',
            ],

            // ============================================================
            // pakaian & kerajinan
            // ============================================================
            [
                'name' => 'Surjan',
                'slug' => 'surjan-kulon-progo',
                'category' => 'Pakaian Adat',
                'description' => 'Pakaian tradisional laki-laki Jawa berkerah tegak, dipakai dengan batik dan blangkon.',
                'content' => 'Pakaian tradisional yang dikenal dalam kebudayaan masyarakat Kulon Progo berkaitan erat dengan kebudayaan Jawa Yogyakarta. Salah satu pakaian yang dikenal adalah Surjan, yaitu pakaian tradisional laki-laki Jawa yang biasanya digunakan bersama kain batik dan blangkon.'
                    ."\n\n"
                    .'Surjan mempunyai bentuk khas dengan kerah tegak dan potongan pakaian yang mencerminkan tata busana tradisional Jawa.',
            ],
            [
                'name' => 'Lurik',
                'slug' => 'lurik-kulon-progo',
                'category' => 'Pakaian Adat',
                'description' => 'Kain bergaris hasil tenun, bahan surjan maupun kebaya.',
                'content' => 'Lurik merupakan kain tradisional yang memiliki pola garis-garis dan sering digunakan sebagai bahan pakaian tradisional. Kain lurik dapat ditemukan dalam bentuk surjan maupun kebaya.'
                    ."\n\n"
                    .'Selain mempunyai fungsi sebagai pakaian, lurik juga memiliki nilai budaya karena berkaitan dengan keterampilan menenun dan tradisi busana masyarakat Jawa.',
            ],
            [
                'name' => 'Blangkon',
                'slug' => 'blangkon-kulon-progo',
                'category' => 'Pakaian Adat',
                'description' => 'Penutup kepala tradisional laki-laki Jawa, bagian dari busana adat.',
                'content' => 'Blangkon merupakan penutup kepala tradisional laki-laki Jawa yang menjadi bagian dari identitas busana adat. Bentuk dan motif blangkon mempunyai variasi sesuai wilayah kebudayaan.'
                    ."\n\n"
                    .'Selain blangkon, masyarakat juga mengenal iket atau kain penutup kepala tradisional.',
            ],

            // ============================================================
            // pengetahuan tradisional & sosial
            // ============================================================
            [
                'name' => 'Sawah Surjan',
                'slug' => 'sawah-surjan',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Sistem lahan bertingkat guludan dan cekungan untuk padi serta palawija; WBTb Indonesia 2019.',
                'content' => 'Sawah Surjan merupakan salah satu bentuk pengetahuan pertanian tradisional yang menjadi identitas penting Kulon Progo. Sistem ini memiliki bentuk lahan yang terdiri atas bagian tinggi atau guludan dan bagian rendah yang memungkinkan petani menanam beberapa jenis tanaman dalam satu kawasan.'
                    ."\n\n"
                    .'Bagian guludan umumnya digunakan untuk tanaman palawija dan hortikultura, sedangkan bagian rendah dapat dimanfaatkan untuk tanaman padi.'
                    ."\n\n"
                    .'Sistem Sawah Surjan memperlihatkan kemampuan masyarakat dalam menyesuaikan pola pertanian dengan kondisi lingkungan dan ketersediaan air. Pengetahuan tersebut diwariskan melalui pengalaman para petani dan menjadi bagian dari identitas agraris Kulon Progo. Sawah Surjan telah ditetapkan sebagai Warisan Budaya Takbenda Indonesia pada tahun 2019.',
                'philosophy' => 'Membagi lahan sesuai tinggi air adalah kearifan bertani: memanfaatkan kondisi alam alih-alih melawannya.',
            ],
            [
                'name' => 'Sambatan dan Gugur Gunung',
                'slug' => 'sambatan-gugur-gunung',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Gotong royong membantu tetangga (sambatan) dan kerja bakti untuk kepentingan bersama (gugur gunung).',
                'content' => 'Kehidupan sosial masyarakat Kulon Progo mempunyai hubungan erat dengan nilai gotong royong dan kebersamaan. Nilai tersebut terlihat dalam kegiatan sambatan, gugur gunung, kerja bakti, kenduri, dan berbagai kegiatan sosial lainnya.'
                    ."\n\n"
                    .'Sambatan merupakan kebiasaan membantu tetangga yang sedang membutuhkan tenaga, misalnya ketika membangun rumah, memperbaiki bangunan, atau mengerjakan pekerjaan tertentu. Kegiatan ini biasanya dilakukan secara sukarela berdasarkan hubungan sosial dan rasa persaudaraan.'
                    ."\n\n"
                    .'Gugur Gunung merupakan kegiatan gotong royong masyarakat untuk menyelesaikan pekerjaan yang bermanfaat bagi kepentingan bersama. Kegiatan tersebut dapat berupa membersihkan jalan, memperbaiki saluran air, membersihkan lingkungan, dan merawat fasilitas umum. Tradisi ini memperlihatkan bahwa masyarakat mempunyai kesadaran untuk menjaga lingkungan dan membantu sesama.',
            ],
            [
                'name' => 'Adat Daur Hidup Jawa',
                'slug' => 'adat-daur-hidup-kulon-progo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Rangkaian tradisi dari kehamilan, kelahiran, masa kanak, pernikahan, hingga kematian.',
                'content' => 'Dalam kehidupan keluarga, masyarakat Kulon Progo mengenal berbagai adat yang berkaitan dengan tahapan kehidupan manusia, mulai dari kelahiran, masa kanak-kanak, pernikahan, hingga kematian.'
                    ."\n\n"
                    .'Pada masa kehamilan dikenal tradisi neloni, ngapati, dan mitoni. Tradisi mitoni atau tingkeban biasanya dilaksanakan ketika usia kehamilan mencapai tujuh bulan. Kegiatan ini menjadi ungkapan harapan agar ibu dan calon bayi memperoleh keselamatan.'
                    ."\n\n"
                    .'Setelah kelahiran, terdapat tradisi brokohan, puputan, sepasaran, dan selapanan. Brokohan merupakan kegiatan syukuran setelah kelahiran bayi, sedangkan puputan berkaitan dengan peristiwa lepasnya tali pusar. Sepasaran dilaksanakan ketika bayi berusia lima hari menurut perhitungan pasaran Jawa, sementara selapanan dilaksanakan ketika bayi berusia sekitar tiga puluh lima hari.'
                    ."\n\n"
                    .'Pada masa pertumbuhan anak dikenal tradisi Tedak Siten yang dilaksanakan ketika anak mulai belajar menginjakkan kaki ke tanah. Tradisi ini mengandung harapan agar anak tumbuh sehat, memperoleh keselamatan, dan mampu menjalani kehidupannya dengan baik.'
                    ."\n\n"
                    .'Dalam adat pernikahan Jawa, masyarakat mengenal rangkaian kegiatan seperti lamaran, srah-srahan, siraman, akad atau ijab, panggih, balangan gantal, wiji dadi, kacar-kucur, dan dahar klimah. Setiap prosesi mempunyai makna simbolis yang berkaitan dengan tanggung jawab, kesetiaan, penghormatan, dan kehidupan rumah tangga.'
                    ."\n\n"
                    .'Dalam tradisi kematian, masyarakat Jawa mengenal kegiatan ngrukti laya, brobosan, dan berbagai selamatan setelah pemakaman. Selamatan dapat dilaksanakan pada hari ketiga, ketujuh, keempat puluh, keseratus, hingga seribu hari setelah seseorang meninggal.',
                'philosophy' => 'Setiap tahap kehidupan ditandai doa dan kebersamaan keluarga — menegaskan bahwa manusia tidak hidup sendiri.',
            ],
            [
                'name' => 'Bahasa dan Sastra Jawa Kulon Progo',
                'slug' => 'bahasa-sastra-jawa-kulon-progo',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Tingkatan ngoko–krama–krama inggil, tembang Macapat, geguritan, parikan, dan cerita rakyat.',
                'content' => 'Bahasa utama masyarakat Kulon Progo adalah Bahasa Jawa dan Bahasa Indonesia. Bahasa Jawa memiliki peranan penting dalam kehidupan sosial dan kebudayaan karena digunakan dalam percakapan sehari-hari, kegiatan adat, pertunjukan seni, serta komunikasi antargenerasi. Penggunaan bahasa Jawa juga berkaitan dengan tata krama dan penghormatan kepada orang lain.'
                    ."\n\n"
                    .'Dalam kebudayaan Jawa dikenal tingkatan bahasa ngoko, krama, dan krama inggil. Ngoko digunakan dalam percakapan informal dengan teman sebaya atau orang yang sudah akrab. Krama digunakan dalam situasi yang lebih sopan, sedangkan krama inggil merupakan bentuk bahasa penghormatan yang digunakan untuk menghargai orang lain.'
                    ."\n\n"
                    .'Sastra tradisional juga menjadi bagian dari kekayaan budaya Kulon Progo. Bentuknya meliputi tembang Macapat, geguritan, parikan, cerita rakyat, dongeng, pepatah Jawa, dan berbagai ungkapan tradisional. Tembang Macapat merupakan puisi tradisional Jawa yang mempunyai aturan tertentu mengenai jumlah baris, jumlah suku kata, dan bunyi akhir setiap baris. Geguritan merupakan karya sastra berbentuk puisi berbahasa Jawa yang digunakan untuk menyampaikan perasaan, pemikiran, pengalaman, dan pesan kehidupan.'
                    ."\n\n"
                    .'Tradisi lisan seperti cerita rakyat dan legenda juga mempunyai peranan penting dalam mempertahankan ingatan sejarah masyarakat. Cerita mengenai tokoh lokal, asal-usul tempat, sejarah desa, dan peninggalan leluhur menjadi bagian dari identitas budaya yang diwariskan melalui tuturan masyarakat.',
            ],
            [
                'name' => 'Nglarak Blarak dan Jemparingan',
                'slug' => 'nglarak-blarak-jemparingan',
                'category' => 'Tradisi & Kesenian',
                'description' => 'Permainan rakyat dan olahraga tradisional: Nglarak Blarak serta seni memanah jemparingan.',
                'content' => 'Permainan rakyat merupakan bagian dari kebudayaan yang berkembang melalui kehidupan anak-anak dan masyarakat. Permainan tradisional biasanya menggunakan bahan sederhana yang tersedia di lingkungan sekitar dan mengutamakan interaksi sosial, kekompakan, ketangkasan, serta kreativitas.'
                    ."\n\n"
                    .'Nglarak Blarak adalah permainan tradisional yang dikembangkan menjadi kegiatan perlombaan dan festival kebudayaan. Permainan ini melibatkan unsur kerja sama, ketangkasan, dan kekompakan peserta. Festival Nglarak Blarak menjadi salah satu upaya pemerintah daerah untuk mengenalkan kembali permainan tradisional kepada generasi muda.'
                    ."\n\n"
                    .'Selain itu, terdapat jemparingan atau seni memanah tradisional yang berkembang sebagai salah satu kegiatan olahraga dan kebudayaan. Jemparingan mempunyai hubungan dengan tradisi memanah Jawa dan mengandung nilai konsentrasi, ketenangan, ketepatan, serta pengendalian diri.',
            ],
        ];
    }
}
