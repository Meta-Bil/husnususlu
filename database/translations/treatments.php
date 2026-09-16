<?php

/*
|--------------------------------------------------------------------------
| Treatment translations
|--------------------------------------------------------------------------
|
| Pain types and interventional procedures, keyed by the Turkish slug. The
| structure is the same as pages.php; see the notes there.
|
| Three records carry `publish => false`. Their Turkish text states something
| clinically wrong, so the translation must not go live in another language
| until the Turkish is corrected. Each one says which sentence is at fault.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Pain types
    |--------------------------------------------------------------------------
    */

    'bas-ve-yuz-agrilari' => [
        'fields' => [
            'title' => ['ru' => 'Боль в голове и лице'],
            'slug' => ['ru' => 'bol-v-golove-i-lice'],
            'summary' => ['ru' => 'Боль в голове и лице: подробный обзор. Боль в голове и лице — частая в повседневной жизни проблема, у которой может быть множество разных причин. Характер, интенсивность и длительность боли меняются в зависимости от основной причины…'],
            'seo_title' => ['ru' => 'Боль в голове и лице — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Причины, диагностика и методы лечения головной и лицевой боли, включая мигрень и невралгию тройничного нерва.'],
        ],
    ],

    'batin-ve-pelvik-agrilar' => [
        'fields' => [
            'title' => ['ru' => 'Боль в животе и тазовой области'],
            'slug' => ['ru' => 'bol-v-zhivote-i-tazu'],
            'summary' => ['ru' => 'Боль в животе и тазовой области — это боль, которая ощущается в области живота и паха и может быть вызвана множеством разных причин. Характер, интенсивность и длительность боли меняются в зависимости от основной причины…'],
            'seo_title' => ['ru' => 'Боль в животе и тазовой области — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Частые причины, диагностика и интервенционные методы лечения хронической боли в животе и тазовой области.'],
        ],
    ],

    'bel-ve-bacak-agrilari' => [
        'fields' => [
            'title' => ['ru' => 'Боль в пояснице и ноге'],
            'slug' => ['ru' => 'bol-v-poyasnice-i-noge'],
            'summary' => ['ru' => 'Боль в пояснице и ноге — очень распространённая в повседневной жизни проблема, у которой может быть множество разных причин. Характер, интенсивность и длительность боли меняются в зависимости от основной причины…'],
            'seo_title' => ['ru' => 'Боль в пояснице и ноге — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Боль в пояснице и ноге: причины — грыжа межпозвонкового диска и ишиас, — диагностика и безоперационные методы лечения в Стамбуле.'],
        ],
        'blocks' => [
            'Ağrı türleri — 01' => ['en' => 'Types of pain — 01', 'ru' => 'Виды боли — 01'],
            'Bel ve bacak' => ['en' => 'Back and leg', 'ru' => 'Боль в пояснице'],
            'ağrıları.' => ['en' => 'pain.', 'ru' => 'и ноге.'],
            'Günlük yaşamda oldukça yaygın görülen ve birçok farklı nedenden kaynaklanabilen bir sorun. Ağrının tipi, şiddeti ve süresi altta yatan nedene göre değişiklik gösterebilir.' => [
                'en' => 'A very common problem in everyday life that can have many different causes. The type, severity and duration of the pain vary with the underlying cause.',
                'ru' => 'Очень распространённая в повседневной жизни проблема, у которой может быть множество разных причин. Характер, интенсивность и длительность боли зависят от основной причины.',
            ],
            'I — Genel bakış' => ['en' => 'I — Overview', 'ru' => 'I — Общий обзор'],
            'V — Girişimsel tedaviler' => ['en' => 'V — Interventional treatments', 'ru' => 'V — Интервенционное лечение'],
            'Girişimsel tedavi' => ['en' => 'Interventional treatment', 'ru' => 'Варианты интервенционного'],
            'seçenekleri' => ['en' => 'options', 'ru' => 'лечения'],
            'Tüm girişimsel tedaviler' => [
                'en' => 'All interventional treatments',
                'ru' => 'Все методы интервенционного лечения',
            ],
            'Ağrınızın kaynağını' => ['en' => 'Let us find the source of your pain', 'ru' => 'Найдём источник вашей боли'],
            'birlikte bulalım.' => ['en' => 'together.', 'ru' => 'вместе.'],
            'Bel ve bacak ağrılarınızda doğru teşhis, doğru tedavinin ilk adımıdır. Randevu talebinizi iletin; sizi arayarak uygun gün ve saati birlikte belirleyelim.' => [
                'en' => 'With back and leg pain, an accurate diagnosis is the first step of the right treatment. Send us your appointment request and we will call you so we can agree on a day and a time together.',
                'ru' => 'При боли в пояснице и ноге точный диагноз — первый шаг правильного лечения. Оставьте заявку на приём: мы перезвоним и вместе подберём удобные день и время.',
            ],
        ],
    ],

    'boyun-servikal-agrilari' => [
        'fields' => [
            'title' => ['ru' => 'Боль в шее (шейный отдел)'],
            'slug' => ['ru' => 'bol-v-shee'],
            'summary' => ['ru' => 'Боль в шее — распространённая проблема, с которой многие сталкиваются в тот или иной момент жизни. Боль может быть острой или тупой и усиливаться при движениях шеи. У боли в шее много возможных причин…'],
            'seo_title' => ['ru' => 'Боль в шее (шейный отдел) — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Причины боли в шее — от мышечного напряжения до грыжи межпозвонкового диска шейного отдела — и безоперационные методы лечения.'],
        ],
    ],

    'fibromiyalji' => [
        'fields' => [
            'title' => ['ru' => 'Фибромиалгия'],
            'slug' => ['ru' => 'fibromialgiya'],
            'summary' => ['ru' => 'Фибромиалгия — хроническое заболевание, для которого характерны распространённая боль в мышцах и суставах, утомляемость и нарушения сна. Точная причина фибромиалгии неизвестна, однако считается, что дело в изменениях работы нервной и иммунной систем…'],
            'seo_title' => ['ru' => 'Фибромиалгия — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Фибромиалгия вызывает распространённую мышечную боль, утомляемость и нарушения сна. Симптомы, диагностика и методы лечения.'],
        ],
    ],

    'kanser-agrilari' => [
        'fields' => [
            'title' => ['ru' => 'Онкологическая боль'],
            'slug' => ['ru' => 'onkologicheskaya-bol'],
            'summary' => ['ru' => 'Онкологическая боль — один из самых частых симптомов у онкологических пациентов, способный заметно снижать качество жизни. Характер и интенсивность боли зависят от вида и стадии опухоли и от индивидуальных особенностей пациента…'],
            'seo_title' => ['ru' => 'Онкологическая боль — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Причины онкологической боли и современные методы лечения — медикаментозные и интервенционные — для улучшения качества жизни.'],
        ],
    ],

    'miyofasiyal-agri-sendromu' => [
        'fields' => [
            'title' => ['ru' => 'Миофасциальный болевой синдром'],
            'slug' => ['ru' => 'miofascialnyy-bolevoy-sindrom'],
            'summary' => ['ru' => 'Миофасциальный болевой синдром (МФБС) — хронический болевой синдром, вызванный напряжёнными участками в мышцах и мышечной фасции (оболочке, окружающей мышцы), которые называют болезненными и триггерными точками…'],
            'seo_title' => ['ru' => 'Миофасциальный болевой синдром — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Миофасциальный болевой синдром вызывают триггерные точки в мышцах. Симптомы, диагностика и лечение, включая инъекции в триггерные точки.'],
        ],
    ],

    'noropatik-agrilar' => [
        'fields' => [
            'title' => ['ru' => 'Нейропатическая боль'],
            'slug' => ['ru' => 'neyropaticheskaya-bol'],
            'summary' => ['ru' => 'Нейропатическая боль — вид боли, возникающий из-за повреждения или нарушения работы нервной системы. Такое повреждение может быть вызвано разными причинами: диабетом, онкологическим заболеванием, травмой, инфекцией или аутоиммунными болезнями…'],
            'seo_title' => ['ru' => 'Нейропатическая боль — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Нейропатическая боль возникает при повреждении нервов из-за диабета, травмы или инфекции. Симптомы и методы лечения.'],
        ],
    ],

    'omuz-kol-agrilari' => [
        'fields' => [
            'title' => ['ru' => 'Боль в плече и руке'],
            'slug' => ['ru' => 'bol-v-pleche-i-ruke'],
            'summary' => ['ru' => 'Боль в плече и руке — весьма распространённая проблема, которая встречается в любом возрасте. Боль может быть вызвана нарушением в плечевом суставе, мышцах руки или нервах…'],
            'seo_title' => ['ru' => 'Боль в плече и руке — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Частые причины боли в плече и руке — повреждение вращательной манжеты, компрессия нервов — и доступные методы лечения.'],
        ],
    ],

    'sirt-ve-gogus-agrilari' => [
        'fields' => [
            'title' => ['ru' => 'Боль в спине и грудной клетке'],
            'slug' => ['ru' => 'bol-v-spine-i-grudi'],
            'summary' => ['ru' => 'Боль в спине и грудной клетке — весьма распространённая проблема, которая встречается в любом возрасте. Боль может быть вызвана нарушением в мышцах, костях, суставах или нервах…'],
            'seo_title' => ['ru' => 'Боль в спине и грудной клетке — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Причины боли в верхней части спины и грудной стенке, связанные с мышцами, суставами и нервами, их диагностика и лечение.'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Interventional procedures
    |--------------------------------------------------------------------------
    */

    'vertebroplasti' => [
        'fields' => [
            'title' => ['ru' => 'Вертебропластика'],
            'slug' => ['ru' => 'vertebroplastika'],
            'summary' => ['ru' => 'Вертебропластика — малоинвазивная хирургическая процедура, которая применяется для стабилизации сломанных или просевших позвонков и уменьшения боли. При этой процедуре в костную часть позвоночника (позвонок) вводят костный…'],
            'seo_title' => ['ru' => 'Вертебропластика — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Вертебропластика стабилизирует болезненные компрессионные переломы позвонков с помощью костного цемента. Малоинвазивная процедура.'],
        ],
    ],

    'tetik-nokta-enjeksiyonlari' => [
        'fields' => [
            'title' => ['ru' => 'Инъекции в триггерные точки'],
            'slug' => ['ru' => 'inekcii-v-triggernye-tochki'],
            'summary' => ['ru' => 'Инъекции в триггерные точки — метод лечения болезненных и напряжённых участков в мышцах и мышечной фасции (оболочке, окружающей мышцы), которые называют триггерными точками…'],
            'seo_title' => ['ru' => 'Инъекции в триггерные точки — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Инъекции в триггерные точки снимают мышечные уплотнения и миофасциальную боль с помощью местных анестетиков: показания, ход процедуры и восстановление.'],
        ],
    ],

    /*
     * HELD BACK. The Turkish summary and body say the spinal port is used for
     * injections of "ilaç ve kemik çimentosu" — a port delivers medication only;
     * bone cement belongs to vertebroplasty. The Turkish also says in one
     * sentence that the port is placed in the spine and in the next that it is
     * placed under the skin. Correct the Turkish, then remove `publish`.
     */
    'spinal-port-uygulamasi' => [
        'publish' => false,
        'fields' => [
            'title' => ['en' => 'Spinal Port', 'ru' => 'Спинальная порт-система'],
            'slug' => ['en' => 'spinal-port', 'ru' => 'spinalnaya-port-sistema'],
            'summary' => [
                'en' => 'Spinal port: a spinal port is a medical device used to make injections of medication and bone cement into the spine easier. The port is a small disc placed under the skin in a surgical procedure…',
                'ru' => 'Спинальная порт-система — медицинское устройство, которое облегчает введение препаратов и костного цемента в позвоночник. Порт представляет собой небольшой диск, который хирургическим путём размещают под кожей…',
            ],
            'seo_title' => ['ru' => 'Спинальная порт-система — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Спинальная порт-система позволяет доставлять обезболивающие препараты вблизи спинного мозга пациентам с тяжёлой хронической или онкологической болью.'],
        ],
    ],

    'sempatik-bloklar' => [
        'fields' => [
            'title' => ['ru' => 'Симпатические блокады'],
            'slug' => ['ru' => 'simpaticheskie-blokady'],
            'summary' => ['ru' => 'Симпатические блокады — метод лечения, при котором временно блокируется часть симпатической нервной системы. Симпатическая нервная система отвечает за реакцию «бей или беги» и влияет на частоту сердечных…'],
            'seo_title' => ['ru' => 'Симпатические блокады — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Симпатические блокады временно прерывают передачу сигналов по симпатическим нервам при таких состояниях, как комплексный регионарный болевой синдром и онкологическая боль.'],
        ],
    ],

    'radyofrekans-uygulamalari' => [
        'fields' => [
            'title' => ['ru' => 'Радиочастотные процедуры'],
            'slug' => ['ru' => 'radiochastotnye-procedury'],
            'summary' => ['ru' => 'Радиочастотные процедуры в алгологии: алгология — раздел медицины, который занимается диагностикой и лечением боли. Радиочастотные (РЧ) процедуры — малоинвазивный метод, применяемый в алгологии при хронической боли…'],
            'seo_title' => ['ru' => 'Радиочастотное лечение боли — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Радиочастотные (РЧ) процедуры — малоинвазивные вмешательства, которые воздействуют на проводящие боль нервы и помогают уменьшить хроническую боль.'],
        ],
    ],

    'faset-eklem-enjeksiyonlari' => [
        'fields' => [
            'title' => ['ru' => 'Инъекции в фасеточные суставы'],
            'slug' => ['ru' => 'inekcii-v-fasetochnye-sustavy'],
            'summary' => ['ru' => 'Инъекции в фасеточные суставы — метод лечения боли в пояснице и шее. Фасеточные суставы — небольшие суставы, расположенные позади позвонков. Эти суставы…'],
            'seo_title' => ['ru' => 'Инъекции в фасеточные суставы — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Инъекции в фасеточные суставы при боли в пояснице и шее, вызванной артрозом или дегенерацией мелких суставов позвоночника.'],
        ],
    ],

    'epidural-steroid-uygulamasi' => [
        'fields' => [
            'title' => ['ru' => 'Эпидуральная инъекция стероида'],
            'slug' => ['ru' => 'epiduralnaya-inekciya-steroida'],
            'summary' => ['ru' => 'Эпидуральная инъекция стероида — метод лечения различных болевых состояний, в том числе боли в пояснице и шее. При такой инъекции стероидные препараты вводят в пространство вокруг нервов позвоночника…'],
            'seo_title' => ['ru' => 'Эпидуральная инъекция стероида — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Эпидуральные инъекции стероидов уменьшают воспаление нервов и боль при грыже диска и стенозе позвоночного канала: показания, ход процедуры и восстановление.'],
        ],
    ],

    'epidural-kateter-uygulamasi' => [
        'fields' => [
            'title' => ['ru' => 'Эпидуральный катетер'],
            'slug' => ['ru' => 'epiduralnyy-kateter'],
            'summary' => ['ru' => 'Эпидуральный катетер — метод, при котором в поясничную область устанавливают катетер, позволяющий вводить препараты в эпидуральное пространство. Эпидуральное пространство — это пространство, окружённое костями позвоночника (позвонками)…'],
            'seo_title' => ['ru' => 'Эпидуральный катетер — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Как эпидуральный катетер доставляет препараты в эпидуральное пространство при острой, послеоперационной и хронической боли.'],
        ],
    ],

    /*
     * HELD BACK, and deliberately left without a translated summary or body.
     * The Turkish describes dorsal column stimulation as a treatment for
     * chronic constipation and lists bowel conditions as its indications. That
     * is a different therapy (sacral nerve stimulation); dorsal column
     * stimulation is a chronic-pain procedure. The Turkish needs a clinical
     * rewrite before this page exists in any other language.
     */
    'dorsal-kolon-stimulasyonu' => [
        'publish' => false,
        'fields' => [
            'title' => ['en' => 'Dorsal Column Stimulation', 'ru' => 'Стимуляция задних столбов спинного мозга'],
            'slug' => ['en' => 'dorsal-column-stimulation', 'ru' => 'stimulyaciya-zadnih-stolbov'],
        ],
    ],

    'nukleoplasti-islemi' => [
        'fields' => [
            'title' => ['en' => 'Nucleoplasty', 'ru' => 'Нуклеопластика'],
            'slug' => ['en' => 'nucleoplasty', 'ru' => 'nukleoplastika'],
            'summary' => [
                'en' => 'Regain your health with nucleoplasty, one of the advanced technologies in the treatment of disc herniation.',
                'ru' => 'Верните себе здоровье с помощью нуклеопластики — одной из передовых технологий в лечении грыжи межпозвонкового диска.',
            ],
            'seo_title' => [
                'en' => 'Nucleoplasty | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Нуклеопластика — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Prof. Dr. Hüsnü Süslü — 30 years of medical practice.',
                'ru' => 'Проф. д-р Хюсню Сюслю — 30 лет врачебной практики.',
            ],
        ],
        'blocks' => [
            'Fıtık tedavisinde' => [
                'en' => 'Nucleoplasty in the treatment of',
                'ru' => 'Нуклеопластика в лечении',
            ],
            'nükleoplasti.' => [
                'en' => 'a disc herniation.',
                'ru' => 'грыжи межпозвонкового диска.',
            ],
            'Bel ve boyun fıtıklarında uygulanan, cerrahi kesi gerektirmeyen girişimsel bir ağrı tedavi yöntemi. Disk içindeki basıncı azaltarak sinir üzerindeki baskıyı hafifletmeyi amaçlar.' => [
                'en' => 'An interventional pain treatment for lumbar and cervical disc herniation that requires no surgical incision. By lowering the pressure inside the disc it aims to relieve the pressure on the nerve.',
                'ru' => 'Интервенционный метод лечения боли при грыже поясничного и шейного отделов, не требующий хирургического разреза. Снижая давление внутри диска, он уменьшает давление на нерв.',
            ],
            'anestezi, bilinç açık' => [
                'en' => 'anaesthesia, you stay awake',
                'ru' => 'анестезия, вы в сознании',
            ],
            'ortalama işlem süresi' => ['en' => 'average procedure time', 'ru' => 'средняя длительность процедуры'],
            'içinde taburcu' => ['en' => 'until discharge', 'ru' => 'до выписки'],
            'Kesi yok' => ['en' => 'No incision', 'ru' => 'Без разреза'],
            'dikiş ve yara izi bulunmaz' => ['en' => 'no stitches and no scar', 'ru' => 'ни швов, ни рубца'],

            'I — Genel bilgi' => ['en' => 'I — General information', 'ru' => 'I — Общие сведения'],
            'Nükleoplasti nedir?' => ['en' => 'What is nucleoplasty?', 'ru' => 'Что такое нуклеопластика?'],
            /* The Turkish ends with a non-breaking space before the closing tag. */
            "<p>Fıtık tedavisinde gelişmiş teknolojilerden biri olan nükleoplasti işlemi ile sağlınıza kavuşun.\u{00A0}</p>" => [
                'en' => '<p>Regain your health with nucleoplasty, one of the advanced technologies in the treatment of disc herniation. </p>',
                'ru' => '<p>Верните себе здоровье с помощью нуклеопластики — одной из передовых технологий в лечении грыжи межпозвонкового диска. </p>',
            ],
            'İyileşme' => ['en' => 'Recovery', 'ru' => 'Восстановление'],
            '2–4 saatte taburcu' => ['en' => 'Discharged within 2–4 hours', 'ru' => 'Выписка через 2–4 часа'],
            "<p>Nükleoplasti işlemi sayesinde aynı gün taburcu olup hayatınıza kaldığınız yerden devam edebilirsiniz.\u{00A0}</p>" => [
                'en' => '<p>Nucleoplasty lets you be discharged the same day and pick your life up where you left off. </p>',
                'ru' => '<p>Благодаря нуклеопластике вы можете выписаться в тот же день и продолжить жизнь с того места, где остановились. </p>',
            ],

            'II — Hasta seçimi' => ['en' => 'II — Choosing the patient', 'ru' => 'II — Отбор пациентов'],
            'Kimlere uygulanır, kimlere uygulanmaz?' => [
                'en' => 'Who is it for, and who is it not for?',
                'ru' => 'Кому подходит, а кому — нет?',
            ],
            'Nükleoplasti her bel ve boyun fıtığı hastası için uygun değildir. Bu nedenle işlem öncesi detaylı muayene ve görüntüleme ile hasta değerlendirmesi mutlaka yapılır.' => [
                'en' => 'Nucleoplasty is not suitable for every patient with a lumbar or cervical disc herniation. The patient is therefore always assessed with a detailed examination and imaging before the procedure.',
                'ru' => 'Нуклеопластика подходит не каждому пациенту с грыжей поясничного или шейного диска. Поэтому перед процедурой пациента обязательно оценивают с помощью подробного осмотра и визуализации.',
            ],
            'Uygulanır' => ['en' => 'Suitable', 'ru' => 'Подходит'],
            'Kimlere uygulanır?' => ['en' => 'Who is it for?', 'ru' => 'Кому подходит?'],
            "Hafif ve orta düzeyde bel veya boyun fıtığı olanlar\nAğrısı ilaç veya fizik tedaviyle geçmeyen hastalar\nAmeliyat gerektirmeyen, uygun bulunan fıtık vakaları" => [
                'en' => "Mild and moderate lumbar or cervical disc herniation\nPatients whose pain does not respond to medication or physical therapy\nHerniations found suitable that do not require surgery",
                'ru' => "Лёгкая и умеренная грыжа поясничного или шейного диска\nПациенты, у которых боль не проходит от препаратов или физиотерапии\nПодходящие случаи грыжи, не требующие операции",
            ],
            'Tedavi öncesi MR ve fizik muayene ile değerlendirme yapılır.' => [
                'en' => 'An assessment is made with an MRI and a physical examination before treatment.',
                'ru' => 'Перед лечением проводится оценка с помощью МРТ и физикального осмотра.',
            ],
            'Uygulanmaz' => ['en' => 'Not suitable', 'ru' => 'Не подходит'],
            'Kimlere uygulanmaz?' => ['en' => 'Who is it not for?', 'ru' => 'Кому не подходит?'],
            "İleri derecede fıtığı olanlar\nCiddi sinir hasarı (sinir kaybı) bulunanlar\nOmurga stabilitesini etkileyen durumlar ya da farklı omurga problemleri olanlar" => [
                'en' => "An advanced herniation\nSerious nerve damage (loss of nerve function)\nConditions that affect the stability of the spine, or other spinal problems",
                'ru' => "Выраженная грыжа\nСерьёзное повреждение нерва (утрата его функции)\nСостояния, влияющие на стабильность позвоночника, или другие проблемы позвоночника",
            ],
            'Bu durumlarda alternatif tedavi yöntemleri değerlendirilir.' => [
                'en' => 'In these cases alternative treatments are considered.',
                'ru' => 'В таких случаях рассматриваются альтернативные методы лечения.',
            ],

            'III — Uygulama' => ['en' => 'III — The procedure', 'ru' => 'III — Проведение процедуры'],
            'İşlem nasıl' => ['en' => 'How is the procedure', 'ru' => 'Как проводится'],
            'yapılır?' => ['en' => 'performed?', 'ru' => 'процедура?'],
            'İşlem, görüntüleme eşliğinde özel bir iğne yardımıyla disk içine girilerek yapılır. Disk içindeki fazla basınç kontrollü şekilde azaltılır.' => [
                'en' => 'The disc is entered with a special needle under imaging guidance. The excess pressure inside the disc is reduced in a controlled way.',
                'ru' => 'В диск входят специальной иглой под контролем визуализации. Избыточное давление внутри диска снижается контролируемым образом.',
            ],
            'Değerlendirme' => ['en' => 'Assessment', 'ru' => 'Обследование'],
            'Tedavi öncesi MR ve fizik muayene ile fıtığınızın bu yönteme uygunluğu değerlendirilir.' => [
                'en' => 'Before treatment, an MRI and a physical examination establish whether your herniation is suitable for this method.',
                'ru' => 'Перед лечением с помощью МРТ и физикального осмотра оценивают, подходит ли ваша грыжа для этого метода.',
            ],
            'İşlem genellikle lokal anestezi altında, bilinciniz açıkken yapılır; genel anesteziye gerek yoktur.' => [
                'en' => 'The procedure is usually carried out under local anaesthesia while you stay awake; no general anaesthesia is needed.',
                'ru' => 'Процедуру обычно проводят под местной анестезией, вы остаётесь в сознании; общая анестезия не требуется.',
            ],
            'Cilt üzerinden çok ince bir iğneyle diske girilir; radyofrekans probuyla diskin iç kısmı ısıtılarak küçültülür.' => [
                'en' => 'The disc is entered through the skin with a very fine needle; the inside of the disc is heated with a radiofrequency probe and reduced in volume.',
                'ru' => 'Через кожу в диск вводят очень тонкую иглу; радиочастотным зондом внутреннюю часть диска нагревают и уменьшают в объёме.',
            ],
            'Aynı gün taburcu' => ['en' => 'Discharged the same day', 'ru' => 'Выписка в тот же день'],
            'İşlem ortalama 20–30 dakika sürer; hasta 2–4 saat içinde taburcu olabilir.' => [
                'en' => 'The procedure takes 20–30 minutes on average; the patient can be discharged within 2–4 hours.',
                'ru' => 'Процедура занимает в среднем 20–30 минут; пациента можно выписать через 2–4 часа.',
            ],

            'IV — Video' => ['en' => 'IV — Video', 'ru' => 'IV — Видео'],
            'Nükleoplasti işlemi' => ['en' => 'Nucleoplasty —', 'ru' => 'Нуклеопластика —'],
            'nedir?' => ['en' => 'what is it?', 'ru' => 'что это?'],

            'V — Sorular' => ['en' => 'V — Questions', 'ru' => 'V — Вопросы'],
            'Nükleoplasti hakkında' => ['en' => 'Frequently asked questions', 'ru' => 'Часто задаваемые вопросы'],
            'sıkça sorulan sorular' => ['en' => 'about nucleoplasty', 'ru' => 'о нуклеопластике'],
            'Hastalar tarafından sıkça sorulan sorular ve cevapları.' => [
                'en' => 'The questions patients ask most often, and their answers.',
                'ru' => 'Вопросы, которые чаще всего задают пациенты, и ответы на них.',
            ],
            'Sorunuz mu var? WhatsApp ile yazın' => [
                'en' => 'Have a question? Message us on WhatsApp',
                'ru' => 'Есть вопрос? Напишите нам в WhatsApp',
            ],
            'Nükleoplasti, bel veya boyun fıtığı nedeniyle omurga sinirlerine baskı yapan disk içi basıncın azaltılması için uygulanan ameliyatsız bir tedavi yöntemidir. Radyofrekans enerjisiyle diskin iç kısmındaki dokular buharlaştırılır ve sinir üzerindeki baskı ortadan kaldırılır. Böylece ağrılar azalır, hareket kabiliyeti artar.' => [
                'en' => 'Nucleoplasty is a non-surgical treatment used to reduce the pressure inside the disc that presses on the spinal nerves in a lumbar or cervical herniation. Radiofrequency energy vaporises tissue inside the disc and the pressure on the nerve is removed. The pain then eases and mobility improves.',
                'ru' => 'Нуклеопластика — безоперационный метод лечения, который применяется для снижения давления внутри диска, сдавливающего нервы позвоночника при грыже поясничного или шейного отдела. Радиочастотной энергией ткани внутри диска выпаривают, и давление на нерв устраняется. Благодаря этому боль уменьшается, а подвижность возрастает.',
            ],
            'Nükleoplasti kimlere uygulanabilir?' => [
                'en' => 'Which patients can have nucleoplasty?',
                'ru' => 'Кому можно проводить нуклеопластику?',
            ],
            'Nükleoplasti, hafif ve orta düzeyde bel veya boyun fıtığı olan, ağrısı ilaç veya fizik tedaviyle geçmeyen hastalarda tercih edilir. Ancak ileri derecede fıtık veya sinir hasarı olan hastalarda uygun değildir. Tedavi öncesi MR ve fizik muayene ile hasta değerlendirilir.' => [
                'en' => 'Nucleoplasty is chosen for patients with a mild or moderate lumbar or cervical disc herniation whose pain does not respond to medication or physical therapy. It is not suitable for patients with an advanced herniation or nerve damage. The patient is assessed with an MRI and a physical examination before treatment.',
                'ru' => 'Нуклеопластику выбирают для пациентов с лёгкой или умеренной грыжей поясничного или шейного диска, у которых боль не проходит от препаратов или физиотерапии. При выраженной грыже или повреждении нерва она не подходит. Перед лечением пациента оценивают с помощью МРТ и физикального осмотра.',
            ],
            'Nükleoplasti işlemi nasıl yapılır?' => [
                'en' => 'How is nucleoplasty performed?',
                'ru' => 'Как проводится нуклеопластика?',
            ],
            'İşlem genellikle lokal anestezi altında, hastanın bilinci açık şekilde gerçekleştirilir. Cilt üzerinden çok ince bir iğneyle diske girilir ve radyofrekans probu yardımıyla diskin iç kısmı ısıtılarak küçültülür. İşlem ortalama 20-30 dakika sürer ve hasta aynı gün taburcu olabilir.' => [
                'en' => 'The procedure is usually carried out under local anaesthesia with the patient awake. The disc is entered through the skin with a very fine needle and the inside of the disc is heated with a radiofrequency probe and reduced in volume. The procedure takes 20-30 minutes on average and the patient can be discharged the same day.',
                'ru' => 'Процедуру обычно проводят под местной анестезией, пациент остаётся в сознании. Через кожу в диск вводят очень тонкую иглу, и радиочастотным зондом внутреннюю часть диска нагревают и уменьшают в объёме. Процедура занимает в среднем 20-30 минут, и пациента можно выписать в тот же день.',
            ],
            'Nükleoplasti sonrası iyileşme süreci nasıldır?' => [
                'en' => 'What is recovery after nucleoplasty like?',
                'ru' => 'Как проходит восстановление после нуклеопластики?',
            ],
            'Hastalar genellikle işlemden birkaç saat sonra yürüyebilir. Hafif bir istirahat dönemi önerilir. İlk hafta içinde ağrıda belirgin azalma görülür. Cerrahi kesi olmadığından, yara izi veya dikiş bulunmaz. Çoğu hasta birkaç gün içinde günlük yaşamına dönebilir.' => [
                'en' => 'Patients can usually walk a few hours after the procedure. A short period of rest is recommended. A clear reduction in pain is seen within the first week. Because there is no surgical incision, there is no scar and there are no stitches. Most patients can return to daily life within a few days.',
                'ru' => 'Пациенты обычно могут ходить уже через несколько часов после процедуры. Рекомендуется недолгий период покоя. В течение первой недели боль заметно уменьшается. Поскольку хирургического разреза нет, не остаётся ни рубца, ни швов. Большинство пациентов возвращаются к повседневной жизни через несколько дней.',
            ],

            '30 yıllık hekimlik deneyimi. İstanbul Üniversitesi Cerrahpaşa Tıp Fakültesi; Anesteziyoloji ve Reanimasyon uzmanlığı, Algoloji yan dalı.' => [
                'en' => '30 years of medical practice. Istanbul University Cerrahpaşa Faculty of Medicine; residency in anaesthesiology and reanimation, subspecialty in algology.',
                'ru' => '30 лет врачебной практики. Стамбульский университет, медицинский факультет Джеррахпаша; ординатура по анестезиологии и реаниматологии, субспециализация по алгологии.',
            ],

            'Size uygun tedaviyi' => [
                'en' => 'Let us settle on the treatment that suits you',
                'ru' => 'Подберём подходящее вам лечение',
            ],
            'birlikte belirleyelim.' => ['en' => 'together.', 'ru' => 'вместе.'],
            'Nükleoplastinin size uygun olup olmadığı, muayene ve görüntüleme sonrası belirlenir. Randevu talebinizi iletin; sizi arayarak uygun gün ve saati birlikte belirleyelim.' => [
                'en' => 'Whether nucleoplasty suits you is decided after an examination and imaging. Send us your appointment request and we will call you so we can agree on a day and a time together.',
                'ru' => 'Подходит ли вам нуклеопластика, определяют после осмотра и обследования. Оставьте заявку на приём: мы перезвоним и вместе подберём удобные день и время.',
            ],
        ],
    ],

    'ameliyatsiz-lazerle-bel-fitigi-tedavisi' => [
        'fields' => [
            'title' => [
                'en' => 'Non-Surgical Laser Treatment for Lumbar Disc Herniation',
                'ru' => 'Безоперационное лазерное лечение грыжи поясничного диска',
            ],
            'slug' => [
                'en' => 'laser-treatment-for-lumbar-disc-herniation',
                'ru' => 'lazernoe-lechenie-gryzhi-poyasnichnogo-diska',
            ],
            'summary' => [
                'en' => 'Non-surgical laser treatment for a lumbar disc herniation is one of the modern methods used to relieve the pain a lumbar disc herniation causes. With this technique doctors reach the herniated disc directly…',
                'ru' => 'Безоперационное лазерное лечение грыжи поясничного диска — один из современных методов устранения боли, вызванной грыжей поясничного диска. Благодаря этой методике врачи напрямую…',
            ],
            'seo_title' => [
                'en' => 'Non-Surgical Laser Treatment for Lumbar Disc Herniation | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Безоперационное лазерное лечение грыжи поясничного диска — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Read our explanatory article on laser procedures in the non-surgical treatment of lumbar disc herniation.',
                'ru' => 'Прочитайте нашу подробную статью о лазерных методиках в безоперационном лечении грыжи поясничного диска.',
            ],
        ],
        'blocks' => [
            'Lazerle bel fıtığı tedavisi kalıcı mıdır?' => [
                'en' => 'Is laser treatment for a lumbar disc herniation permanent?',
                'ru' => 'Лазерное лечение грыжи поясничного диска — это навсегда?',
            ],
            'Evet, lazer ile buharlaştırılan doku geri gelmez. Ancak hastanın yaşam tarzına dikkat etmesi (kilo kontrolü, egzersiz) yeni fıtık oluşumlarını engellemek için kritiktir.' => [
                'en' => 'Yes, the tissue vaporised by the laser does not come back. But it is critical that the patient watches their way of life (weight control, exercise) to prevent new herniations from forming.',
                'ru' => 'Да, ткань, выпаренная лазером, не восстанавливается. Однако чтобы не возникали новые грыжи, пациенту крайне важно следить за образом жизни (контроль веса, физическая активность).',
            ],
            'İşlem sırasında ağrı hissedilir mi?' => [
                'en' => 'Does the procedure hurt?',
                'ru' => 'Больно ли во время процедуры?',
            ],
            'Hayır. Bölge lokal anestezi ile uyuşturulduğu için sadece hafif bir baskı hissi oluşabilir.' => [
                'en' => 'No. Because the area is numbed with local anaesthesia, you may only feel a slight sense of pressure.',
                'ru' => 'Нет. Поскольку область обезболивают местной анестезией, возможно лишь лёгкое ощущение давления.',
            ],
            'Kimler bu tedavi için uygundur?' => [
                'en' => 'Who is suitable for this treatment?',
                'ru' => 'Кому подходит это лечение?',
            ],
            'İlaç ve fizik tedaviye yanıt vermeyen, ancak &#34;acil ameliyat&#34; gerektirecek kadar büyük (felç riski taşıyan) fıtığı olmayan hastalar için idealdir.' => [
                'en' => 'It suits patients whose herniation does not respond to medication and physical therapy but is not large enough to call for "emergency surgery" (carrying a risk of paralysis).',
                'ru' => 'Оно подходит пациентам, у которых грыжа не поддаётся медикаментозному лечению и физиотерапии, но не настолько велика, чтобы потребовалась «экстренная операция» (с риском паралича).',
            ],
            'Lazerle bel fıtığı tedavisi boyun fıtığı için de uygulanabilir mi?' => [
                'en' => 'Can laser treatment also be used for a cervical disc herniation?',
                'ru' => 'Можно ли применять лазерное лечение и при грыже шейного диска?',
            ],
            'Evet, bu yöntem sadece bel bölgesinde değil, uygun görülen vakalarda boyun fıtığı tedavisinde de güvenle kullanılmaktadır. Prensip aynıdır; disk içindeki basıncı düşürerek sinir rahatlatılır.' => [
                'en' => 'Yes. In suitable cases this method is used safely not only in the lower back but also in the treatment of a cervical disc herniation. The principle is the same: lowering the pressure inside the disc relieves the nerve.',
                'ru' => 'Да, в подходящих случаях этот метод безопасно применяется не только в поясничном отделе, но и при лечении грыжи шейного диска. Принцип тот же: снижение давления внутри диска освобождает нерв.',
            ],
            'İşlemden hemen sonra kendi aracımla eve dönebilir miyim?' => [
                'en' => 'Can I drive myself home right after the procedure?',
                'ru' => 'Могу ли я сразу после процедуры поехать домой за рулём?',
            ],
            'Lokal anestezi uygulandığı ve genel bir uyuşukluk olmadığı için teorik olarak mümkündür. Ancak konforunuz ve güvenliğiniz açısından işlem günü yanınızda bir refakatçi olması veya taksi kullanmanız önerilir.' => [
                'en' => 'In theory it is possible, because local anaesthesia is used and there is no general drowsiness. For your comfort and safety, however, we recommend having someone with you on the day or taking a taxi.',
                'ru' => 'Теоретически это возможно, так как используется местная анестезия и общей сонливости нет. Но ради вашего удобства и безопасности в день процедуры лучше, чтобы вас кто-то сопровождал, или воспользоваться такси.',
            ],
            'Aynı seansta birden fazla fıtığa müdahale edilebilir mi?' => [
                'en' => 'Can more than one herniation be treated in the same session?',
                'ru' => 'Можно ли за один сеанс воздействовать на несколько грыж?',
            ],
            'Evet. Eğer hastanın birden fazla seviyede (örneğin hem L4-L5 hem L5-S1) fıtığı varsa, tek bir seansta her iki bölgeye de lazer uygulaması yapılabilmektedir.' => [
                'en' => 'Yes. If the patient has a herniation at more than one level (for example both L4-L5 and L5-S1), the laser can be applied to both levels in a single session.',
                'ru' => 'Да. Если у пациента грыжи на нескольких уровнях (например, и L4-L5, и L5-S1), лазер можно применить к обеим зонам за один сеанс.',
            ],
            'Lazerle bel fıtığı tedavisinin başarı oranı nedir?' => [
                'en' => 'What is the success rate of laser treatment for a lumbar disc herniation?',
                'ru' => 'Какова эффективность лазерного лечения грыжи поясничного диска?',
            ],
            'Bu, cerrahi olmayan bir yöntem için oldukça yüksek bir orandır.' => [
                'en' => 'That is a fairly high rate for a non-surgical method.',
                'ru' => 'Для безоперационного метода это довольно высокий показатель.',
            ],
            'İleri yaştaki hastalar veya kalp hastaları bu tedaviyi yaptırabilir mi?' => [
                'en' => 'Can older patients or patients with heart conditions have this treatment?',
                'ru' => 'Можно ли проводить это лечение пожилым пациентам или людям с болезнями сердца?',
            ],
            'Genel anestezi (narkoz) gerekmediği için ileri yaşta olan, kalp, tansiyon veya şeker hastalığı nedeniyle ameliyatı riskli bulunan kişiler için lazer uygulaması çok daha güvenli bir alternatiftir.' => [
                'en' => 'Because no general anaesthesia is needed, the laser procedure is a much safer alternative for older people and for those for whom surgery is considered risky because of a heart condition, blood pressure or diabetes.',
                'ru' => 'Поскольку общая анестезия (наркоз) не требуется, лазерная процедура — значительно более безопасная альтернатива для пожилых людей и для тех, кому операция считается рискованной из-за болезни сердца, артериального давления или диабета.',
            ],
            'İşlem öncesinde aç kalmak veya özel bir hazırlık gerekir mi?' => [
                'en' => 'Do I have to fast or prepare in any special way before the procedure?',
                'ru' => 'Нужно ли голодать или как-то особенно готовиться перед процедурой?',
            ],
            'Genellikle işlemden 4-6 saat öncesine kadar hafif gıdalar tüketilebilir. Tamamen aç kalmanıza gerek yoktur ancak kullanılan ilaçlar varsa mutlaka doktorunuza danışmanız gerekir.' => [
                'en' => 'Light food can usually be eaten up to 4-6 hours before the procedure. You do not need to fast completely, but if you take any medication you must speak to your doctor about it.',
                'ru' => 'Как правило, лёгкую пищу можно есть за 4-6 часов до процедуры. Полностью голодать не нужно, но если вы принимаете какие-либо препараты, обязательно посоветуйтесь с врачом.',
            ],
            'Lazer ışığı sinirlere veya omuriliğe zarar verir mi?' => [
                'en' => 'Can the laser damage the nerves or the spinal cord?',
                'ru' => 'Может ли лазер повредить нервы или спинной мозг?',
            ],
            'Hayır. İşlem sırasında gelişmiş görüntüleme cihazları (Skopi) kullanıldığı için iğnenin ve lazerin yeri milimetrik olarak izlenir. Lazer sadece disk içindeki sıvıya odaklanır, sinir köklerine temas etmez.' => [
                'en' => 'No. Advanced imaging equipment (fluoroscopy) is used during the procedure, so the position of the needle and the laser is followed to the millimetre. The laser is focused only on the fluid inside the disc and does not touch the nerve roots.',
                'ru' => 'Нет. Во время процедуры используется современное оборудование для визуализации (рентгеноскопия), поэтому положение иглы и лазера отслеживается с точностью до миллиметра. Лазер направлен только на жидкость внутри диска и не касается нервных корешков.',
            ],
            'Tedaviden ne kadar süre sonra spor yapmaya başlayabilirim?' => [
                'en' => 'How long after the treatment can I start doing sport?',
                'ru' => 'Через какое время после лечения можно начать заниматься спортом?',
            ],
            'Yürüyüşlere 1-2 gün sonra başlayabilirsiniz. Ancak ağır sporlar, yüzme veya profesyonel antrenmanlar için genellikle 3-4 hafta beklenmesi ve doktor kontrolünden geçilmesi önerilir.' => [
                'en' => 'You can start walking after 1-2 days. For heavier sport, swimming or professional training, however, it is generally recommended to wait 3-4 weeks and to be seen by the doctor first.',
                'ru' => 'Ходить можно начинать через 1-2 дня. Однако для тяжёлых видов спорта, плавания или профессиональных тренировок обычно рекомендуется подождать 3-4 недели и пройти контрольный осмотр у врача.',
            ],
            'Lazer uygulaması sonrası fıtığın tekrarlama riski var mıdır?' => [
                'en' => 'Is there a risk of the herniation coming back after the laser procedure?',
                'ru' => 'Есть ли риск, что после лазерной процедуры грыжа вернётся?',
            ],
            'Lazerle küçültülen doku geri gelmez; ancak bel bölgesini korumamak, ağır kaldırmak veya aşırı kilo alımı yeni fıtıkların oluşmasına zemin hazırlayabilir. Yani mevcut fıtık değil, yanlış yaşam tarzı risk oluşturur.' => [
                'en' => 'The tissue reduced by the laser does not come back; but failing to look after the lower back, lifting heavy loads or putting on a lot of weight can prepare the ground for new herniations. The risk comes from the wrong way of life, not from the existing herniation.',
                'ru' => 'Ткань, уменьшенная лазером, не восстанавливается; однако если не беречь поясницу, поднимать тяжести или сильно набирать вес, это может создать условия для новых грыж. То есть риск создаёт неправильный образ жизни, а не уже пролеченная грыжа.',
            ],
            'SGK veya özel sigortalar bu işlemi karşılıyor mu?' => [
                'en' => 'Do the Turkish social security scheme (SGK) or private insurers cover this procedure?',
                'ru' => 'Покрывают ли эту процедуру турецкая система соцстрахования (SGK) или частные страховые компании?',
            ],
            'Maalesef bu tedavi türü SGK kapsamında bulunmamaktadır.' => [
                'en' => 'Unfortunately this kind of treatment is not covered by the Turkish social security scheme (SGK).',
                'ru' => 'К сожалению, этот вид лечения не входит в покрытие турецкой системы социального страхования (SGK).',
            ],
        ],
    ],
];
