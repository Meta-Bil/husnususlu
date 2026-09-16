<?php

namespace Database\Seeders;

use App\Enums\PageTemplate;
use App\Models\Page;
use App\Models\Treatment;
use App\Models\Video;
use App\Settings\SiteSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/**
 * Arranges the blocks the WordPress import produced so the important pages read
 * like the approved design.
 *
 * The import could only map Elementor sections into generic blocks, which left
 * the pages as walls of `rich_text`. This seeder is the editorial pass on top of
 * it: it reuses the imported blocks, gives them the headings and the order of
 * the mockups and adds the designed sections whose content already exists as a
 * record (treatments, videos, posts) or in the mockups themselves.
 *
 * It writes no new prose of its own, it never drops an imported text block, and
 * running it twice leaves the database exactly as the first run did — imported
 * blocks are looked up by their body, which this seeder never rewrites.
 */
class ComposePagesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Prefix of the `import_notes` line this seeder owns; a rerun replaces it
     * instead of appending a second one.
     */
    private const NOTE_PREFIX = 'Tasarıma göre düzenlendi:';

    /** Pain types in the order the mockups number them. */
    private const PAIN_TYPES = [
        'bel-ve-bacak-agrilari',
        'boyun-servikal-agrilari',
        'bas-ve-yuz-agrilari',
        'omuz-kol-agrilari',
        'sirt-ve-gogus-agrilari',
        'fibromiyalji',
        'miyofasiyal-agri-sendromu',
        'noropatik-agrilar',
        'kanser-agrilari',
        'batin-ve-pelvik-agrilar',
    ];

    /** Interventional procedures shown as chips under the pain types. */
    private const PROCEDURE_CHIPS = [
        'epidural-steroid-uygulamasi',
        'epidural-kateter-uygulamasi',
        'faset-eklem-enjeksiyonlari',
        'radyofrekans-uygulamalari',
        'sempatik-bloklar',
        'tetik-nokta-enjeksiyonlari',
        'vertebroplasti',
        'spinal-port-uygulamasi',
        'dorsal-kolon-stimulasyonu',
    ];

    public function run(): void
    {
        $this->composeHome();
        $this->composeAbout();
        $this->composeContact();
        $this->composeInternationalPatients();
        $this->composeNucleoplasty();
        $this->composeBackAndLegPain();
    }

    /**
     * The home page: the mockup's hero, credentials strip, non-surgical section,
     * pain types, physician, media, reading corner and appointment form.
     */
    private function composeHome(): void
    {
        $page = Page::query()->where('template', PageTemplate::Home)->first();

        if (! $page) {
            return;
        }

        $imported = $this->blocksOf($page);
        $nucleoplasty = $this->treatment('nukleoplasti-islemi');

        $this->save($page, [
            $this->block($this->pick($imported, 'hero'), 'hero', [
                'variant' => 'home',
                'eyebrow' => ['tr' => 'Algoloji — ağrı bilimi', 'en' => 'Algology — the science of pain'],
                'title' => ['tr' => 'Ağrısız yaşam', 'en' => 'Living without pain'],
                'accent' => ['tr' => 'mümkün.', 'en' => 'is possible.'],
                'lead' => [
                    'tr' => 'Algoloji, ağrılarınızın kaynağını bulan ve sizi doğru çözüme ulaştıran bilim dalıdır. Otuz yıllık hekimlik deneyimiyle ameliyatsız ve girişimsel tedavi seçenekleri sunuyoruz.',
                    'en' => 'Algology is the branch of medicine that finds the source of your pain and guides you to the right solution. With thirty years of medical experience, we offer non-surgical and interventional treatment options.',
                ],
                'image' => $this->photo('ofis.jpg'),
                'show_cta' => true,
                'show_phone' => true,
            ]),

            $this->block($this->pick($imported, 'stats'), 'stats', [
                'use_settings' => true,
            ]),

            $this->block(null, 'media_strip', [
                'use_settings' => true,
            ]),

            $this->block(null, 'treatment_highlight', [
                'eyebrow' => ['tr' => 'I — Ameliyatsız fıtık tedavisi'],
                'title' => ['tr' => 'Fıtıkta ameliyat,'],
                'accent' => ['tr' => 'tek çözüm değil.'],
                'lead' => ['tr' => 'Bel ve boyun fıtığında cerrahiye alternatif, lokal anestezi altında uygulanan minimal invaziv yöntemler. Doğru hasta, doğru yöntemle.'],
                'featured_treatment_id' => $nucleoplasty?->getKey(),
                'featured_image' => $this->photo('ameliyathane.jpg'),
                'featured_eyebrow' => ['tr' => 'Öne çıkan işlem'],
                'featured_title' => ['tr' => 'Nükleoplasti'],
                'featured_text' => ['tr' => 'Bel ve boyun fıtığında, açık cerrahi gerektirmeden diskin sinir üzerindeki baskısını azaltmayı amaçlayan girişimsel yöntem.'],
                'featured_link_label' => ['tr' => 'İşlemi inceleyin'],
                'facts' => [
                    ['value' => ['tr' => '20–30 dk'], 'label' => ['tr' => 'işlem süresi']],
                    ['value' => ['tr' => 'Lokal'], 'label' => ['tr' => 'anestezi']],
                    ['value' => ['tr' => '2–4 saat'], 'label' => ['tr' => 'sonra taburcu']],
                ],
                'card_source' => 'manual',
                'cards' => [
                    [
                        'title' => ['tr' => 'Ameliyatsız Bel Fıtığı'],
                        'eyebrow' => ['tr' => 'Lazer işlemi'],
                        'text' => ['tr' => 'Fıtıklaşan diskin sinir üzerindeki baskısını, cerrahi müdahale gerektirmeden azaltmayı hedefleyen modern bir tedavi seçeneği.'],
                        'link_label' => ['tr' => 'Detaylı bilgi'],
                        'url' => $this->treatmentUrl('ameliyatsiz-lazerle-bel-fitigi-tedavisi'),
                    ],
                    [
                        'title' => ['tr' => 'Ameliyatsız Boyun Fıtığı'],
                        'eyebrow' => ['tr' => 'Lazer işlemi'],
                        'text' => ['tr' => 'Cerrahi kesiye gerek kalmadan, lokal anestezi altında sinir üzerindeki baskıyı azaltmayı amaçlayan girişimsel bir uygulama.'],
                        'link_label' => ['tr' => 'Detaylı bilgi'],
                        'url' => null,
                    ],
                    [
                        'title' => ['tr' => 'Radyofrekans ile Fıtık Tedavisi'],
                        'eyebrow' => ['tr' => 'Radyofrekans uygulaması'],
                        'text' => ['tr' => 'Sinir kökü çevresindeki ağrıyı kontrol altına almak ve diskin neden olduğu basıncı azaltmak için uygulanan ameliyatsız yöntem.'],
                        'link_label' => ['tr' => 'Detaylı bilgi'],
                        'url' => $this->treatmentUrl('radyofrekans-uygulamalari'),
                    ],
                ],
            ]),

            $this->block(null, 'comparison_table', [
                'eyebrow' => ['tr' => 'Karşılaştırma'],
                'title' => ['tr' => 'Lazer tedavisi ve geleneksel ameliyat'],
                'lead' => ['tr' => 'Hangi yöntemin size uygun olduğuna muayene ve görüntüleme sonrası birlikte karar veriyoruz.'],
                'column_a' => ['tr' => 'Lazer tedavisi'],
                'column_b' => ['tr' => 'Geleneksel ameliyat'],
                'rows' => [
                    ['label' => ['tr' => 'Anestezi'], 'value_a' => ['tr' => 'Lokal anestezi'], 'value_b' => ['tr' => 'Genel anestezi']],
                    ['label' => ['tr' => 'Hastanede kalış'], 'value_a' => ['tr' => '2–4 saat'], 'value_b' => ['tr' => '1–2 gün']],
                    ['label' => ['tr' => 'İşe dönüş süresi'], 'value_a' => ['tr' => '3–5 gün'], 'value_b' => ['tr' => '4–6 hafta']],
                    ['label' => ['tr' => 'Doku hasarı'], 'value_a' => ['tr' => 'Çok düşük'], 'value_b' => ['tr' => 'Kas ve kemik dokusu etkilenebilir']],
                ],
            ]),

            $this->block(null, 'process_steps', [
                'eyebrow' => ['tr' => 'Dört adımda'],
                'title' => ['tr' => 'Tedavi süreciniz'],
                'steps' => [
                    ['title' => ['tr' => 'Muayene'], 'text' => ['tr' => 'Şikâyetinizi, geçmişinizi ve görüntülemelerinizi birlikte değerlendiriyoruz.']],
                    ['title' => ['tr' => 'Tanı'], 'text' => ['tr' => 'Ağrının kaynağını fizik muayene ve gerekirse ek tetkiklerle netleştiriyoruz.']],
                    ['title' => ['tr' => 'Kişiye özel plan'], 'text' => ['tr' => 'Size en uygun ameliyatsız ya da girişimsel tedaviyi belirliyoruz.']],
                    ['title' => ['tr' => 'İşlem ve takip'], 'text' => ['tr' => 'İşlem sonrası kontrollerle iyileşme sürecinizi yakından izliyoruz.']],
                ],
            ]),

            $this->block($this->pickText($imported, 'Ameliyatsız bel fıtığı tedavisinde kullanılan lazer'), 'rich_text', [
                'background' => 'paper',
                'eyebrow' => ['tr' => 'Lazer işlemi'],
                'title' => ['tr' => 'Ameliyatsız bel fıtığı'],
            ]),

            $this->block($this->pickText($imported, 'Ameliyatsız boyun fıtığı tedavisinde lazer'), 'rich_text', [
                'background' => 'ivory',
                'eyebrow' => ['tr' => 'Lazer işlemi'],
                'title' => ['tr' => 'Ameliyatsız boyun fıtığı'],
            ]),

            $this->block($this->pickText($imported, 'hedeflenen sinir dokusu ısıtılır'), 'rich_text', [
                'background' => 'paper',
                'eyebrow' => ['tr' => 'Radyofrekans uygulaması'],
                'title' => ['tr' => 'Radyofrekans ile fıtık tedavisi'],
            ]),

            $this->block($this->pickText($imported, 'Algoloji (Ağrı Tedavisi) Uygulamaları'), 'rich_text', [
                'background' => 'ivory',
                'title' => ['tr' => 'Ameliyatsız fıtık tedavisi'],
            ]),

            $this->block($this->pick($imported, 'treatment_index'), 'treatment_index', [
                'eyebrow' => ['tr' => 'II — Ağrı türleri'],
                'title' => ['tr' => 'Her ağrının bir'],
                'accent' => ['tr' => 'kaynağı vardır.'],
                'lead' => ['tr' => 'Her bel ağrısının sebebi fıtık değildir. Doğru teşhis, doğru tedavinin ilk adımıdır.'],
                'pain_type_ids' => $this->treatmentIds(self::PAIN_TYPES),
                'show_procedures' => true,
                'procedures_label' => ['tr' => 'Girişimsel tedaviler'],
                'procedure_ids' => $this->treatmentIds(self::PROCEDURE_CHIPS),
            ]),

            $this->block(null, 'doctor_profile', $this->doctorProfile([
                'eyebrow' => ['tr' => 'III — Hekiminiz'],
                'text' => ['tr' => 'Ağrıyı geçici olarak bastırmak yerine kaynağına bilimsel, girişimsel yöntemlerle ulaşmayı esas alır; hastalarına cerrahiye alternatif seçenekler sunar.'],
                'facts' => [
                    ['label' => ['tr' => 'Tıp eğitimi'], 'value' => ['tr' => 'İstanbul Üniversitesi Cerrahpaşa Tıp Fakültesi']],
                    ['label' => ['tr' => 'Uzmanlık'], 'value' => ['tr' => 'Anesteziyoloji ve Reanimasyon — Dr. Lütfi Kırdar Kartal Eğitim ve Araştırma Hastanesi']],
                    ['label' => ['tr' => 'Yan dal'], 'value' => ['tr' => 'Algoloji (Ağrı Bilimi)']],
                    ['label' => ['tr' => 'Üyelikler'], 'value' => ['tr' => 'IASP · Türk Anesteziyoloji ve Reanimasyon Derneği · Algoloji Derneği · Türk Yoğun Bakım Derneği · Rejyonal Anestezi Derneği']],
                ],
            ])),

            $this->block($this->pick($imported, 'video_grid'), 'video_grid', [
                'eyebrow' => ['tr' => 'V — Medya'],
                'title' => ['tr' => 'Ekranlarda ağrı bilimi'],
                'source' => 'selected',
                'video_ids' => $this->videoIds(['-AWn5RpcsC0', 'Br54yed9Xjc', 'n-g6h1R2Ga8']),
                'show_featured' => false,
            ]),

            $this->block(null, 'latest_posts', [
                'eyebrow' => ['tr' => 'VI — Bilgi köşesi'],
                'title' => ['tr' => 'Hastalarımızın en çok sorduğu sorular'],
                'source' => 'latest',
                'limit' => 3,
            ]),

            $this->block(null, 'appointment', [
                'eyebrow' => ['tr' => 'Randevu'],
                'title' => ['tr' => 'Randevunuzu'],
                'accent' => ['tr' => 'birlikte planlayalım.'],
                'lead' => ['tr' => 'Formu doldurun, sizi arayarak uygun gün ve saati birlikte belirleyelim. Dilerseniz WhatsApp veya telefonla da ulaşabilirsiniz.'],
                'show_contact_lines' => true,
            ]),
        ], 'anasayfa (Main.dc.html) bölüm sırası.');
    }

    /**
     * Hakkımda: page title, biography, education and career, fields of
     * expertise, publications, the broadcast strip and the appointment band.
     */
    private function composeAbout(): void
    {
        $page = $this->page('hakkimda');

        if (! $page) {
            return;
        }

        $imported = $this->blocksOf($page);

        $this->save($page, [
            $this->block(null, 'hero', [
                'variant' => 'page',
                'eyebrow' => ['tr' => 'Hakkımda'],
                'title' => ['tr' => 'Prof. Dr.'],
                'accent' => ['tr' => 'Hüsnü Süslü'],
                'lead' => ['tr' => 'Ağrıyı geçici olarak bastırmak yerine kaynağına bilimsel, girişimsel yöntemlerle ulaşmayı esas alır; hastalarına cerrahiye alternatif seçenekler sunar.'],
                'image' => $this->photo('klinik.jpg'),
                'show_cta' => true,
                'show_phone' => true,
            ]),

            $this->block(null, 'doctor_profile', $this->doctorProfile([
                'eyebrow' => ['tr' => 'I — Özgeçmiş'],
                'text' => ['tr' => 'Kuşkusuz, her ağrı hikâyesi kişiye özeldir. Bu nedenle hastalarımı sadece semptomları üzerinden değil, biyopsikososyal bir bütünlük içinde değerlendirerek kendilerine uygun tedavi seçeneklerini planlıyorum.'],
                'facts' => [
                    ['label' => ['tr' => 'Doğum'], 'value' => ['tr' => '1970, Erzincan']],
                    ['label' => ['tr' => 'Tıp eğitimi'], 'value' => ['tr' => 'İ.Ü. Cerrahpaşa Tıp Fakültesi, 1994']],
                    ['label' => ['tr' => 'Uzmanlık'], 'value' => ['tr' => 'Anesteziyoloji ve Reanimasyon']],
                    ['label' => ['tr' => 'Yan dal'], 'value' => ['tr' => 'Algoloji (Ağrı Bilimi)']],
                    ['label' => ['tr' => 'Yabancı dil'], 'value' => ['tr' => 'İngilizce']],
                ],
            ])),

            $this->block($this->pickText($imported, 'biyopsikososyal'), 'rich_text', [
                'background' => 'ivory',
                'eyebrow' => ['tr' => 'Özgeçmiş'],
                'title' => ['tr' => 'Hastayı bir bütün olarak değerlendirmek.'],
            ]),

            $this->block($this->pickText($imported, 'Algoloji Profesörü İstanbul'), 'rich_text', [
                'background' => 'paper',
            ]),

            $this->block(null, 'timeline', [
                'eyebrow' => ['tr' => 'II — Eğitim ve kariyer'],
                'title' => ['tr' => 'Eğitim ve'],
                'accent' => ['tr' => 'kariyer'],
                'lead' => ['tr' => 'Tıp fakültesinden uzmanlık eğitimine, ağrı polikliniklerinden üniversitede öğretim üyeliğine uzanan mesleki yol.'],
                'groups' => [
                    [
                        'label' => ['tr' => 'Eğitim'],
                        'items' => [
                            ['year' => '1976 – 1987', 'title' => ['tr' => 'Kartal\'da ilk ve orta öğrenim'], 'institution' => ['tr' => 'Zekeriya Güçer İlkokulu, Kartal Ortaokulu, Kartal Lisesi']],
                            ['year' => '1987 – 1989', 'title' => ['tr' => 'Dokuz Eylül Üniversitesi Tıp Fakültesi'], 'institution' => ['tr' => 'Tıp eğitimi']],
                            ['year' => '1989 – 1994', 'title' => ['tr' => 'İstanbul Üniversitesi Cerrahpaşa Tıp Fakültesi'], 'institution' => ['tr' => 'Tıp eğitimi · mezuniyet 1994']],
                            ['year' => '1993 – 1994', 'title' => ['tr' => 'İnlingua Dil Okulu, Ulm / Almanya'], 'institution' => ['tr' => 'Ağustos 1993 – Ocak 1994']],
                            ['year' => '1997 – 2001', 'title' => ['tr' => 'Dr. Lütfi Kırdar Kartal Eğitim ve Araştırma Hastanesi'], 'institution' => ['tr' => 'Anesteziyoloji ve Reanimasyon Kliniği · uzmanlık eğitimi']],
                            ['year' => '2002', 'title' => ['tr' => 'Vakıf Gureba Eğitim ve Araştırma Hastanesi'], 'institution' => ['tr' => 'Ağrı polikliniğinde 2 ay süreli ağrı eğitimi (Ocak – Şubat)']],
                            ['year' => '2006', 'title' => ['tr' => 'İstanbul Üniversitesi İstanbul Tıp Fakültesi'], 'institution' => ['tr' => 'Algoloji Kliniği\'nde 6 ay süreli eğitim']],
                        ],
                    ],
                    [
                        'label' => ['tr' => 'Deneyim'],
                        'items' => [
                            ['year' => '1994 – 1995', 'title' => ['tr' => 'Mardin Kızıltepe Devlet Hastanesi'], 'institution' => ['tr' => 'Pratisyen hekim, mecburi hizmet']],
                            ['year' => '1995 – 1996', 'title' => ['tr' => 'Mardin Devlet Hastanesi'], 'institution' => ['tr' => 'Pratisyen hekim, mecburi hizmet']],
                            ['year' => '2002', 'title' => ['tr' => 'Beykoz Çocuk Göğüs Hastalıkları Hastanesi'], 'institution' => ['tr' => 'Anesteziyoloji ve Reanimasyon uzmanı']],
                            ['year' => '2002 – 2003', 'title' => ['tr' => 'Şile Devlet Hastanesi'], 'institution' => ['tr' => '6 ay süreli']],
                            ['year' => '2003 – 2006', 'title' => ['tr' => 'Dr. Lütfi Kırdar Kartal Eğitim ve Araştırma Hastanesi'], 'institution' => ['tr' => 'I. Anesteziyoloji ve Reanimasyon Kliniği']],
                            ['year' => '2006 – 2012', 'title' => ['tr' => 'Dr. Lütfi Kırdar Kartal Eğitim ve Araştırma Hastanesi'], 'institution' => ['tr' => 'I. Anesteziyoloji ve Reanimasyon Kliniği, Ağrı Bölümü']],
                            ['year' => '2012', 'title' => ['tr' => 'Maltepe Üniversitesi'], 'institution' => ['tr' => 'Öğretim üyesi, Algoloji (Ağrı) Bölümü sorumlusu']],
                        ],
                    ],
                    [
                        'label' => ['tr' => 'Uzmanlık sonrası eğitimler'],
                        'items' => [
                            ['year' => 'Haziran 2006', 'title' => ['tr' => 'Kadavra üzerinde rejyonal anestezi teknikleri'], 'institution' => ['tr' => null]],
                            ['year' => 'Haziran – Kasım 2006', 'title' => ['tr' => 'Algoloji (ağrı) eğitimi'], 'institution' => ['tr' => null]],
                            ['year' => 'Mayıs 2008', 'title' => ['tr' => 'Vertebroplasti eğitimi'], 'institution' => ['tr' => 'Cerrahpaşa Tıp Fakültesi, Nöroşirürji Bölüm Başkanlığı']],
                            ['year' => 'Haziran 2008', 'title' => ['tr' => 'Ultrason eşliğinde rejyonal bloklar'], 'institution' => ['tr' => null]],
                            ['year' => '2010', 'title' => ['tr' => 'Dorsal kolon stimülasyonu (DCS)'], 'institution' => ['tr' => 'İstanbul']],
                            ['year' => 'Mart 2011', 'title' => ['tr' => 'Dorsal kolon stimülasyonu (DCS)'], 'institution' => ['tr' => 'Amsterdam, Hollanda']],
                            ['year' => 'Eylül 2012', 'title' => ['tr' => 'Epiduroskopi'], 'institution' => ['tr' => 'Venedik, İtalya']],
                        ],
                    ],
                ],
            ]),

            $this->block(null, 'treatment_index', [
                'eyebrow' => ['tr' => 'III — Uzmanlık alanları'],
                'title' => ['tr' => 'Uzmanlık'],
                'accent' => ['tr' => 'alanlarım'],
                'lead' => ['tr' => 'Her bel ağrısının sebebi fıtık değildir. Doğru teşhis, doğru tedavinin ilk adımıdır.'],
                'pain_type_ids' => $this->treatmentIds(self::PAIN_TYPES),
                'show_procedures' => true,
                'procedures_label' => ['tr' => 'Girişimsel tedaviler'],
                'procedure_ids' => $this->treatmentIds(self::PROCEDURE_CHIPS),
            ]),

            $this->block(null, 'publications_link', [
                'eyebrow' => ['tr' => 'V — Akademik çalışmalar'],
                'title' => ['tr' => '28 bilimsel yayın'],
                'lead' => ['tr' => 'Ulusal ve uluslararası hakemli dergilerde yayımlanmış akademik çalışmalar. Yayınların güncel listesi Google Scholar profilindedir.'],
                'link_label' => ['tr' => 'Google Scholar profili'],
            ]),

            $this->block(null, 'media_strip', [
                'use_settings' => true,
            ]),

            $this->block(null, 'cta_band', [
                'eyebrow' => ['tr' => 'Randevu'],
                'title' => ['tr' => 'Size özel'],
                'accent' => ['tr' => 'tedavi planı için iletişime geçin.'],
                'lead' => ['tr' => 'Randevu talebinizi iletin; sizi arayarak uygun gün ve saati birlikte belirleyelim.'],
                'show_whatsapp' => true,
                'show_phone' => true,
            ]),
        ], 'Hakkimda.dc.html bölüm sırası.');
    }

    /**
     * İletişim ve randevu: page title, every way to reach the practice and the
     * appointment form, which closes the page.
     */
    private function composeContact(): void
    {
        $page = $this->page('iletisim');

        if (! $page) {
            return;
        }

        $this->save($page, [
            $this->block(null, 'hero', [
                'variant' => 'page',
                'eyebrow' => ['tr' => 'Algoloji randevusu'],
                'title' => ['tr' => 'İletişim ve'],
                'accent' => ['tr' => 'Randevu'],
                'lead' => ['tr' => 'Algoloji randevusu almak ve bize ulaşmak için aşağıdaki seçenekleri kullanabilirsiniz: online randevu, telefonla randevu ya da diğer iletişim kanalları.'],
                'image' => $this->photo('klinik.jpg'),
                'show_cta' => true,
                'show_phone' => true,
            ]),

            $this->block(null, 'contact_details', [
                'eyebrow' => ['tr' => 'I — İletişim bilgileri'],
                'title' => ['tr' => 'Kadıköy, Bağdat Caddesi'],
                'show_map' => true,
                'map_label' => ['tr' => 'Bağdat Caddesi · Göztepe'],
                'map_area' => ['tr' => 'Kadıköy – İstanbul'],
            ]),

            $this->block(null, 'appointment', [
                'eyebrow' => ['tr' => 'II — Randevu talebi'],
                'title' => ['tr' => 'Randevunuzu'],
                'accent' => ['tr' => 'birlikte planlayalım.'],
                'lead' => ['tr' => 'Formu doldurun, sizi arayarak uygun gün ve saati birlikte belirleyelim. Dilerseniz WhatsApp veya telefonla da ulaşabilirsiniz.'],
                'show_contact_lines' => false,
            ]),
        ], 'Iletisim.dc.html bölüm sırası; sayfa randevu bölümüyle bitiyor.');
    }

    /**
     * Yurt Dışı Hastalar: the three imported service packages become one
     * three-card section, followed by the services accordion, the conditions
     * list and the WhatsApp band.
     */
    private function composeInternationalPatients(): void
    {
        $page = $this->page('yurt-disi-hastalar');

        if (! $page) {
            return;
        }

        $imported = $this->blocksOf($page);

        $this->save($page, [
            $this->block($this->pick($imported, 'hero'), 'hero', [
                'variant' => 'facts',
                'eyebrow' => ['tr' => 'Yurt dışı hastalar'],
                'title' => ['tr' => 'Yurt dışı hastalara özel'],
                'accent' => ['tr' => 'ameliyatsız fıtık tedavisi'],
                'lead' => ['tr' => 'Konaklamadan karşılamaya tam kapasite özel hizmet.'],
                'show_cta' => true,
                'show_phone' => true,
                'facts' => [
                    ['value' => ['tr' => '30'], 'label' => ['tr' => 'yıllık hekimlik deneyimi']],
                    ['value' => ['tr' => '11.700+'], 'label' => ['tr' => 'hasta']],
                    ['value' => ['tr' => '27'], 'label' => ['tr' => 'girişimsel teknik']],
                    ['value' => ['tr' => '28'], 'label' => ['tr' => 'bilimsel yayın']],
                ],
            ]),

            $this->block(null, 'packages', [
                'eyebrow' => ['tr' => 'I — Hizmet paketleri'],
                'title' => ['tr' => 'Yurt dışı hastalara özel'],
                'accent' => ['tr' => 'paketler'],
                'lead' => ['tr' => 'Her pakette Prof. Dr. Hüsnü Süslü ile tıbbi konsültasyon yer alır. Ayrıntılar için WhatsApp ile yazın.'],
                'items' => $this->packageItems($imported),
            ]),

            $this->block($this->plainAnswers($this->pick($imported, 'faq')), 'faq', [
                'eyebrow' => ['tr' => 'II — Hizmetlerimiz'],
                'title' => ['tr' => 'Her hizmet,'],
                'accent' => ['tr' => 'ayrıntısıyla'],
            ]),

            $this->block($this->pick($imported, 'treatment_index'), 'treatment_index', [
                'eyebrow' => ['tr' => 'III — Hizmet verilen diğer konular'],
                'title' => ['tr' => 'Hizmet verilen diğer'],
                'accent' => ['tr' => 'konular'],
                'lead' => ['tr' => 'Her ağrının sebebi fıtık değildir. Doğru teşhis için uzman bir hekimden destek almalısınız.'],
                'pain_type_ids' => $this->treatmentIds(self::PAIN_TYPES),
                'show_procedures' => false,
            ]),

            $this->block(null, 'cta_band', [
                'eyebrow' => ['tr' => 'IV — Yurt dışından iletişim'],
                'title' => ['tr' => 'Yurt dışından'],
                'accent' => ['tr' => 'WhatsApp ile yazın.'],
                'lead' => ['tr' => 'Randevu ve bilgi için klinik telefonundan ya da e-posta ile de ulaşabilirsiniz.'],
                'show_whatsapp' => true,
                'show_phone' => true,
            ]),
        ], 'YurtDisi.dc.html bölüm sırası; üç paket bloğu tek bölümde birleştirildi.');
    }

    /**
     * The featured procedure page, following TedaviDetay.dc.html.
     */
    private function composeNucleoplasty(): void
    {
        $treatment = $this->treatment('nukleoplasti-islemi');

        if (! $treatment) {
            return;
        }

        $imported = $this->blocksOf($treatment);

        $this->save($treatment, [
            $this->block(null, 'hero', [
                'variant' => 'facts',
                'eyebrow' => ['tr' => 'Ameliyatsız fıtık tedavisi'],
                'title' => ['tr' => 'Fıtık tedavisinde'],
                'accent' => ['tr' => 'nükleoplasti.'],
                'lead' => ['tr' => 'Bel ve boyun fıtıklarında uygulanan, cerrahi kesi gerektirmeyen girişimsel bir ağrı tedavi yöntemi. Disk içindeki basıncı azaltarak sinir üzerindeki baskıyı hafifletmeyi amaçlar.'],
                'show_cta' => true,
                'show_phone' => true,
                'facts' => [
                    ['value' => ['tr' => 'Lokal'], 'label' => ['tr' => 'anestezi, bilinç açık']],
                    ['value' => ['tr' => '20–30 dk'], 'label' => ['tr' => 'ortalama işlem süresi']],
                    ['value' => ['tr' => '2–4 saat'], 'label' => ['tr' => 'içinde taburcu']],
                    ['value' => ['tr' => 'Kesi yok'], 'label' => ['tr' => 'dikiş ve yara izi bulunmaz']],
                ],
            ]),

            $this->block($this->pickText($imported, 'gelişmiş teknolojilerden biri olan nükleoplasti'), 'rich_text', [
                'background' => 'ivory',
                'eyebrow' => ['tr' => 'I — Genel bilgi'],
                'title' => ['tr' => 'Nükleoplasti nedir?'],
            ]),

            $this->block($this->pickText($imported, 'aynı gün taburcu olup hayatınıza'), 'rich_text', [
                'background' => 'paper',
                'eyebrow' => ['tr' => 'İyileşme'],
                'title' => ['tr' => '2–4 saatte taburcu'],
            ]),

            $this->block($this->pick($imported, 'two_column_lists'), 'two_column_lists', [
                'eyebrow' => ['tr' => 'II — Hasta seçimi'],
                'title' => ['tr' => 'Kimlere uygulanır, kimlere uygulanmaz?'],
                'lead' => ['tr' => 'Nükleoplasti her bel ve boyun fıtığı hastası için uygun değildir. Bu nedenle işlem öncesi detaylı muayene ve görüntüleme ile hasta değerlendirmesi mutlaka yapılır.'],
                'positive_label' => ['tr' => 'Uygulanır'],
                'positive_title' => ['tr' => 'Kimlere uygulanır?'],
                'positive_items' => ['tr' => "Hafif ve orta düzeyde bel veya boyun fıtığı olanlar\nAğrısı ilaç veya fizik tedaviyle geçmeyen hastalar\nAmeliyat gerektirmeyen, uygun bulunan fıtık vakaları"],
                'positive_note' => ['tr' => 'Tedavi öncesi MR ve fizik muayene ile değerlendirme yapılır.'],
                'negative_label' => ['tr' => 'Uygulanmaz'],
                'negative_title' => ['tr' => 'Kimlere uygulanmaz?'],
                'negative_items' => ['tr' => "İleri derecede fıtığı olanlar\nCiddi sinir hasarı (sinir kaybı) bulunanlar\nOmurga stabilitesini etkileyen durumlar ya da farklı omurga problemleri olanlar"],
                'negative_note' => ['tr' => 'Bu durumlarda alternatif tedavi yöntemleri değerlendirilir.'],
            ]),

            $this->block(null, 'process_steps', [
                'eyebrow' => ['tr' => 'III — Uygulama'],
                'title' => ['tr' => 'İşlem nasıl'],
                'accent' => ['tr' => 'yapılır?'],
                'lead' => ['tr' => 'İşlem, görüntüleme eşliğinde özel bir iğne yardımıyla disk içine girilerek yapılır. Disk içindeki fazla basınç kontrollü şekilde azaltılır.'],
                'image' => $this->photo('ameliyathane.jpg'),
                'steps' => [
                    ['title' => ['tr' => 'Değerlendirme'], 'text' => ['tr' => 'Tedavi öncesi MR ve fizik muayene ile fıtığınızın bu yönteme uygunluğu değerlendirilir.']],
                    ['title' => ['tr' => 'Lokal anestezi'], 'text' => ['tr' => 'İşlem genellikle lokal anestezi altında, bilinciniz açıkken yapılır; genel anesteziye gerek yoktur.']],
                    ['title' => ['tr' => 'Radyofrekans uygulaması'], 'text' => ['tr' => 'Cilt üzerinden çok ince bir iğneyle diske girilir; radyofrekans probuyla diskin iç kısmı ısıtılarak küçültülür.']],
                    ['title' => ['tr' => 'Aynı gün taburcu'], 'text' => ['tr' => 'İşlem ortalama 20–30 dakika sürer; hasta 2–4 saat içinde taburcu olabilir.']],
                ],
            ]),

            $this->block($this->pick($imported, 'video_grid'), 'video_grid', [
                'eyebrow' => ['tr' => 'IV — Video'],
                'title' => ['tr' => 'Nükleoplasti işlemi'],
                'accent' => ['tr' => 'nedir?'],
                'source' => 'selected',
                'video_ids' => $this->videoIds(['obBgAWjzUpQ']),
                'show_featured' => true,
            ]),

            $this->block($this->plainAnswers($this->pick($imported, 'faq')), 'faq', [
                'eyebrow' => ['tr' => 'V — Sorular'],
                'title' => ['tr' => 'Nükleoplasti hakkında'],
                'accent' => ['tr' => 'sıkça sorulan sorular'],
                'lead' => ['tr' => 'Hastalar tarafından sıkça sorulan sorular ve cevapları.'],
                'link_label' => ['tr' => 'Sorunuz mu var? WhatsApp ile yazın'],
                'link_url' => $this->whatsappUrl(),
            ]),

            $this->block(null, 'doctor_profile', $this->doctorProfile([
                'eyebrow' => ['tr' => 'Hekiminiz'],
                'text' => ['tr' => '30 yıllık hekimlik deneyimi. İstanbul Üniversitesi Cerrahpaşa Tıp Fakültesi; Anesteziyoloji ve Reanimasyon uzmanlığı, Algoloji yan dalı.'],
            ])),

            $this->block(null, 'cta_band', [
                'eyebrow' => ['tr' => 'Randevu'],
                'title' => ['tr' => 'Size uygun tedaviyi'],
                'accent' => ['tr' => 'birlikte belirleyelim.'],
                'lead' => ['tr' => 'Nükleoplastinin size uygun olup olmadığı, muayene ve görüntüleme sonrası belirlenir. Randevu talebinizi iletin; sizi arayarak uygun gün ve saati birlikte belirleyelim.'],
                'show_whatsapp' => true,
                'show_phone' => true,
            ]),
        ], 'TedaviDetay.dc.html bölüm sırası.');
    }

    /**
     * The sample pain type page, following AgriTuru.dc.html.
     */
    private function composeBackAndLegPain(): void
    {
        $treatment = $this->treatment('bel-ve-bacak-agrilari');

        if (! $treatment) {
            return;
        }

        $imported = $this->blocksOf($treatment);

        $this->save($treatment, [
            $this->block(null, 'hero', [
                'variant' => 'page',
                'eyebrow' => ['tr' => 'Ağrı türleri — 01'],
                'title' => ['tr' => 'Bel ve bacak'],
                'accent' => ['tr' => 'ağrıları.'],
                'lead' => ['tr' => 'Günlük yaşamda oldukça yaygın görülen ve birçok farklı nedenden kaynaklanabilen bir sorun. Ağrının tipi, şiddeti ve süresi altta yatan nedene göre değişiklik gösterebilir.'],
                'image' => $this->photo('klinik.jpg'),
                'show_cta' => true,
                'show_phone' => true,
            ]),

            /*
             * The imported article opens with its own second-level heading, so
             * the section only needs the label above it.
             */
            $this->block($this->pickText($imported, 'Bel ve Bacak Ağrılarının Nedenleri'), 'rich_text', [
                'background' => 'ivory',
                'eyebrow' => ['tr' => 'I — Genel bakış'],
                'title' => null,
                'show_toc' => false,
            ]),

            $this->block(null, 'related_treatments', [
                'eyebrow' => ['tr' => 'V — Girişimsel tedaviler'],
                'title' => ['tr' => 'Girişimsel tedavi'],
                'accent' => ['tr' => 'seçenekleri'],
                'link_label' => ['tr' => 'Tüm girişimsel tedaviler'],
                'link_url' => $this->route('tr.procedures.index'),
                'source' => 'selected',
                'treatment_ids' => $this->treatmentIds([
                    'epidural-steroid-uygulamasi',
                    'faset-eklem-enjeksiyonlari',
                    'sempatik-bloklar',
                    'radyofrekans-uygulamalari',
                    'nukleoplasti-islemi',
                    'ameliyatsiz-lazerle-bel-fitigi-tedavisi',
                ]),
            ]),

            $this->block(null, 'cta_band', [
                'eyebrow' => ['tr' => 'Randevu'],
                'title' => ['tr' => 'Ağrınızın kaynağını'],
                'accent' => ['tr' => 'birlikte bulalım.'],
                'lead' => ['tr' => 'Bel ve bacak ağrılarınızda doğru teşhis, doğru tedavinin ilk adımıdır. Randevu talebinizi iletin; sizi arayarak uygun gün ve saati birlikte belirleyelim.'],
                'show_whatsapp' => true,
                'show_phone' => true,
            ]),
        ], 'AgriTuru.dc.html bölüm sırası.');
    }

    /**
     * Writes the composed blocks back, clears the review flag and records what
     * happened, replacing this seeder's previous note instead of stacking one.
     *
     * @param  array<int, array{type: string, data: array<string, mixed>}|null>  $blocks
     */
    private function save(Model $record, array $blocks, string $summary): void
    {
        $record->forceFill([
            'blocks' => array_values(array_filter($blocks)),
            'needs_review' => false,
            'import_notes' => $this->notes($record->import_notes, $summary),
        ])->save();

        $this->command?->info("Düzenlendi: {$record->localized('title')}");
    }

    private function notes(?string $notes, string $summary): string
    {
        $lines = array_values(array_filter(
            preg_split('/\R/', trim((string) $notes)) ?: [],
            fn (string $line): bool => filled(trim($line)) && ! str_starts_with($line, self::NOTE_PREFIX),
        ));

        $lines[] = self::NOTE_PREFIX.' '.$summary;

        return implode("\n", $lines);
    }

    /**
     * @return array<int, array{type: string, data: array<string, mixed>}>
     */
    private function blocksOf(Model $record): array
    {
        return array_values(array_filter(
            (array) $record->blocks,
            fn ($block): bool => is_array($block) && filled($block['type'] ?? null),
        ));
    }

    /**
     * Builds a block, keeping the data of the imported one it replaces.
     *
     * @param  array{type: string, data: array<string, mixed>}|null  $imported
     * @param  array<string, mixed>  $overrides
     * @return array{type: string, data: array<string, mixed>}
     */
    private function block(?array $imported, string $type, array $overrides): array
    {
        return [
            'type' => $type,
            'data' => $this->merge((array) ($imported['data'] ?? []), $overrides),
        ];
    }

    /**
     * Overrides block data, merging translations so a language this seeder does
     * not write keeps the value the import gave it.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function merge(array $data, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            $data[$key] = $this->isTranslation($value) && $this->isTranslation($data[$key] ?? null)
                ? array_replace($data[$key], array_filter($value, 'filled'))
                : $value;
        }

        return $data;
    }

    /**
     * Whether a value is a locale => text map rather than a plain list.
     */
    private function isTranslation(mixed $value): bool
    {
        if (! is_array($value) || $value === []) {
            return false;
        }

        foreach (array_keys($value) as $key) {
            if (! is_string($key) || ! array_key_exists($key, (array) config('locales.locales'))) {
                return false;
            }
        }

        return true;
    }

    /**
     * The first imported block of a type.
     *
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $blocks
     * @return array{type: string, data: array<string, mixed>}|null
     */
    private function pick(array $blocks, string $type): ?array
    {
        foreach ($blocks as $block) {
            if ($block['type'] === $type) {
                return $block;
            }
        }

        return null;
    }

    /**
     * The imported text block whose body contains a phrase. Bodies are never
     * rewritten, so the same phrase still finds the block on a rerun.
     *
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $blocks
     * @return array{type: string, data: array<string, mixed>}|null
     */
    private function pickText(array $blocks, string $phrase): ?array
    {
        foreach ($blocks as $block) {
            if ($block['type'] !== 'rich_text') {
                continue;
            }

            foreach ((array) ($block['data']['body'] ?? []) as $body) {
                if (is_string($body) && str_contains($body, $phrase)) {
                    return $block;
                }
            }
        }

        return null;
    }

    /**
     * The import stored the answers of a question block as HTML, but the field
     * is plain text and the view escapes it, so the markup showed up on screen.
     * Unwrapping it keeps every word and reads the same on a rerun.
     *
     * @param  array{type: string, data: array<string, mixed>}|null  $block
     * @return array{type: string, data: array<string, mixed>}|null
     */
    private function plainAnswers(?array $block): ?array
    {
        if (! $block) {
            return null;
        }

        foreach ((array) ($block['data']['items'] ?? []) as $index => $item) {
            foreach ((array) ($item['answer'] ?? []) as $locale => $answer) {
                if (is_string($answer)) {
                    $block['data']['items'][$index]['answer'][$locale] = $this->unwrap($answer);
                }
            }
        }

        return $block;
    }

    private function unwrap(string $html): string
    {
        $text = preg_replace('#<(br|/p|/li|/div|/h[1-6])\b[^>]*>#i', "\n", $html) ?? $html;
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $lines = array_values(array_filter(array_map(
            fn (string $line): string => trim(preg_replace('/\s+/u', ' ', $line) ?? $line),
            preg_split('/\R/', $text) ?: [],
        )));

        return implode("\n", $lines);
    }

    /**
     * The items of every imported package block, so three one-card sections
     * become the mockup's single three-card section without losing a package.
     *
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $blocks
     * @return array<int, array<string, mixed>>
     */
    private function packageItems(array $blocks): array
    {
        $items = [];

        foreach ($blocks as $block) {
            if ($block['type'] !== 'packages') {
                continue;
            }

            foreach ((array) ($block['data']['items'] ?? []) as $item) {
                $item = (array) $item;
                $item['eyebrow'] ??= $block['data']['title'] ?? null;

                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * The physician card, which every mockup shows with the same portrait,
     * name, title and links.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function doctorProfile(array $overrides): array
    {
        return $overrides + [
            'image' => $this->photo('portre.jpg'),
            'name' => ['tr' => 'Prof. Dr. Hüsnü Süslü'],
            'role' => ['tr' => 'Algoloji ve Anesteziyoloji Profesörü'],
            'primary_label' => ['tr' => 'Özgeçmişin tamamı'],
            'primary_url' => $this->pageUrl('hakkimda'),
            'secondary_label' => ['tr' => 'Yayınlar · Google Scholar'],
            'secondary_url' => app(SiteSettings::class)->scholar_url ?: null,
        ];
    }

    private function page(string $slug): ?Page
    {
        return Page::query()->where('slug_tr', $slug)->first();
    }

    private function treatment(string $slug): ?Treatment
    {
        return Treatment::query()->where('slug_tr', $slug)->first();
    }

    /**
     * @param  array<int, string>  $slugs
     * @return array<int, int>
     */
    private function treatmentIds(array $slugs): array
    {
        $ids = Treatment::query()
            ->whereIn('slug_tr', $slugs)
            ->pluck('id', 'slug_tr')
            ->all();

        return array_values(array_filter(array_map(
            fn (string $slug): ?int => isset($ids[$slug]) ? (int) $ids[$slug] : null,
            $slugs,
        )));
    }

    /**
     * @param  array<int, string>  $youtubeIds
     * @return array<int, int>
     */
    private function videoIds(array $youtubeIds): array
    {
        $ids = Video::query()
            ->whereIn('youtube_id', $youtubeIds)
            ->pluck('id', 'youtube_id')
            ->all();

        return array_values(array_filter(array_map(
            fn (string $youtubeId): ?int => isset($ids[$youtubeId]) ? (int) $ids[$youtubeId] : null,
            $youtubeIds,
        )));
    }

    private function treatmentUrl(string $slug): ?string
    {
        $treatment = $this->treatment($slug);

        if (! $treatment || blank($treatment->slugFor('tr'))) {
            return null;
        }

        return $this->route('tr.'.$treatment->kind->routeName(), ['slug' => $treatment->slugFor('tr')]);
    }

    private function pageUrl(string $slug): ?string
    {
        return $this->page($slug) ? $this->route('tr.page', ['slug' => $slug]) : null;
    }

    /**
     * Root-relative URLs, so the composed blocks do not carry the host the
     * seeder happened to run under.
     *
     * @param  array<string, mixed>  $parameters
     */
    private function route(string $name, array $parameters = []): ?string
    {
        return Route::has($name)
            ? route($name, $parameters, absolute: false)
            : null;
    }

    private function whatsappUrl(): string
    {
        return 'https://wa.me/'.preg_replace('/\D/', '', app(SiteSettings::class)->whatsapp_phone);
    }

    /**
     * A photograph from `storage/app/public/site`, as the blocks expect it: a
     * path on the public disk.
     */
    private function photo(string $file): ?string
    {
        $path = 'site/'.$file;

        return Storage::disk('public')->exists($path) ? $path : null;
    }
}
