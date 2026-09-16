<?php

/*
|--------------------------------------------------------------------------
| Page translations
|--------------------------------------------------------------------------
|
| Keyed by the Turkish slug of the page, which never changes. Per record:
|
|   fields  translations of the model's own columns, per locale
|   blocks  Turkish block text => translations, matched anywhere in `blocks`
|   paths   a block leaf addressed directly, for the rare Turkish word that
|           has to be translated two ways on the same page
|   publish set to false to keep a record out of a locale even when complete
|
| `database/translations/shared.php` holds the block text that repeats across
| records, and `bodies.php` the long HTML bodies. Both are overridden here.
|
| Turkish is the source of truth: never edit a key, and never state a fact,
| number or duration that the Turkish does not.
|
*/

return [

    'anasayfa' => [
        'fields' => [
            'title' => ['ru' => 'Главная'],
            'slug' => ['ru' => 'glavnaya'],
            'seo_title' => ['ru' => 'Специалист по алгологии — проф. д-р Хюсню Сюслю — медицина боли'],
            'seo_description' => ['ru' => 'Проф. д-р Хюсню Сюслю — специалист по алгологии (медицине боли) в Стамбуле: безоперационные и малоинвазивные методы лечения боли в пояснице, шее и хронической боли.'],
        ],
        'blocks' => [
            'Algoloji, ağrılarınızın kaynağını bulan ve sizi doğru çözüme ulaştıran bilim dalıdır. Otuz yıllık hekimlik deneyimiyle ameliyatsız ve girişimsel tedavi seçenekleri sunuyoruz.' => [
                'ru' => 'Алгология — раздел медицины, который находит источник вашей боли и приводит к верному решению. Опираясь на тридцать лет врачебной практики, мы предлагаем безоперационные и интервенционные методы лечения.',
            ],
            'Ağrısız yaşam' => ['ru' => 'Жизнь без боли'],
            'mümkün.' => ['ru' => 'возможна.'],
            'Algoloji — ağrı bilimi' => ['ru' => 'Алгология — наука о боли'],

            'Fıtıkta ameliyat,' => [
                'en' => 'Surgery is not the only',
                'ru' => 'Операция —',
            ],
            'tek çözüm değil.' => [
                'en' => 'solution for a herniated disc.',
                'ru' => 'не единственное решение при грыже диска.',
            ],
            'I — Ameliyatsız fıtık tedavisi' => [
                'en' => 'I — Non-surgical treatment of disc herniation',
                'ru' => 'I — Безоперационное лечение грыжи диска',
            ],
            'Bel ve boyun fıtığında cerrahiye alternatif, lokal anestezi altında uygulanan minimal invaziv yöntemler. Doğru hasta, doğru yöntemle.' => [
                'en' => 'Minimally invasive methods performed under local anaesthesia as an alternative to surgery for lumbar and cervical disc herniation. The right patient, with the right method.',
                'ru' => 'Малоинвазивные методы под местной анестезией как альтернатива операции при грыже поясничного и шейного отделов. Правильному пациенту — правильный метод.',
            ],
            'Öne çıkan işlem' => [
                'en' => 'Featured procedure',
                'ru' => 'Ключевая процедура',
            ],
            'Bel ve boyun fıtığında, açık cerrahi gerektirmeden diskin sinir üzerindeki baskısını azaltmayı amaçlayan girişimsel yöntem.' => [
                'en' => 'An interventional method for lumbar and cervical disc herniation that aims to reduce the pressure of the disc on the nerve without open surgery.',
                'ru' => 'Интервенционный метод при грыже поясничного и шейного отделов, цель которого — уменьшить давление диска на нерв без открытой операции.',
            ],
            'İşlemi inceleyin' => [
                'en' => 'See the procedure',
                'ru' => 'Подробнее о процедуре',
            ],
            'işlem süresi' => [
                'en' => 'procedure time',
                'ru' => 'длительность процедуры',
            ],
            'anestezi' => [
                'en' => 'anaesthesia',
                'ru' => 'анестезия',
            ],
            'sonra taburcu' => [
                'en' => 'until discharge',
                'ru' => 'до выписки',
            ],
            'Lazer işlemi' => [
                'en' => 'Laser procedure',
                'ru' => 'Лазерная процедура',
            ],
            'Ameliyatsız Bel Fıtığı' => [
                'en' => 'Non-surgical lumbar disc herniation',
                'ru' => 'Грыжа поясничного диска без операции',
            ],
            'Fıtıklaşan diskin sinir üzerindeki baskısını, cerrahi müdahale gerektirmeden azaltmayı hedefleyen modern bir tedavi seçeneği.' => [
                'en' => 'A modern treatment option that sets out to reduce the pressure the herniated disc puts on the nerve without surgery.',
                'ru' => 'Современный метод лечения, направленный на уменьшение давления грыжи диска на нерв без хирургического вмешательства.',
            ],
            'Ameliyatsız Boyun Fıtığı' => [
                'en' => 'Non-surgical cervical disc herniation',
                'ru' => 'Грыжа шейного диска без операции',
            ],
            'Cerrahi kesiye gerek kalmadan, lokal anestezi altında sinir üzerindeki baskıyı azaltmayı amaçlayan girişimsel bir uygulama.' => [
                'en' => 'An interventional procedure that aims to relieve the pressure on the nerve under local anaesthesia, with no surgical incision.',
                'ru' => 'Интервенционная процедура под местной анестезией, цель которой — уменьшить давление на нерв без хирургического разреза.',
            ],
            'Radyofrekans ile Fıtık Tedavisi' => [
                'en' => 'Radiofrequency treatment for disc herniation',
                'ru' => 'Лечение грыжи диска радиочастотной абляцией',
            ],
            'Sinir kökü çevresindeki ağrıyı kontrol altına almak ve diskin neden olduğu basıncı azaltmak için uygulanan ameliyatsız yöntem.' => [
                'en' => 'A non-surgical method used to bring the pain around the nerve root under control and to reduce the pressure caused by the disc.',
                'ru' => 'Безоперационный метод, применяемый для контроля боли вокруг нервного корешка и уменьшения давления, вызванного диском.',
            ],

            'Karşılaştırma' => ['en' => 'Comparison', 'ru' => 'Сравнение'],
            'Lazer tedavisi ve geleneksel ameliyat' => [
                'en' => 'Laser treatment and conventional surgery',
                'ru' => 'Лазерное лечение и традиционная операция',
            ],
            'Hangi yöntemin size uygun olduğuna muayene ve görüntüleme sonrası birlikte karar veriyoruz.' => [
                'en' => 'We decide together which method suits you after an examination and imaging.',
                'ru' => 'Какой метод вам подходит, мы решаем вместе после осмотра и обследования.',
            ],
            'Lazer tedavisi' => ['en' => 'Laser treatment', 'ru' => 'Лазерное лечение'],
            'Geleneksel ameliyat' => ['en' => 'Conventional surgery', 'ru' => 'Традиционная операция'],
            'Anestezi' => ['en' => 'Anaesthesia', 'ru' => 'Анестезия'],
            'Genel anestezi' => ['en' => 'General anaesthesia', 'ru' => 'Общая анестезия'],
            'Hastanede kalış' => ['en' => 'Hospital stay', 'ru' => 'Пребывание в стационаре'],
            '1–2 gün' => ['en' => '1–2 days', 'ru' => '1–2 дня'],
            'İşe dönüş süresi' => ['en' => 'Return to work', 'ru' => 'Возвращение к работе'],
            '3–5 gün' => ['en' => '3–5 days', 'ru' => '3–5 дней'],
            '4–6 hafta' => ['en' => '4–6 weeks', 'ru' => '4–6 недель'],
            'Doku hasarı' => ['en' => 'Tissue damage', 'ru' => 'Повреждение тканей'],
            'Çok düşük' => ['en' => 'Very low', 'ru' => 'Очень низкое'],
            'Kas ve kemik dokusu etkilenebilir' => [
                'en' => 'Muscle and bone tissue may be affected',
                'ru' => 'Возможно воздействие на мышечную и костную ткань',
            ],

            'Dört adımda' => ['en' => 'In four steps', 'ru' => 'В четыре шага'],
            'Tedavi süreciniz' => ['en' => 'Your treatment journey', 'ru' => 'Ваш путь лечения'],
            'Muayene' => ['en' => 'Examination', 'ru' => 'Осмотр'],
            'Şikâyetinizi, geçmişinizi ve görüntülemelerinizi birlikte değerlendiriyoruz.' => [
                'en' => 'We go through your complaint, your history and your imaging together.',
                'ru' => 'Мы вместе разбираем ваши жалобы, историю болезни и результаты обследований.',
            ],
            'Tanı' => ['en' => 'Diagnosis', 'ru' => 'Диагноз'],
            'Ağrının kaynağını fizik muayene ve gerekirse ek tetkiklerle netleştiriyoruz.' => [
                'en' => 'We pin down the source of the pain with a physical examination and, where needed, further tests.',
                'ru' => 'Источник боли уточняем с помощью физикального осмотра и, при необходимости, дополнительных исследований.',
            ],
            'Kişiye özel plan' => ['en' => 'A plan of your own', 'ru' => 'Индивидуальный план'],
            'Size en uygun ameliyatsız ya da girişimsel tedaviyi belirliyoruz.' => [
                'en' => 'We settle on the non-surgical or interventional treatment that suits you best.',
                'ru' => 'Мы подбираем наиболее подходящее вам безоперационное или интервенционное лечение.',
            ],
            'İşlem ve takip' => ['en' => 'Procedure and follow-up', 'ru' => 'Процедура и наблюдение'],
            'İşlem sonrası kontrollerle iyileşme sürecinizi yakından izliyoruz.' => [
                'en' => 'We follow your recovery closely with check-ups after the procedure.',
                'ru' => 'После процедуры мы внимательно наблюдаем за вашим восстановлением на контрольных приёмах.',
            ],

            'Ameliyatsız bel fıtığı' => [
                'en' => 'Non-surgical treatment of a lumbar disc herniation',
                'ru' => 'Безоперационное лечение грыжи поясничного диска',
            ],
            'Ameliyatsız boyun fıtığı' => [
                'en' => 'Non-surgical treatment of a cervical disc herniation',
                'ru' => 'Безоперационное лечение грыжи шейного диска',
            ],
            'Radyofrekans ile fıtık tedavisi' => [
                'en' => 'Radiofrequency treatment of a disc herniation',
                'ru' => 'Лечение грыжи диска радиочастотной абляцией',
            ],

            'Her ağrının bir' => ['en' => 'Every pain has', 'ru' => 'У каждой боли есть'],
            'kaynağı vardır.' => ['en' => 'a source.', 'ru' => 'свой источник.'],
            'II — Ağrı türleri' => ['en' => 'II — Types of pain', 'ru' => 'II — Виды боли'],

            'III — Hekiminiz' => ['en' => 'III — Your doctor', 'ru' => 'III — Ваш врач'],
            'Anesteziyoloji ve Reanimasyon — Dr. Lütfi Kırdar Kartal Eğitim ve Araştırma Hastanesi' => [
                'en' => 'Anaesthesiology and Reanimation — Dr. Lütfi Kırdar Kartal Training and Research Hospital',
                'ru' => 'Анестезиология и реаниматология — учебно-исследовательская больница имени д-ра Лютфи Кырдара (Картал)',
            ],
            'Üyelikler' => ['en' => 'Memberships', 'ru' => 'Членство в обществах'],
            'IASP · Türk Anesteziyoloji ve Reanimasyon Derneği · Algoloji Derneği · Türk Yoğun Bakım Derneği · Rejyonal Anestezi Derneği' => [
                'en' => 'IASP · Turkish Society of Anaesthesiology and Reanimation · Turkish Algology Association · Turkish Society of Intensive Care · Turkish Society of Regional Anaesthesia',
                'ru' => 'IASP · Турецкое общество анестезиологии и реаниматологии · Общество алгологии · Турецкое общество интенсивной терапии · Общество регионарной анестезии',
            ],

            'V — Medya' => ['en' => 'V — Media', 'ru' => 'V — СМИ'],
            'Ekranlarda ağrı bilimi' => [
                'en' => 'Pain medicine on air',
                'ru' => 'Медицина боли на экране',
            ],
            'VI — Bilgi köşesi' => ['en' => 'VI — Knowledge corner', 'ru' => 'VI — Полезные материалы'],
            'Hastalarımızın en çok sorduğu sorular' => [
                'en' => 'The questions our patients ask most',
                'ru' => 'Вопросы, которые чаще всего задают наши пациенты',
            ],
        ],
    ],

    'hakkimda' => [
        'fields' => [
            'title' => ['ru' => 'Обо мне'],
            'slug' => ['ru' => 'obo-mne'],
            'seo_title' => ['ru' => 'Обо мне — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Проф. д-р Хюсню Сюслю — профессор алгологии, ведёт приём в Стамбуле.'],
        ],
        'blocks' => [
            'Hakkımda' => ['en' => 'About me', 'ru' => 'Обо мне'],
            'Prof. Dr.' => ['en' => 'Prof. Dr.', 'ru' => 'Проф. д-р'],
            'Hüsnü Süslü' => ['en' => 'Hüsnü Süslü', 'ru' => 'Хюсню Сюслю'],

            'I — Özgeçmiş' => ['en' => 'I — Curriculum vitae', 'ru' => 'I — Биография'],
            'Kuşkusuz, her ağrı hikâyesi kişiye özeldir. Bu nedenle hastalarımı sadece semptomları üzerinden değil, biyopsikososyal bir bütünlük içinde değerlendirerek kendilerine uygun tedavi seçeneklerini planlıyorum.' => [
                'en' => 'Every story of pain is of course particular to the person living it. That is why I assess my patients not through their symptoms alone but as a biopsychosocial whole, and plan the treatment options that suit them.',
                'ru' => 'Разумеется, история боли у каждого своя. Поэтому я оцениваю пациентов не только по симптомам, но как биопсихосоциальное целое и подбираю подходящие именно им варианты лечения.',
            ],
            'Doğum' => ['en' => 'Born', 'ru' => 'Год и место рождения'],
            '1970, Erzincan' => ['en' => '1970, Erzincan', 'ru' => '1970, Эрзинджан'],
            'İ.Ü. Cerrahpaşa Tıp Fakültesi, 1994' => [
                'en' => 'Istanbul University Cerrahpaşa Faculty of Medicine, 1994',
                'ru' => 'Стамбульский университет, медицинский факультет Джеррахпаша, 1994',
            ],
            'Yabancı dil' => ['en' => 'Languages', 'ru' => 'Иностранный язык'],
            'İngilizce' => ['en' => 'English', 'ru' => 'английский'],

            'Özgeçmiş' => ['en' => 'Curriculum vitae', 'ru' => 'Биография'],
            'Hastayı bir bütün olarak değerlendirmek.' => [
                'en' => 'Assessing the patient as a whole.',
                'ru' => 'Оценивать пациента как единое целое.',
            ],
            '<h2>Algoloji Profesörü İstanbul.</h2>İstanbul&#039;da ikametgah etmektedir ve İngilizce bilmektedir.' => [
                'en' => '<h2>Professor of algology in Istanbul.</h2>He lives in Istanbul and speaks English.',
                'ru' => '<h2>Профессор алгологии в Стамбуле.</h2>Живёт в Стамбуле, владеет английским языком.',
            ],

            'II — Eğitim ve kariyer' => ['en' => 'II — Education and career', 'ru' => 'II — Образование и карьера'],
            'Eğitim ve' => ['en' => 'Education and', 'ru' => 'Образование и'],
            'kariyer' => ['en' => 'career', 'ru' => 'карьера'],
            'Tıp fakültesinden uzmanlık eğitimine, ağrı polikliniklerinden üniversitede öğretim üyeliğine uzanan mesleki yol.' => [
                'en' => 'A professional path running from medical school to residency, from pain clinics to a university teaching post.',
                'ru' => 'Профессиональный путь: от медицинского факультета к ординатуре, от клиник боли к преподаванию в университете.',
            ],
            'Eğitim' => ['en' => 'Education', 'ru' => 'Образование'],
            'Kartal\'da ilk ve orta öğrenim' => [
                'en' => 'Primary and secondary school in Kartal',
                'ru' => 'Начальная и средняя школа в Картале',
            ],
            'Zekeriya Güçer İlkokulu, Kartal Ortaokulu, Kartal Lisesi' => [
                'en' => 'Zekeriya Güçer Primary School, Kartal Secondary School, Kartal High School',
                'ru' => 'Начальная школа имени Зекерии Гючера, средняя школа Картала, лицей Картала',
            ],
            'Dokuz Eylül Üniversitesi Tıp Fakültesi' => [
                'en' => 'Dokuz Eylül University Faculty of Medicine',
                'ru' => 'Университет Докуз Эйлюль, медицинский факультет',
            ],
            'Tıp eğitimi · mezuniyet 1994' => [
                'en' => 'Medical education · graduated 1994',
                'ru' => 'Медицинское образование · выпуск 1994 года',
            ],
            'İnlingua Dil Okulu, Ulm / Almanya' => [
                'en' => 'Inlingua Language School, Ulm / Germany',
                'ru' => 'Языковая школа Inlingua, Ульм / Германия',
            ],
            'Ağustos 1993 – Ocak 1994' => [
                'en' => 'August 1993 – January 1994',
                'ru' => 'август 1993 — январь 1994',
            ],
            'Anesteziyoloji ve Reanimasyon Kliniği · uzmanlık eğitimi' => [
                'en' => 'Anaesthesiology and Reanimation Clinic · residency',
                'ru' => 'Клиника анестезиологии и реаниматологии · ординатура',
            ],
            'Vakıf Gureba Eğitim ve Araştırma Hastanesi' => [
                'en' => 'Vakıf Gureba Training and Research Hospital',
                'ru' => 'Учебно-исследовательская больница Вакыф Гуреба',
            ],
            'Ağrı polikliniğinde 2 ay süreli ağrı eğitimi (Ocak – Şubat)' => [
                'en' => 'Two-month pain training at the pain clinic (January – February)',
                'ru' => 'Двухмесячное обучение в клинике боли (январь — февраль)',
            ],
            'İstanbul Üniversitesi İstanbul Tıp Fakültesi' => [
                'en' => 'Istanbul University Istanbul Faculty of Medicine',
                'ru' => 'Стамбульский университет, Стамбульский медицинский факультет',
            ],
            'Algoloji Kliniği\'nde 6 ay süreli eğitim' => [
                'en' => 'Six-month training at the Algology Clinic',
                'ru' => 'Шестимесячное обучение в клинике алгологии',
            ],
            'Deneyim' => ['en' => 'Experience', 'ru' => 'Опыт работы'],
            'Mardin Kızıltepe Devlet Hastanesi' => [
                'en' => 'Mardin Kızıltepe State Hospital',
                'ru' => 'Государственная больница Кызылтепе (Мардин)',
            ],
            'Pratisyen hekim, mecburi hizmet' => [
                'en' => 'General practitioner, compulsory service',
                'ru' => 'Врач общей практики, обязательная отработка',
            ],
            'Mardin Devlet Hastanesi' => [
                'en' => 'Mardin State Hospital',
                'ru' => 'Государственная больница Мардина',
            ],
            'Beykoz Çocuk Göğüs Hastalıkları Hastanesi' => [
                'en' => 'Beykoz Children\'s Chest Diseases Hospital',
                'ru' => 'Бейкозская детская больница лёгочных заболеваний',
            ],
            'Anesteziyoloji ve Reanimasyon uzmanı' => [
                'en' => 'Specialist in anaesthesiology and reanimation',
                'ru' => 'Врач — анестезиолог-реаниматолог',
            ],
            'Şile Devlet Hastanesi' => ['en' => 'Şile State Hospital', 'ru' => 'Государственная больница Шиле'],
            '6 ay süreli' => ['en' => 'Six months', 'ru' => 'шесть месяцев'],
            'I. Anesteziyoloji ve Reanimasyon Kliniği' => [
                'en' => '1st Anaesthesiology and Reanimation Clinic',
                'ru' => '1-я клиника анестезиологии и реаниматологии',
            ],
            'I. Anesteziyoloji ve Reanimasyon Kliniği, Ağrı Bölümü' => [
                'en' => '1st Anaesthesiology and Reanimation Clinic, Pain Department',
                'ru' => '1-я клиника анестезиологии и реаниматологии, отделение лечения боли',
            ],
            'Maltepe Üniversitesi' => ['en' => 'Maltepe University', 'ru' => 'Университет Мальтепе'],
            'Öğretim üyesi, Algoloji (Ağrı) Bölümü sorumlusu' => [
                'en' => 'Lecturer, head of the Algology (Pain) Department',
                'ru' => 'Преподаватель, руководитель отделения алгологии (лечения боли)',
            ],
            'Uzmanlık sonrası eğitimler' => [
                'en' => 'Post-residency training',
                'ru' => 'Обучение после ординатуры',
            ],
            'Kadavra üzerinde rejyonal anestezi teknikleri' => [
                'en' => 'Regional anaesthesia techniques on cadavers',
                'ru' => 'Техники регионарной анестезии на кадаврах',
            ],
            'Algoloji (ağrı) eğitimi' => [
                'en' => 'Algology (pain) training',
                'ru' => 'Обучение по алгологии (лечению боли)',
            ],
            'Vertebroplasti eğitimi' => ['en' => 'Vertebroplasty training', 'ru' => 'Обучение вертебропластике'],
            'Cerrahpaşa Tıp Fakültesi, Nöroşirürji Bölüm Başkanlığı' => [
                'en' => 'Cerrahpaşa Faculty of Medicine, Department of Neurosurgery',
                'ru' => 'Медицинский факультет Джеррахпаша, кафедра нейрохирургии',
            ],
            'Ultrason eşliğinde rejyonal bloklar' => [
                'en' => 'Regional blocks under ultrasound guidance',
                'ru' => 'Регионарные блокады под ультразвуковым контролем',
            ],
            'Dorsal kolon stimülasyonu (DCS)' => [
                'en' => 'Dorsal column stimulation (DCS)',
                'ru' => 'Стимуляция задних столбов спинного мозга (DCS)',
            ],
            'İstanbul' => ['en' => 'Istanbul', 'ru' => 'Стамбул'],
            'Amsterdam, Hollanda' => ['en' => 'Amsterdam, the Netherlands', 'ru' => 'Амстердам, Нидерланды'],
            'Epiduroskopi' => ['en' => 'Epiduroscopy', 'ru' => 'Эпидуроскопия'],
            'Venedik, İtalya' => ['en' => 'Venice, Italy', 'ru' => 'Венеция, Италия'],

            'III — Uzmanlık alanları' => ['en' => 'III — Areas of expertise', 'ru' => 'III — Направления работы'],
            'alanlarım' => ['en' => 'of expertise', 'ru' => 'работы'],

            'V — Akademik çalışmalar' => ['en' => 'V — Academic work', 'ru' => 'V — Научная работа'],
            '28 bilimsel yayın' => ['en' => '28 scientific publications', 'ru' => '28 научных публикаций'],
            'Ulusal ve uluslararası hakemli dergilerde yayımlanmış akademik çalışmalar. Yayınların güncel listesi Google Scholar profilindedir.' => [
                'en' => 'Academic work published in national and international peer-reviewed journals. The current list of publications is on the Google Scholar profile.',
                'ru' => 'Научные работы, опубликованные в национальных и международных рецензируемых журналах. Актуальный список публикаций — в профиле Google Scholar.',
            ],
            'Google Scholar profili' => ['en' => 'Google Scholar profile', 'ru' => 'Профиль в Google Scholar'],

            'Size özel' => ['en' => 'Get in touch', 'ru' => 'Свяжитесь с нами,'],
            'tedavi planı için iletişime geçin.' => [
                'en' => 'for a treatment plan of your own.',
                'ru' => 'чтобы составить индивидуальный план лечения.',
            ],
        ],
        'paths' => [
            /* "Uzmanlık" is the residency fact above and the heading of the
               specialities section here, so the two cannot share a translation. */
            'blocks.5.data.title' => ['en' => 'My areas', 'ru' => 'Мои направления'],
        ],
    ],

    'iletisim' => [
        'fields' => [
            'title' => ['ru' => 'Контакты'],
            'slug' => ['ru' => 'kontakty'],
            'seo_title' => ['ru' => 'Контакты — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Контактные данные проф. д-ра Хюсню Сюслю и способы записи на приём в Кадыкёе, Стамбул.'],
        ],
        'blocks' => [
            'Algoloji randevusu' => ['en' => 'Algology appointment', 'ru' => 'Запись к алгологу'],
            'İletişim ve' => ['en' => 'Contact and', 'ru' => 'Контакты и'],
            'Algoloji randevusu almak ve bize ulaşmak için aşağıdaki seçenekleri kullanabilirsiniz: online randevu, telefonla randevu ya da diğer iletişim kanalları.' => [
                'en' => 'To book an algology appointment and to reach us, you can use the options below: online booking, booking by phone, or the other contact channels.',
                'ru' => 'Чтобы записаться на приём к алгологу и связаться с нами, воспользуйтесь одним из вариантов ниже: онлайн-запись, запись по телефону или другие каналы связи.',
            ],
            'I — İletişim bilgileri' => ['en' => 'I — Contact details', 'ru' => 'I — Контактные данные'],
            'Kadıköy, Bağdat Caddesi' => ['en' => 'Kadıköy, Bağdat Avenue', 'ru' => 'Кадыкёй, проспект Багдат'],
            'Bağdat Caddesi · Göztepe' => ['en' => 'Bağdat Avenue · Göztepe', 'ru' => 'Проспект Багдат · Гёзтепе'],
            'Kadıköy – İstanbul' => ['en' => 'Kadıköy – Istanbul', 'ru' => 'Кадыкёй — Стамбул'],
            'II — Randevu talebi' => ['en' => 'II — Appointment request', 'ru' => 'II — Заявка на приём'],
        ],
        'paths' => [
            /* Accent word of the hero title, so it reads "Контакты и запись на приём". */
            'blocks.0.data.accent' => ['en' => 'appointments', 'ru' => 'запись на приём'],
        ],
    ],

    'algoloji-randevu' => [
        'fields' => [
            'title' => ['en' => 'Algology Appointment', 'ru' => 'Запись к алгологу'],
            'slug' => ['en' => 'algology-appointment', 'ru' => 'zapis-k-algologu'],
            'seo_title' => [
                'en' => 'Algology Appointment | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Запись к алгологу — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Ways to book an algology appointment with Prof. Dr. Hüsnü Süslü: online, by phone or through the other contact channels.',
                'ru' => 'Варианты записи к алгологу: онлайн, по телефону или через другие каналы связи. Подробности — на нашей странице.',
            ],
        ],
        'blocks' => [
            'Prof.Dr.Hüsnü Süslü — Algoloji (Ağrı Bilimi) Online Randevu Al!' => [
                'en' => 'Prof. Dr. Hüsnü Süslü — algology (pain medicine) online appointment',
                'ru' => 'Проф. д-р Хюсню Сюслю — алгология (медицина боли): онлайн-запись',
            ],
            /* The Turkish carries a non-breaking space after "Algoloji". */
            "<p>Algoloji\u{00A0} Randevusu Almak ve iletişime geçmek için aşağıdaki seçenekleri kullanınız. Online randevu almak, telefonda randevu almak ya da diğer iletişim kanallarından randevu almak için aşağıdaki seçenekleri kullanabilirsiniz.</p>" => [
                'en' => '<p>Use the options below to book an algology appointment and to get in touch. You can book online, book by phone, or book through the other contact channels.</p>',
                'ru' => '<p>Чтобы записаться на приём к алгологу и связаться с нами, воспользуйтесь вариантами ниже. Записаться можно онлайн, по телефону или через другие каналы связи.</p>',
            ],
            'Address' => ['en' => 'Address', 'ru' => 'Адрес'],
            '<ul><li>Göztepe Mah. Bağdat Cad. Alemdar Apt. No:213/3Kadıköy-İSTANBUL</li></ul>' => [
                'en' => '<ul><li>Göztepe Mah. Bağdat Cad. Alemdar Apt. No:213/3 Kadıköy-İSTANBUL</li></ul>',
                'ru' => '<ul><li>Göztepe Mah. Bağdat Cad. Alemdar Apt. No:213/3 Kadıköy-İSTANBUL (Кадыкёй, Стамбул)</li></ul>',
            ],
            'Çalışma Saatleri' => ['en' => 'Opening hours', 'ru' => 'Часы приёма'],
            '<ul><li>Pazartesi - Pazar 08:00-18:00</li></ul>' => [
                'en' => '<ul><li>Monday – Sunday 08:00-18:00</li></ul>',
                'ru' => '<ul><li>Понедельник — воскресенье 08:00-18:00</li></ul>',
            ],
        ],
    ],

    'girisimsel-agri-tedavileri' => [
        'fields' => [
            'title' => ['ru' => 'Интервенционное лечение боли'],
            'slug' => ['ru' => 'intervencionnoe-lechenie-boli'],
            'seo_title' => ['ru' => 'Интервенционное лечение боли — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Обзор методов интервенционного лечения боли: эпидуральные инъекции, радиочастотная абляция, блокады нервов и вертебропластика.'],
        ],
    ],

    'agri-turleri' => [
        'fields' => [
            'title' => ['ru' => 'Виды боли'],
            'slug' => ['ru' => 'vidy-boli'],
            'seo_title' => ['ru' => 'Виды боли — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Основные виды боли — в пояснице, шее, голове, онкологическая и нейропатическая боль — и то, как алголог их диагностирует и лечит.'],
        ],
    ],

    'yurt-disi-hastalar' => [
        'fields' => [
            'title' => ['en' => 'International Patients', 'ru' => 'Пациентам из-за рубежа'],
            'slug' => ['en' => 'international-patients', 'ru' => 'pacientam-iz-za-rubezha'],
            'seo_title' => [
                'en' => 'International Patients | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Пациентам из-за рубежа — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Service packages for patients travelling to Istanbul: medical consultation, hospital admission support, accommodation, transfers and insurance assistance.',
                'ru' => 'Пакеты услуг для пациентов, приезжающих в Стамбул: медицинская консультация, помощь с госпитализацией, проживание, трансфер и поддержка по страховке.',
            ],
        ],
        'blocks' => [
            'Yurt dışı hastalar' => ['en' => 'International patients', 'ru' => 'Пациенты из-за рубежа'],
            'Yurt dışı hastalara özel' => [
                'en' => 'For international patients —',
                'ru' => 'Для зарубежных пациентов —',
            ],
            'ameliyatsız fıtık tedavisi' => [
                'en' => 'non-surgical treatment of disc herniation',
                'ru' => 'безоперационное лечение грыжи диска',
            ],
            'Konaklamadan karşılamaya tam kapasite özel hizmet.' => [
                'en' => 'A full service of its own, from accommodation to the airport welcome.',
                'ru' => 'Полный набор индивидуальных услуг — от проживания до встречи в аэропорту.',
            ],
            /* The figures of the hero fact panel. Only the thousands separator
               changes: 11.700+ is written 11,700+ in English and 11 700+ in
               Russian. */
            '30' => ['en' => '30', 'ru' => '30'],
            'yıllık hekimlik deneyimi' => ['en' => 'years of medical practice', 'ru' => 'лет врачебной практики'],
            '11.700+' => ['en' => '11,700+', 'ru' => '11 700+'],
            'hasta' => ['en' => 'patients', 'ru' => 'пациентов'],
            '27' => ['en' => '27', 'ru' => '27'],
            'girişimsel teknik' => ['en' => 'interventional techniques', 'ru' => 'интервенционных методик'],
            '28' => ['en' => '28', 'ru' => '28'],
            'bilimsel yayın' => ['en' => 'scientific publications', 'ru' => 'научных публикаций'],

            'I — Hizmet paketleri' => ['en' => 'I — Service packages', 'ru' => 'I — Пакеты услуг'],
            'paketler' => ['en' => 'service packages', 'ru' => 'пакеты услуг'],
            'Her pakette Prof. Dr. Hüsnü Süslü ile tıbbi konsültasyon yer alır. Ayrıntılar için WhatsApp ile yazın.' => [
                'en' => 'Every package includes a medical consultation with Prof. Dr. Hüsnü Süslü. Message us on WhatsApp for the details.',
                'ru' => 'В каждый пакет входит медицинская консультация с проф. д-ром Хюсню Сюслю. Подробности — напишите нам в WhatsApp.',
            ],
            'Başlangıç Hizmeti' => ['en' => 'Entry service', 'ru' => 'Базовый набор'],
            'Konaklamasız Paket' => ['en' => 'Package without accommodation', 'ru' => 'Пакет без проживания'],
            "Tıbbi Konsültasyon\nHastane Yatış Hizmetleri Desteği\nSigorta Hizmeti Desteği" => [
                'en' => "Medical consultation\nSupport with hospital admission\nSupport with insurance",
                'ru' => "Медицинская консультация\nПомощь с госпитализацией\nПоддержка по страховке",
            ],
            'Hemen Bilgi AL' => ['en' => 'Get in touch', 'ru' => 'Узнать подробнее'],
            'Hemen Bilgi Al' => ['en' => 'Get in touch', 'ru' => 'Узнать подробнее'],
            'Konaklama Hizmetli' => ['en' => 'With accommodation', 'ru' => 'С проживанием'],
            'Konaklama Dahil' => ['en' => 'Accommodation included', 'ru' => 'Проживание включено'],
            "Tıbbi Konsültasyon\nHastane Yatış Hizmetleri\nSigorta Hizmeti Desteği\nHastane Yakını Konaklama Hizmeti\nÖzel Araçlar ile Taşıma Hizmeti" => [
                'en' => "Medical consultation\nHospital admission services\nSupport with insurance\nAccommodation near the hospital\nTransfers by private vehicle",
                'ru' => "Медицинская консультация\nУслуги по госпитализации\nПоддержка по страховке\nПроживание рядом с больницей\nТрансфер на отдельном автомобиле",
            ],
            'Refakatçi Destekli' => ['en' => 'With a companion nurse', 'ru' => 'С сопровождающим'],
            "Tıbbi Konsültasyon\nHastane Yatış Hizmetleri\nHastane Refakatçi Hizmetleri\nSigorta Hizmeti Desteği\nHastane Yakını Konaklama Hizmeti\nÖzel Araç ile Taşıma Hizmeti\nHavaalanı Karşılama\nGün Boyu Yardımcı Asistan Sağlama" => [
                'en' => "Medical consultation\nHospital admission services\nCompanion nurse at the hospital\nSupport with insurance\nAccommodation near the hospital\nTransfers by private vehicle\nAirport welcome\nA support assistant throughout the day",
                'ru' => "Медицинская консультация\nУслуги по госпитализации\nСопровождающая медсестра в больнице\nПоддержка по страховке\nПроживание рядом с больницей\nТрансфер на отдельном автомобиле\nВстреча в аэропорту\nПомощник, сопровождающий вас в течение дня",
            ],

            'II — Hizmetlerimiz' => ['en' => 'II — Our services', 'ru' => 'II — Наши услуги'],
            'Her hizmet,' => ['en' => 'Every service,', 'ru' => 'Каждая услуга —'],
            'ayrıntısıyla' => ['en' => 'in detail', 'ru' => 'в подробностях'],
            'Tıbbi Konsültasyon Hizmeti Nedir?' => [
                'en' => 'What is the medical consultation service?',
                'ru' => 'Что такое медицинская консультация?',
            ],
            'Tıbbi Konsültasyon Hizmeti hastalık kaynağını bulmak, bunun için gerekli tedavi organizasyonunu yapmak olarak ifade edilir.' => [
                'en' => 'The medical consultation service means finding the source of the illness and organising the treatment that this calls for.',
                'ru' => 'Медицинская консультация — это поиск источника заболевания и организация необходимого для этого лечения.',
            ],
            'Refakatçi Hizmeti Nedir?' => [
                'en' => 'What is the companion nurse service?',
                'ru' => 'Что такое услуга сопровождающего?',
            ],
            'Eğer hastalığınız hastanede yatmayı gerekli kılıyorsa, hastanede size özel bir hemşire hizmeti sunmaktayız. Hastalığınız boyunca sadece size hizmet eder ve ihtiyaç duyduğunuz desteği size sağlar.' => [
                'en' => 'If your condition means you have to stay in hospital, we provide a nurse of your own there. Throughout your illness she looks after you alone and gives you the support you need.',
                'ru' => 'Если ваше состояние требует пребывания в стационаре, мы предоставляем в больнице отдельную медсестру. Всё время болезни она занимается только вами и оказывает нужную вам поддержку.',
            ],
            'Hasta Yakınlarına veya Hastaya Konaklama Hizmeti Nedir?' => [
                'en' => 'What is the accommodation service for patients and their families?',
                'ru' => 'Что такое услуга проживания для пациента и его близких?',
            ],
            'Anlaşmalı otellerde hastalığınız boyunca kalmanız gereken sürede size konaklama hizmeti sunuyoruz. Düzenli olarak kliniğimize ve hastaneye gitmeniz gerektiğinde bu süreyi en kısa olacak şekilde anlaşmalı otel konaklaması desteği veriyoruz.' => [
                'en' => 'We arrange accommodation in partner hotels for as long as your treatment requires. When you have to travel to our clinic and to the hospital regularly, we arrange partner hotels that keep that journey as short as possible.',
                'ru' => 'Мы размещаем вас в отелях-партнёрах на весь срок, который требует лечение. Если вам нужно регулярно приезжать в клинику и в больницу, мы подбираем отель-партнёр так, чтобы дорога занимала как можно меньше времени.',
            ],
            'Özel Araç Hizmeti Nedir?' => [
                'en' => 'What is the private vehicle service?',
                'ru' => 'Что такое услуга отдельного автомобиля?',
            ],
            'Hastalığınız süresince seyahat etmeniz gerekli durumlar otelden hastaneye, hastaneden otele sizin rahatsızlığınıza özel tasarlanmış araçlar ile seyahat etmenizi sağlıyoruz.' => [
                'en' => 'Whenever your treatment requires you to travel, we take you from the hotel to the hospital and back in vehicles fitted out for your condition.',
                'ru' => 'Когда лечение требует поездок, мы доставляем вас из отеля в больницу и обратно на автомобилях, оборудованных с учётом вашего состояния.',
            ],
            'Sigorta Desteği Nedir?' => ['en' => 'What is the insurance support?', 'ru' => 'Что такое поддержка по страховке?'],
            'Yurt içinde ve dışında özel sağlık sigortanız ile ilgili gerekli yazışmaları sizin için yapıyoruz. Böylece sigortalarınızdan yararlanma imkanı sunuyoruz. ( Kısıtlamalar ve poliçenizdeki kurallara tabidir.)' => [
                'en' => 'We handle the correspondence your private health insurance requires, in Turkey and abroad, so that you can draw on your cover. (Subject to the limits and the rules of your policy.)',
                'ru' => 'Мы ведём за вас необходимую переписку по вашей частной медицинской страховке — в Турции и за рубежом, — чтобы вы могли воспользоваться её покрытием. (С учётом ограничений и правил вашего полиса.)',
            ],
            'Gün Boyu Yardımcı Asistan Hizmeti Nedir?' => [
                'en' => 'What is the day-long support assistant?',
                'ru' => 'Что такое помощник, сопровождающий вас в течение дня?',
            ],
            'İhtiyaç olması halinde sizin yanınızdan ayrılmayacak ve gittiğiniz yerlerde size eşlik edecek bir asistan hizmeti sunmaktayız.' => [
                'en' => 'Where it is needed, we provide an assistant who stays with you and goes with you wherever you need to be.',
                'ru' => 'При необходимости мы предоставляем помощника, который остаётся рядом с вами и сопровождает вас везде, куда вам нужно.',
            ],

            'III — Hizmet verilen diğer konular' => ['en' => 'III — Other areas we cover', 'ru' => 'III — Другие направления работы'],
            'Hizmet verilen diğer' => ['en' => 'Other areas', 'ru' => 'Другие'],
            'konular' => ['en' => 'we cover', 'ru' => 'направления'],
            'Her ağrının sebebi fıtık değildir. Doğru teşhis için uzman bir hekimden destek almalısınız.' => [
                'en' => 'Not every pain is caused by a herniated disc. For an accurate diagnosis you should turn to a specialist.',
                'ru' => 'Не всякая боль вызвана грыжей диска. Для точного диагноза следует обратиться к врачу-специалисту.',
            ],

            'IV — Yurt dışından iletişim' => ['en' => 'IV — Getting in touch from abroad', 'ru' => 'IV — Связь из-за рубежа'],
            'Yurt dışından' => ['en' => 'From abroad,', 'ru' => 'Из-за рубежа —'],
            'WhatsApp ile yazın.' => ['en' => 'message us on WhatsApp.', 'ru' => 'напишите нам в WhatsApp.'],
            'Randevu ve bilgi için klinik telefonundan ya da e-posta ile de ulaşabilirsiniz.' => [
                'en' => 'You can also reach us for appointments and information on the clinic telephone or by e-mail.',
                'ru' => 'Для записи и справок с нами также можно связаться по телефону клиники или по электронной почте.',
            ],
        ],
    ],

    'algolojide-sikca-sorulan-sorular' => [
        'fields' => [
            'title' => [
                'en' => 'Frequently Asked Questions About Algology',
                'ru' => 'Часто задаваемые вопросы об алгологии',
            ],
            'slug' => ['en' => 'algology-faq', 'ru' => 'chasto-zadavaemye-voprosy'],
            'seo_title' => [
                'en' => 'Algology FAQ | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Часто задаваемые вопросы об алгологии — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Prof. Dr. Hüsnü Süslü answers the questions most often asked about algology: what it covers, which methods are used and how long treatment takes.',
                'ru' => 'Проф. д-р Хюсню Сюслю отвечает на самые частые вопросы об алгологии: чем она занимается, какие методы применяет и сколько длится лечение.',
            ],
        ],
        'blocks' => [
            'Algoloji nedir?' => ['en' => 'What is algology?', 'ru' => 'Что такое алгология?'],
            '<p>Algoloji, ağrı bilimi anlamına gelir. Kronik ve akut ağrıların tanısı, tedavisi ve yönetimiyle ilgilenen bir tıp dalıdır.</p>' => [
                'en' => '<p>Algology means the science of pain. It is the branch of medicine concerned with the diagnosis, treatment and management of chronic and acute pain.</p>',
                'ru' => '<p>Алгология — это наука о боли. Раздел медицины, который занимается диагностикой, лечением и ведением хронической и острой боли.</p>',
            ],
            'Algoloji uzmanı kimdir?' => ['en' => 'Who is an algology specialist?', 'ru' => 'Кто такой врач-алголог?'],
            '<p>Algoloji uzmanı, kronik ağrı tedavisinde uzmanlaşmış bir doktordur. Genellikle anesteziyoloji, nöroloji veya fiziksel tıp ve rehabilitasyon gibi uzmanlık alanlarından sonra ağrı tedavisi üzerine eğitim alırlar.</p>' => [
                'en' => '<p>An algology specialist is a doctor who specialises in the treatment of chronic pain. They usually train in pain treatment after a specialty such as anaesthesiology, neurology or physical medicine and rehabilitation.</p>',
                'ru' => '<p>Алголог — врач, специализирующийся на лечении хронической боли. Как правило, обучение лечению боли проходят после специализации по анестезиологии, неврологии либо физической и реабилитационной медицине.</p>',
            ],
            'Hangi ağrılar algoloji kapsamında tedavi edilir?' => [
                'en' => 'Which kinds of pain does algology treat?',
                'ru' => 'Какие виды боли лечит алгология?',
            ],
            '<p>Algoloji, bel ağrısı, boyun ağrısı, migren, kanser ağrıları, nöropatik ağrılar, fibromiyalji ve eklem ağrıları gibi kronik ağrıları tedavi eder.</p>' => [
                'en' => '<p>Algology treats chronic pain such as low back pain, neck pain, migraine, cancer pain, neuropathic pain, fibromyalgia and joint pain.</p>',
                'ru' => '<p>Алгология лечит хроническую боль: боль в пояснице и шее, мигрень, онкологическую боль, нейропатическую боль, фибромиалгию и боль в суставах.</p>',
            ],
            'Algoloji tedavilerinde hangi yöntemler kullanılır?' => [
                'en' => 'Which methods are used in algology treatments?',
                'ru' => 'Какие методы применяются в алгологии?',
            ],
            '<p>Algoloji tedavilerinde ilaç tedavisi, sinir blokajları, radyofrekans uygulamaları, nöromodülasyon, epidural enjeksiyonlar ve fizik tedavi gibi yöntemler kullanılır.</p>' => [
                'en' => '<p>Algology treatments use methods such as medication, nerve blocks, radiofrequency procedures, neuromodulation, epidural injections and physical therapy.</p>',
                'ru' => '<p>В алгологии применяются медикаментозная терапия, блокады нервов, радиочастотная абляция, нейромодуляция, эпидуральные инъекции и физиотерапия.</p>',
            ],
            'Ağrı tedavisi uzun sürer mi?' => [
                'en' => 'Does pain treatment take a long time?',
                'ru' => 'Долго ли длится лечение боли?',
            ],
            '<p>Ağrı tedavisi hastanın durumu, ağrının şiddeti ve tipi gibi faktörlere bağlı olarak değişir. Bazı durumlarda kısa sürede sonuç alınabilirken, kronik ağrılar uzun süreli tedavi gerektirebilir.</p>' => [
                'en' => '<p>Pain treatment varies with factors such as the patient\'s condition and the severity and type of the pain. In some cases results come quickly, while chronic pain may call for long-term treatment.</p>',
                'ru' => '<p>Лечение боли зависит от состояния пациента, интенсивности и характера боли. В одних случаях результат достигается быстро, а хроническая боль может потребовать длительного лечения.</p>',
            ],
            'Ağrı tedavisinde kullanılan ilaçlar bağımlılık yapar mı?' => [
                'en' => 'Are the medicines used in pain treatment addictive?',
                'ru' => 'Вызывают ли препараты для лечения боли зависимость?',
            ],
            '<p>Algoloji uzmanları, bağımlılık riskini minimize etmek için uygun doz ve tedavi yöntemleri belirler. Uygun dozda kullanılan ağrı kesici ilaçlar genellikle bağımlılık yapmaz.</p>' => [
                'en' => '<p>Algology specialists choose the doses and treatment methods that keep the risk of dependence to a minimum. Painkillers used at an appropriate dose are generally not addictive.</p>',
                'ru' => '<p>Алгологи подбирают дозы и методы лечения так, чтобы свести риск зависимости к минимуму. Обезболивающие препараты в надлежащей дозе, как правило, зависимости не вызывают.</p>',
            ],
            'Algoloji tedavileri güvenli midir?' => [
                'en' => 'Are algology treatments safe?',
                'ru' => 'Безопасно ли лечение в алгологии?',
            ],
            '<p>Algoloji tedavileri, uzman doktorlar tarafından yapıldığında genellikle güvenlidir. Tedavi sürecinde olası yan etkiler veya riskler hastayla paylaşılır.</p>' => [
                'en' => '<p>Algology treatments are generally safe when performed by specialist doctors. Possible side effects and risks are discussed with the patient during the course of treatment.</p>',
                'ru' => '<p>Лечение в алгологии, как правило, безопасно, если его проводит врач-специалист. Возможные побочные эффекты и риски обсуждаются с пациентом в ходе лечения.</p>',
            ],
            'Algoloji tedavisi sırasında ameliyat gerekli midir?' => [
                'en' => 'Is surgery necessary during algology treatment?',
                'ru' => 'Нужна ли операция при лечении в алгологии?',
            ],
            '<p>Algoloji tedavilerinde genellikle cerrahi olmayan yöntemler tercih edilir. Ancak bazı durumlarda cerrahi müdahale gerekli olabilir.</p>' => [
                'en' => '<p>Algology treatments generally favour non-surgical methods. In some cases, however, surgery may be necessary.</p>',
                'ru' => '<p>В алгологии предпочтение обычно отдаётся безоперационным методам. Однако в некоторых случаях хирургическое вмешательство может понадобиться.</p>',
            ],
            'Ağrı tedavisi herkese uygun mudur?' => [
                'en' => 'Is pain treatment suitable for everyone?',
                'ru' => 'Подходит ли лечение боли всем?',
            ],
            '<p>Her hastanın durumu farklı olduğu için ağrı tedavisi kişiye özel planlanır. Algoloji uzmanı, hastanın genel sağlık durumu ve ağrının kaynağına göre tedavi yöntemini belirler.</p>' => [
                'en' => '<p>Because every patient\'s situation is different, pain treatment is planned individually. The algology specialist chooses the method according to the patient\'s general health and the source of the pain.</p>',
                'ru' => '<p>Поскольку ситуация у каждого пациента своя, лечение боли планируется индивидуально. Алголог выбирает метод исходя из общего состояния здоровья и источника боли.</p>',
            ],
            'Algoloji tedavisi sonrasında ağrılar tamamen geçer mi?' => [
                'en' => 'Does the pain go away completely after algology treatment?',
                'ru' => 'Проходит ли боль полностью после лечения?',
            ],
            '<p>Algoloji, ağrı bilimi anlamına gelir. Kronik ve akut ağrıların tanısı, tedavisi ve yönetimiyle ilgilenen bir tıp dalıdır.</p> <p>Algoloji tedavisi, ağrıyı yönetmeyi ve hafifletmeyi amaçlar. Tamamen ağrısız bir yaşam garanti edilmez, ancak yaşam kalitesi önemli ölçüde artırılabilir.</p>' => [
                'en' => '<p>Algology means the science of pain. It is the branch of medicine concerned with the diagnosis, treatment and management of chronic and acute pain.</p> <p>Algology treatment aims to manage and relieve pain. A completely pain-free life is not guaranteed, but quality of life can be improved considerably.</p>',
                'ru' => '<p>Алгология — это наука о боли. Раздел медицины, который занимается диагностикой, лечением и ведением хронической и острой боли.</p> <p>Лечение в алгологии направлено на управление болью и её облегчение. Полностью безболезненная жизнь не гарантируется, однако качество жизни можно заметно улучшить.</p>',
            ],
        ],
    ],

    'kvkk-ve-gizlilik-politikasi' => [
        'fields' => [
            'title' => ['ru' => 'Политика конфиденциальности и защита персональных данных'],
            'slug' => ['ru' => 'politika-konfidencialnosti'],
            'seo_title' => ['ru' => 'Политика конфиденциальности — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Как на этом сайте обрабатываются персональные данные и файлы cookie в соответствии с турецким законом о защите персональных данных (KVKK).'],
        ],
    ],

    'video-galeri' => [
        'fields' => [
            'title' => ['ru' => 'Видеогалерея'],
            'slug' => ['ru' => 'videogalereya'],
            'seo_title' => ['ru' => 'Видео — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Телеинтервью и информационные видео проф. д-ра Хюсню Сюслю о боли, озонотерапии, фибромиалгии и других темах.'],
        ],
    ],

    'blog' => [
        'fields' => [
            'title' => ['en' => 'Blog', 'ru' => 'Блог'],
            'slug' => ['en' => 'blog', 'ru' => 'blog'],
            'seo_title' => [
                'en' => 'Blog | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Блог — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Articles by Prof. Dr. Hüsnü Süslü on pain, disc herniation and interventional treatments.',
                'ru' => 'Статьи проф. д-ра Хюсню Сюслю о боли, грыже диска и интервенционных методах лечения.',
            ],
        ],
    ],

    'fibromiyalji-nedir' => [
        'fields' => [
            'title' => ['ru' => 'Что такое фибромиалгия?'],
            'slug' => ['ru' => 'chto-takoe-fibromialgiya'],
            'seo_title' => ['ru' => 'Что такое фибромиалгия? Симптомы, причины и лечение'],
            'seo_description' => ['ru' => 'Что такое фибромиалгия: распространённая боль, утомляемость и нарушения сна, возможные причины, диагностика и методы лечения.'],
        ],
    ],

    /*
     * HELD BACK, and deliberately left without a translated body. The Turkish
     * has been damaged somewhere in its history: it defines algology as the
     * care of "chronic pain caused by various bacteria", says psychogenic pain
     * is pain that improves with psychological factors, and lists "separation"
     * among psychological problems. Roughly a dozen further words have been
     * swapped for unrelated ones. The Turkish needs rewriting before this page
     * exists in another language.
     */
    'algoloji-agri-polikligini-nedir' => [
        'publish' => false,
        'fields' => [
            'title' => [
                'en' => 'What Is Algology (the Pain Clinic)?',
                'ru' => 'Что такое алгология (клиника боли)?',
            ],
            'slug' => ['en' => 'what-is-algology', 'ru' => 'chto-takoe-algologiya'],
            'seo_title' => [
                'en' => 'What Is Algology (the Pain Clinic)? | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Что такое алгология (клиника боли)? — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'What algology is, which kinds of pain a pain clinic treats and which treatment methods it offers.',
                'ru' => 'Что такое алгология, с какими видами боли работает клиника боли и какие методы лечения она предлагает.',
            ],
        ],
    ],

    'nukleoplasti-islemi-nedir' => [
        'fields' => [
            'title' => ['en' => 'What Is Nucleoplasty?', 'ru' => 'Что такое нуклеопластика?'],
            'slug' => ['en' => 'what-is-nucleoplasty', 'ru' => 'chto-takoe-nukleoplastika'],
            'seo_title' => [
                'en' => 'What Is Nucleoplasty? | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Что такое нуклеопластика? — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'What nucleoplasty is, which patients it suits and which patients it does not. Answered by algology specialist Prof. Dr. Hüsnü Süslü.',
                'ru' => 'Что такое нуклеопластика, кому она подходит и кому не подходит. Отвечает алголог проф. д-р Хюсню Сюслю.',
            ],
        ],
        'blocks' => [
            'Nükleoplasti İşlemi Nedir?' => ['en' => 'What is nucleoplasty?', 'ru' => 'Что такое нуклеопластика?'],
            '<p>Nükleoplasti işlemi nedir? Hangi hastalara uygulanır, avantajları ve dezavantajları nedir?</p>' => [
                'en' => '<p>What is nucleoplasty? Which patients is it used for, and what are its advantages and disadvantages?</p>',
                'ru' => '<p>Что такое нуклеопластика? Каким пациентам её проводят, в чём её преимущества и недостатки?</p>',
            ],
            '<p>Nükleoplasti işlemi ile sizde Ağrısız ve Acısız olarak fıtıklarınızdan kurtulabilirsiniz.</p>' => [
                'en' => '<p>With nucleoplasty you too can be rid of your disc herniation without pain.</p>',
                'ru' => '<p>С помощью нуклеопластики и вы можете избавиться от грыжи диска без боли.</p>',
            ],
            'Nükleoplasti Nedir?' => ['en' => 'What is nucleoplasty?', 'ru' => 'Что такое нуклеопластика?'],
            'Nükleoplasti Hangi Fıtıklarda Uygulanır?' => [
                'en' => 'Which disc herniations is nucleoplasty used for?',
                'ru' => 'При каких грыжах применяется нуклеопластика?',
            ],
            '<p><strong>Nükleoplasti</strong>, bel ve boyun fıtıklarında uygulanan, cerrahi kesi gerektirmeyen modern bir girişimsel ağrı tedavi yöntemidir. Disk içindeki basıncı azaltarak sinir üzerindeki baskıyı hafifletmeyi amaçlar. Lokal anestezi ile uygulanır ve hastalar aynı gün günlük yaşamlarına dönebilir.</p>' => [
                'en' => '<p><strong>Nucleoplasty</strong> is a modern interventional pain treatment for lumbar and cervical disc herniation that requires no surgical incision. By lowering the pressure inside the disc, it aims to relieve the pressure on the nerve. It is performed under local anaesthesia and patients can return to their daily lives the same day.</p>',
                'ru' => '<p><strong>Нуклеопластика</strong> — современный интервенционный метод лечения боли при грыже поясничного и шейного отделов, не требующий хирургического разреза. Снижая давление внутри диска, он уменьшает давление на нерв. Процедура выполняется под местной анестезией, и пациенты могут вернуться к повседневной жизни в тот же день.</p>',
            ],
            'Nükleoplasti Nasıl Uygulanır?' => [
                'en' => 'How is nucleoplasty performed?',
                'ru' => 'Как проводится нуклеопластика?',
            ],
            '<p>İşlem, görüntüleme eşliğinde özel bir iğne yardımıyla disk içine girilerek yapılır. Disk içindeki fazla basınç kontrollü şekilde azaltılır. Yaklaşık <strong>20–30 dakika</strong> sürer ve genel anesteziye gerek yoktur.</p>' => [
                'en' => '<p>The disc is entered with a special needle under imaging guidance. The excess pressure inside the disc is reduced in a controlled way. It takes about <strong>20–30 minutes</strong> and no general anaesthesia is needed.</p>',
                'ru' => '<p>В диск входят специальной иглой под контролем визуализации. Избыточное давление внутри диска снижается контролируемым образом. Процедура занимает около <strong>20–30 минут</strong>, общая анестезия не требуется.</p>',
            ],
            'Nükleoplastinin Avantajları Nelerdir?' => [
                'en' => 'What are the advantages of nucleoplasty?',
                'ru' => 'В чём преимущества нуклеопластики?',
            ],
            '<p>Ameliyatsız bir yöntemdir; dikiş ve kesi gerektirmez. Hastanede yatışa ihtiyaç duyulmadan uygulanır. İşlem ağrısız ve konforludur, iyileşme süresi ise oldukça kısadır. Bu özellikleri sayesinde nükleoplasti, modern fıtık tedavisinde sıkça tercih edilen yöntemler arasında yer alır.</p>' => [
                'en' => '<p>It is a non-surgical method: it needs no stitches and no incision. It is performed without a hospital stay. The procedure is painless and comfortable, and recovery is quite short. These qualities place nucleoplasty among the methods often chosen in modern treatment of disc herniation.</p>',
                'ru' => '<p>Это безоперационный метод: не требуется ни швов, ни разреза. Процедура проводится без госпитализации. Она безболезненна и комфортна, а восстановление занимает совсем немного времени. Благодаря этому нуклеопластика входит в число методов, которые часто выбирают в современном лечении грыжи диска.</p>',
            ],
            'Nükleoplasti Sonrası İyileşme Süreci' => [
                'en' => 'Recovery after nucleoplasty',
                'ru' => 'Восстановление после нуклеопластики',
            ],
            '<p>İşlem sonrası hastalar genellikle <strong>aynı gün taburcu edilir</strong>. Günlük hayata dönüş hızlıdır. Doktorun önerdiği kısa süreli istirahat ve basit egzersizlerle iyileşme süreci desteklenir.</p>' => [
                'en' => '<p>After the procedure patients are usually <strong>discharged the same day</strong>. The return to daily life is quick. Recovery is supported by the short rest and the simple exercises the doctor recommends.</p>',
                'ru' => '<p>После процедуры пациентов обычно <strong>выписывают в тот же день</strong>. Возвращение к повседневной жизни происходит быстро. Восстановление поддерживают короткий отдых и простые упражнения, рекомендованные врачом.</p>',
            ],
            'Nükleoplasti Kimler İçin Uygun Değildir?' => [
                'en' => 'Who is nucleoplasty not suitable for?',
                'ru' => 'Кому нуклеопластика не подходит?',
            ],
            '<p>İleri derece fıtığı olan, ciddi sinir hasarı bulunan veya omurga stabilitesini etkileyen durumlarda nükleoplasti uygun olmayabilir. Bu nedenle işlem öncesi detaylı muayene ve görüntüleme ile hasta değerlendirmesi mutlaka yapılmalıdır.</p>' => [
                'en' => '<p>Nucleoplasty may not be suitable for an advanced herniation, serious nerve damage or conditions that affect the stability of the spine. The patient must therefore always be assessed with a detailed examination and imaging before the procedure.</p>',
                'ru' => '<p>Нуклеопластика может не подойти при выраженной грыже, серьёзном повреждении нерва или состояниях, влияющих на стабильность позвоночника. Поэтому перед процедурой пациента обязательно оценивают с помощью подробного осмотра и визуализации.</p>',
            ],
            '30 Senelik Hekim Tecrübesi' => [
                'en' => 'Thirty years of medical practice',
                'ru' => 'Тридцать лет врачебной практики',
            ],
            '<p><strong>Prof. Dr. Hüsnü Süslü</strong>, 30 yıllık hekimlik tecrübesiyle; tanıdan tedaviye kadar her aşamada bilimsel bilgi, klinik deneyim ve hasta güvenliğini bir arada sunan bir yaklaşım benimsemektedir. Yıllar içinde edinilen bu tecrübe, her hastaya özel en doğru tedavi planının oluşturulmasında önemli bir rehberdir.</p>' => [
                'en' => '<p>With 30 years of medical practice, <strong>Prof. Dr. Hüsnü Süslü</strong> follows an approach that brings scientific knowledge, clinical experience and patient safety together at every stage, from diagnosis to treatment. That experience, gathered over the years, is an important guide in drawing up the right treatment plan for each patient.</p>',
                'ru' => '<p>Имея 30 лет врачебной практики, <strong>проф. д-р Хюсню Сюслю</strong> придерживается подхода, в котором на каждом этапе — от диагностики до лечения — соединяются научные знания, клинический опыт и безопасность пациента. Накопленный за эти годы опыт служит важным ориентиром при составлении наиболее подходящего плана лечения для каждого пациента.</p>',
            ],
            '<ul><li>30+ Yıllık Tecrübe</li><li>Multi-disipliner Yaklaşım</li><li>Girişimsel Ağrı Tedavisi Uzmanlığı</li></ul>' => [
                'en' => '<ul><li>30+ years of experience</li><li>A multidisciplinary approach</li><li>Specialisation in interventional pain treatment</li></ul>',
                'ru' => '<ul><li>Более 30 лет опыта</li><li>Мультидисциплинарный подход</li><li>Специализация по интервенционному лечению боли</li></ul>',
            ],
            'Sıkça Sorulan Sorular' => ['en' => 'Frequently asked questions', 'ru' => 'Часто задаваемые вопросы'],
            '<p>Hastalar tarafından sıkça sorulan sorular ve cevapları.</p>' => [
                'en' => '<p>The questions patients ask most often, and their answers.</p>',
                'ru' => '<p>Вопросы, которые чаще всего задают пациенты, и ответы на них.</p>',
            ],
            'Nükleoplasti kalıcı bir tedavi midir?' => [
                'en' => 'Is nucleoplasty a permanent treatment?',
                'ru' => 'Нуклеопластика — это лечение навсегда?',
            ],
            '<p>Nükleoplasti, uygun hastalarda uzun süreli rahatlama sağlayabilen etkili bir tedavi yöntemidir. Ancak her fıtık yapısı ve her hasta farklıdır. Tedavinin kalıcılığı; fıtığın derecesi, hastanın yaşam tarzı ve hekimin değerlendirmesine bağlıdır.</p>' => [
                'en' => '<p>Nucleoplasty is an effective treatment that can give lasting relief in suitable patients. But every herniation and every patient is different. How long the result lasts depends on the degree of the herniation, the patient\'s way of life and the doctor\'s assessment.</p>',
                'ru' => '<p>Нуклеопластика — эффективный метод, который у подходящих пациентов может дать длительное облегчение. Но каждая грыжа и каждый пациент индивидуальны. Насколько стойким будет результат, зависит от степени грыжи, образа жизни пациента и оценки врача.</p>',
            ],
            'Nükleoplasti sonrası fıtık tekrarlar mı?' => [
                'en' => 'Can the herniation come back after nucleoplasty?',
                'ru' => 'Может ли грыжа вернуться после нуклеопластики?',
            ],
            '<p>Nükleoplasti sonrası aynı disk seviyesinde tekrar fıtık gelişme ihtimali düşüktür ancak tamamen sıfır değildir. Doktor önerilerine uyulması, ani yüklenmelerden kaçınılması ve bel–boyun sağlığını koruyucu önlemler alınması bu riski azaltır.</p>' => [
                'en' => '<p>The chance of a new herniation at the same disc level after nucleoplasty is low, but not zero. Following the doctor\'s advice, avoiding sudden strain and taking care of the lower back and neck reduce that risk.</p>',
                'ru' => '<p>Вероятность повторной грыжи на том же уровне диска после нуклеопластики низкая, но не нулевая. Соблюдение рекомендаций врача, отказ от резких нагрузок и меры по защите поясницы и шеи снижают этот риск.</p>',
            ],
            'Nükleoplasti mi ameliyat mı, hangisi daha etkilidir?' => [
                'en' => 'Which is more effective, nucleoplasty or surgery?',
                'ru' => 'Что эффективнее — нуклеопластика или операция?',
            ],
            '<p>Her fıtık ameliyat gerektirmez. Cerrahi dışı yöntemlerle tedavi edilebilen birçok hasta vardır. Nükleoplasti, ameliyat gerektirmeyen uygun vakalarda daha konforlu ve düşük riskli bir alternatif olarak tercih edilir. Nihai karar, detaylı muayene sonrası verilir.</p>' => [
                'en' => '<p>Not every herniation calls for surgery. Many patients can be treated by non-surgical methods. In suitable cases that do not require surgery, nucleoplasty is chosen as the more comfortable, lower-risk alternative. The final decision is taken after a detailed examination.</p>',
                'ru' => '<p>Не всякая грыжа требует операции. Многих пациентов можно лечить безоперационными методами. В подходящих случаях, когда операция не нужна, нуклеопластику выбирают как более комфортную альтернативу с меньшим риском. Окончательное решение принимается после подробного осмотра.</p>',
            ],
            'Nükleoplasti sonrası fizik tedavi gerekir mi?' => [
                'en' => 'Is physical therapy needed after nucleoplasty?',
                'ru' => 'Нужна ли физиотерапия после нуклеопластики?',
            ],
            '<p>Nükleoplasti her bel ve boyun fıtığı hastası için uygun değildir. İleri derecede fıtığı olan, ciddi sinir kaybı bulunan veya farklı omurga problemleri olan hastalarda alternatif tedavi yöntemleri değerlendirilir. Uygunluk, uzman hekim değerlendirmesiyle belirlenir.</p>' => [
                'en' => '<p>Nucleoplasty is not suitable for every patient with a lumbar or cervical disc herniation. For patients with an advanced herniation, serious nerve loss or other spinal problems, alternative treatments are considered. Suitability is determined by a specialist\'s assessment.</p>',
                'ru' => '<p>Нуклеопластика подходит не каждому пациенту с грыжей поясничного или шейного диска. При выраженной грыже, серьёзной утрате функции нерва или других проблемах позвоночника рассматриваются альтернативные методы лечения. Показания определяет врач-специалист.</p>',
            ],
            'Bire bir Destek Almak İçin' => [
                'en' => 'For one-to-one support',
                'ru' => 'Чтобы получить индивидуальную поддержку',
            ],
            '<p>Profesör Doktor Hüsnü Tarafından Cevaplanır.</p>' => [
                'en' => '<p>Answered by Professor Doctor Hüsnü.</p>',
                'ru' => '<p>Отвечает профессор, доктор Хюсню.</p>',
            ],
        ],
    ],
];
