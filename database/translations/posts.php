<?php

/*
|--------------------------------------------------------------------------
| Blog post translations
|--------------------------------------------------------------------------
|
| Titles, excerpts, slugs and SEO fields only. The long `body` of each post is
| still Turkish, so no post is published in a new locale: `translate:content`
| only adds a locale once every Turkish value on the record has a translation,
| and the untranslated body keeps these records out.
|
| Translate the bodies next; then these records go live on their own, with no
| change to this file.
|
*/

return [

    'agri-tedavisi-nedir' => [
        'fields' => [
            'title' => ['en' => 'What Is Pain Treatment?', 'ru' => 'Что такое лечение боли?'],
            'slug' => ['en' => 'what-is-pain-treatment', 'ru' => 'chto-takoe-lechenie-boli'],
            'excerpt' => [
                'en' => 'First of all I should say that pain is not only a complaint, it is also an illness. Yet many patients take years of pain for granted. Modern medicine, however, now makes a life without pain…',
                'ru' => 'Прежде всего нужно сказать: боль — это не только жалоба, но и самостоятельное заболевание. Однако многие пациенты годами считают боль чем-то обычным. Между тем современная медицина уже делает жизнь без боли…',
            ],
            'seo_title' => [
                'en' => 'What Is Pain Treatment? | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Что такое лечение боли? — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Prof. Dr. Hüsnü Süslü works on what pain medicine is and on interventional approaches to the treatment of pain.',
                'ru' => 'Проф. д-р Хюсню Сюслю занимается вопросами медицины боли и интервенционными подходами к её лечению.',
            ],
        ],
    ],

    'algolojide-sikca-sorulan-sorular' => [
        'fields' => [
            'title' => [
                'en' => 'Frequently Asked Questions About Algology',
                'ru' => 'Часто задаваемые вопросы об алгологии',
            ],
            'slug' => ['en' => 'algology-frequently-asked-questions', 'ru' => 'voprosy-ob-algologii'],
            'excerpt' => [
                'en' => 'Algology is the branch of medicine concerned with the diagnosis and treatment of pain. Not only lumbar and cervical disc herniation, but also trigeminal neuralgia (facial pain), complex regional pain syndrome (reflex sympathetic dystrophy), cancer…',
                'ru' => 'Алгология — раздел медицины, который занимается диагностикой и лечением боли. Речь идёт не только о грыже поясничного и шейного дисков, но и о невралгии тройничного нерва (лицевой боли), комплексном регионарном болевом синдроме (рефлекторной симпатической дистрофии), онкологической…',
            ],
            'seo_title' => [
                'en' => 'Algology FAQ | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Часто задаваемые вопросы об алгологии — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Prof. Dr. Hüsnü Süslü answers the questions patients ask most often about algology.',
                'ru' => 'Проф. д-р Хюсню Сюслю отвечает на вопросы, которые пациенты чаще всего задают об алгологии.',
            ],
        ],
    ],

    'agri-uzmani' => [
        'fields' => [
            'title' => ['en' => 'Pain Specialist', 'ru' => 'Специалист по лечению боли'],
            'slug' => ['en' => 'pain-specialist', 'ru' => 'specialist-po-lecheniyu-boli'],
            'excerpt' => [
                'en' => 'What is algology? Algology is the specialty known in medicine as the science of pain. "Algology specialist" is the title given to doctors trained in the diagnosis and treatment of chronic pain in particular…',
                'ru' => 'Что такое алгология? Алгология — специальность, которую в медицине называют наукой о боли. «Алголог» — звание, которое получают врачи, прошедшие подготовку по диагностике и лечению прежде всего хронической боли…',
            ],
            'seo_title' => [
                'en' => 'Pain Specialist | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Специалист по лечению боли — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'An algology specialist is a doctor trained in the diagnosis and treatment of chronic pain in particular.',
                'ru' => 'Алголог — врач, прошедший подготовку по диагностике и лечению прежде всего хронической боли.',
            ],
        ],
    ],

    'ameliyatsiz-fitik-tedavisi' => [
        'fields' => [
            'title' => [
                'en' => 'Non-Surgical Disc Herniation Treatment',
                'ru' => 'Безоперационное лечение грыжи диска',
            ],
            'slug' => [
                'en' => 'non-surgical-disc-herniation-treatment',
                'ru' => 'bezoperacionnoe-lechenie-gryzhi-diska',
            ],
            'excerpt' => [
                'en' => 'If a knife-like pain in your lower back makes it hard to get out of bed in the morning, or if you feel an electric shock running down your leg as you walk, you have probably reached the point of "I cannot take this any more, operate…"',
                'ru' => 'Если из-за режущей боли в пояснице вам трудно вставать по утрам или при ходьбе в ногу словно бьёт током, вы, скорее всего, уже дошли до мысли «больше терпеть не могу, пусть оперируют…»',
            ],
            'seo_title' => [
                'en' => 'Non-Surgical Disc Herniation Treatment | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Безоперационное лечение грыжи диска — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Although surgery is often thought to be unavoidable for a disc herniation, there are many effective non-surgical treatments available today.',
                'ru' => 'Хотя операцию при грыже диска часто считают неизбежной, сегодня существует немало эффективных безоперационных методов лечения.',
            ],
        ],
    ],

    'ozon-tedavisi' => [
        'fields' => [
            'title' => ['ru' => 'Озонотерапия'],
            'slug' => ['ru' => 'ozonoterapiya'],
            'excerpt' => ['ru' => 'Озонотерапия предлагает действенное решение при ведении хронической боли. Алгологи с успехом применяют этот метод при грыже поясничного и шейного отделов, боли в суставах и фибромиалгии.…'],
            'seo_title' => ['ru' => 'Озонотерапия при боли — проф. д-р Хюсню Сюслю'],
            'seo_description' => ['ru' => 'Медицинская озонотерапия при грыже диска, боли в суставах и фибромиалгии: как она работает, кому подходит и чего ожидать.'],
        ],
    ],

    'ameliyatsiz-bel-fitigi-tedavisi' => [
        'fields' => [
            'title' => [
                'en' => 'Non-Surgical Treatment for Lumbar Disc Herniation',
                'ru' => 'Безоперационное лечение грыжи поясничного диска',
            ],
            'slug' => [
                'en' => 'non-surgical-lumbar-disc-herniation-treatment',
                'ru' => 'bezoperacionnoe-lechenie-gryzhi-poyasnicy',
            ],
            'excerpt' => [
                'en' => 'Low back pain is one of the great problems of modern life, and it lowers quality of life considerably. Sadly, many patients believe the answer lies in surgery alone. The picture is not…',
                'ru' => 'Боль в пояснице — одна из главных проблем современной жизни, и она заметно снижает качество жизни. К сожалению, многие пациенты уверены, что решение только в операции. На деле всё совсем…',
            ],
            'seo_title' => [
                'en' => 'Non-Surgical Treatment for Lumbar Disc Herniation | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Безоперационное лечение грыжи поясничного диска — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Pain caused by a disc herniation can be brought to an end without surgery. Non-surgical treatment of a disc herniation is possible.',
                'ru' => 'Боль, вызванную грыжей диска, можно устранить без операции. Безоперационное лечение грыжи диска возможно.',
            ],
        ],
    ],

    'noral-foraminal-steroz' => [
        'fields' => [
            'title' => ['en' => 'Neural Foraminal Stenosis', 'ru' => 'Фораминальный стеноз'],
            'slug' => ['en' => 'neural-foraminal-stenosis', 'ru' => 'foraminalnyy-stenoz'],
            'excerpt' => [
                'en' => 'Neural foraminal stenosis is a condition in which the nerve roots come under pressure because the canals the spinal nerves pass through (the foramina) have narrowed. It causes complaints related to nerve compression…',
                'ru' => 'Фораминальный стеноз — состояние, при котором нервные корешки сдавливаются из-за сужения каналов (отверстий), через которые проходят нервы позвоночника. Это вызывает жалобы, связанные с защемлением нерва…',
            ],
            'seo_title' => [
                'en' => 'Neural Foraminal Stenosis | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Фораминальный стеноз — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Foraminal stenosis is a condition often confused with a disc herniation. See an algology specialist. We wish you good health.',
                'ru' => 'Фораминальный стеноз часто путают с грыжей диска. Обратитесь к врачу-алгологу. Желаем вам здоровья.',
            ],
        ],
    ],

    'ameliyatsiz-boyun-fitigi-tedavisi' => [
        'fields' => [
            'title' => [
                'en' => 'Non-Surgical Treatment for Cervical Disc Herniation',
                'ru' => 'Безоперационное лечение грыжи шейного диска',
            ],
            'slug' => [
                'en' => 'non-surgical-cervical-disc-herniation-treatment',
                'ru' => 'bezoperacionnoe-lechenie-gryzhi-shei',
            ],
            'excerpt' => [
                'en' => 'Non-surgical treatment for a cervical disc herniation and the algology specialty. Treatment options without a scalpel and without stitches, using interventional pain methods. WhatsApp information line 0216 234 18 81. Four non-surgical interventional methods…',
                'ru' => 'Безоперационное лечение грыжи шейного диска и специальность алгология. Варианты лечения без скальпеля и швов с помощью интервенционных методов обезболивания. Информационная линия WhatsApp: 0216 234 18 81. Четыре безоперационных интервенционных метода…',
            ],
            'seo_title' => [
                'en' => 'Non-Surgical Treatment for Cervical Disc Herniation | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Безоперационное лечение грыжи шейного диска — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'The pain caused by a disc herniation can be brought to an end without surgery. Non-surgical treatment of a disc herniation is possible.',
                'ru' => 'Боль, вызванную грыжей диска, можно устранить без операции. Безоперационное лечение грыжи диска возможно.',
            ],
        ],
    ],

    'bel-fitigi-belirtileri-nelerdir' => [
        'fields' => [
            'title' => [
                'en' => 'What Are the Symptoms of a Lumbar Disc Herniation?',
                'ru' => 'Каковы симптомы грыжи поясничного диска?',
            ],
            'slug' => [
                'en' => 'symptoms-of-lumbar-disc-herniation',
                'ru' => 'simptomy-gryzhi-poyasnichnogo-diska',
            ],
            'excerpt' => [
                'en' => 'Low back pain is without doubt one of the most common health problems of modern life. Even so, not every episode of low back pain is a simple muscle strain. Pain that radiates into the leg, numbness…',
                'ru' => 'Боль в пояснице, без сомнения, одна из самых распространённых проблем со здоровьем в современной жизни. И всё же не всякая боль в пояснице — это простое мышечное напряжение. Особенно если боль отдаёт в ногу, есть онемение…',
            ],
            'seo_title' => [
                'en' => 'What Are the Symptoms of a Lumbar Disc Herniation? | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Каковы симптомы грыжи поясничного диска? — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'What is a lumbar disc herniation? Is every herniation treated with surgery? What are its symptoms and treatment options?',
                'ru' => 'Что такое грыжа поясничного диска? Каждую ли грыжу лечат операцией? Каковы её симптомы и методы лечения?',
            ],
        ],
    ],

    'nukleoplasti-islemi-nedir' => [
        'fields' => [
            'title' => ['en' => 'What Is Nucleoplasty?', 'ru' => 'Что такое нуклеопластика?'],
            'slug' => ['en' => 'what-is-nucleoplasty', 'ru' => 'chto-takoe-nukleoplastika'],
            'excerpt' => [
                'en' => 'Nucleoplasty is one of the most effective non-surgical methods we use to relieve the pain of a lumbar or cervical disc herniation. Most patients given the diagnosis are, without doubt, unsettled by the idea of surgery…',
                'ru' => 'Нуклеопластика — один из самых действенных безоперационных методов, которые мы применяем, чтобы избавить пациента от боли при грыже поясничного или шейного диска. Большинство пациентов с таким диагнозом, безусловно, пугает мысль об операции…',
            ],
            'seo_title' => [
                'en' => 'What Is Nucleoplasty? | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Что такое нуклеопластика? — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'What is nucleoplasty? Recover without a scalpel and without pain with this method used in the non-surgical treatment of a disc herniation. Click for details and appointments.',
                'ru' => 'Что такое нуклеопластика? Этот метод безоперационного лечения грыжи диска позволяет восстановиться без скальпеля и без боли. Подробности и запись — по ссылке.',
            ],
        ],
    ],

    'sinir-sikismasi-ile-fitik-arasindaki-fark-nedir' => [
        'fields' => [
            'title' => [
                'en' => 'What Is the Difference Between a Pinched Nerve and a Disc Herniation?',
                'ru' => 'Чем защемление нерва отличается от грыжи диска?',
            ],
            'slug' => [
                'en' => 'pinched-nerve-vs-disc-herniation',
                'ru' => 'zashchemlenie-nerva-ili-gryzha-diska',
            ],
            'excerpt' => [
                'en' => 'First of all I should say that a pinched nerve and a disc herniation are among the most common causes of low back and neck pain. The two are often confused, though. Many patients say "my nerve is pinched"…',
                'ru' => 'Прежде всего нужно сказать, что защемление нерва и грыжа диска — среди самых частых причин боли в пояснице и шее. Однако эти два понятия часто путают. Например, многие пациенты говорят «у меня защемило нерв»…',
            ],
            'seo_title' => [
                'en' => 'Pinched Nerve or Disc Herniation? | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Защемление нерва или грыжа диска? — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'What are the differences between a pinched nerve and a disc herniation, and what part does algology play in the relationship between them?',
                'ru' => 'В чём разница между защемлением нерва и грыжей диска и какова роль алгологии в их взаимосвязи?',
            ],
        ],
    ],

    'fitik-ameliyati-ne-zaman-gercekten-gerekir' => [
        'fields' => [
            'title' => [
                'en' => 'When Is Disc Herniation Surgery Really Necessary?',
                'ru' => 'Когда операция при грыже диска действительно нужна?',
            ],
            'slug' => [
                'en' => 'when-is-disc-surgery-necessary',
                'ru' => 'kogda-nuzhna-operaciya-pri-gryzhe',
            ],
            'excerpt' => [
                'en' => 'First of all I should say that about 85-90% of patients diagnosed with a lumbar disc herniation do not need surgery. Many patients, however, assume they will be on the operating table the moment they hear the diagnosis. What is more, this…',
                'ru' => 'Прежде всего нужно сказать, что примерно 85-90 % пациентов с диагнозом «грыжа поясничного диска» операция не требуется. Однако многие, едва услышав диагноз, уверены, что окажутся на операционном столе. Более того, это…',
            ],
            'seo_title' => [
                'en' => 'When Is Disc Herniation Surgery Really Necessary? | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Когда операция при грыже диска действительно нужна? — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'When is disc herniation surgery really necessary? What are the non-surgical methods? Is surgery compulsory for every herniation?',
                'ru' => 'Когда операция при грыже диска действительно нужна? Какие есть безоперационные методы? Обязательна ли операция при любой грыже?',
            ],
        ],
    ],

    'lazer-ile-bel-boyun-fitigi-tedavisi' => [
        'fields' => [
            'title' => [
                'en' => 'Laser Treatment for Lumbar and Cervical Disc Herniation',
                'ru' => 'Лазерное лечение грыжи поясничного и шейного отделов',
            ],
            'slug' => [
                'en' => 'laser-treatment-lumbar-cervical-disc-herniation',
                'ru' => 'lazernoe-lechenie-gryzhi-poyasnicy-i-shei',
            ],
            'excerpt' => [
                'en' => 'First of all I should say that laser treatment for lumbar and cervical disc herniation is one of the most effective minimally invasive answers modern medicine offers. Many patients may not have heard of the method, because…',
                'ru' => 'Прежде всего нужно сказать, что лазерное лечение грыжи поясничного и шейного отделов — одно из самых действенных малоинвазивных решений, которые предлагает современная медицина. Однако многие пациенты о нём могли и не слышать. Потому что…',
            ],
            'seo_title' => [
                'en' => 'Laser Treatment for Lumbar and Cervical Disc Herniation | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Лазерное лечение грыжи поясничного и шейного отделов — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Which non-surgical methods treat a disc herniation? Learn about the effective options for low back and neck pain, and about laser treatment.',
                'ru' => 'Какие безоперационные методы применяются при грыже диска? Узнайте об эффективных вариантах при боли в пояснице и шее и о лазерном лечении.',
            ],
        ],
    ],

    'lazer-ile-fitik-tedavisi-ne-kadar-basarili' => [
        'fields' => [
            'title' => [
                'en' => 'How Successful Is Laser Treatment for a Disc Herniation',
                'ru' => 'Насколько эффективно лазерное лечение грыжи диска',
            ],
            'slug' => [
                'en' => 'how-successful-is-laser-disc-treatment',
                'ru' => 'naskolko-effektivno-lazernoe-lechenie',
            ],
            'excerpt' => [
                'en' => 'Laser treatment for a disc herniation: the success rate in lumbar and cervical herniation. Laser treatment and an analysis of its results. Laser treatment for a disc herniation is today, for patients who want to be rid of a lumbar or cervical herniation, one of the most…',
                'ru' => 'Лазерное лечение грыжи диска: показатели эффективности при грыже поясничного и шейного отделов. Лазерное лечение грыжи и анализ результатов. Сегодня для пациентов, желающих избавиться от грыжи поясничного или шейного диска, лазерное лечение — один из самых…',
            ],
            'seo_title' => [
                'en' => 'How Successful Is Laser Treatment for a Disc Herniation | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Насколько эффективно лазерное лечение грыжи диска — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'What is the success rate of laser treatment for a disc herniation? Is there a risk of recurrence? How does it differ from the other treatments?',
                'ru' => 'Какова эффективность лазерного лечения грыжи диска? Есть ли риск рецидива? Чем оно отличается от других методов?',
            ],
        ],
    ],

    'bel-fitiginda-hangi-bolume-gidilir-dogru-doktor-secimi-rehberi' => [
        'fields' => [
            'title' => [
                'en' => 'Which Department for a Lumbar Disc Herniation? A Guide to Choosing the Right Doctor',
                'ru' => 'К какому врачу идти при грыже поясничного диска? Руководство по выбору специалиста',
            ],
            'slug' => [
                'en' => 'which-doctor-for-lumbar-disc-herniation',
                'ru' => 'k-kakomu-vrachu-pri-gryzhe-poyasnicy',
            ],
            'excerpt' => [
                'en' => 'The right address when a lumbar disc herniation is suspected: algology specialist Professor Doctor Hüsnü Süslü. The most useful answer to the question of which department to go to is algology (pain…',
                'ru' => 'Куда обращаться при подозрении на грыжу поясничного диска: алголог, профессор, доктор Хюсню Сюслю. Самый действенный ответ на вопрос, к какому врачу идти при грыже поясничного диска, — алгология (медицина…',
            ],
            'seo_title' => [
                'en' => 'Which Department for a Lumbar Disc Herniation? | Prof. Dr. Hüsnü Süslü',
                'ru' => 'К какому врачу идти при грыже поясничного диска? — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'What is the success rate of laser treatment for a disc herniation? Is there a risk of recurrence? How does it differ from the other treatments?',
                'ru' => 'Какова эффективность лазерного лечения грыжи диска? Есть ли риск рецидива? Чем оно отличается от других методов?',
            ],
        ],
    ],

    'bel-agrisi-icin-hangi-bolumden-randevu-alinir' => [
        'fields' => [
            'title' => [
                'en' => 'Which Department Should You Book for Low Back Pain? A Detailed Guide (2026)',
                'ru' => 'К какому специалисту записаться при боли в пояснице? Подробное руководство (2026)',
            ],
            'slug' => [
                'en' => 'which-department-for-low-back-pain',
                'ru' => 'k-kakomu-specialistu-pri-boli-v-poyasnice',
            ],
            'excerpt' => [
                'en' => 'Low back pain is one of the health problems that lowers quality of life the most. Most patients are afraid, assuming they will go straight onto the operating table; today, however, a large share of low back pain…',
                'ru' => 'Боль в пояснице — одна из проблем со здоровьем, которая сильнее всего снижает качество жизни. Большинство пациентов боятся, полагая, что сразу окажутся на операционном столе; однако сегодня значительная часть болей в пояснице…',
            ],
            'seo_title' => [
                'en' => 'Which Department Should You Book for Low Back Pain? (2026) | Prof. Dr. Hüsnü Süslü',
                'ru' => 'К какому специалисту записаться при боли в пояснице? (2026) — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Read our detailed explanation of which department to book an appointment with for low back pain.',
                'ru' => 'Прочитайте наше подробное объяснение о том, к какому специалисту записываться при боли в пояснице.',
            ],
        ],
    ],

    'bel-fitigi-ayaga-vurur-mu' => [
        'fields' => [
            'title' => [
                'en' => 'Can a Lumbar Disc Herniation Radiate to the Foot?',
                'ru' => 'Отдаёт ли грыжа поясничного диска в стопу?',
            ],
            'slug' => [
                'en' => 'can-lumbar-disc-herniation-reach-the-foot',
                'ru' => 'otdaet-li-gryzha-v-stopu',
            ],
            'excerpt' => [
                'en' => 'Lumbar disc herniation • Symptoms • Pain treatment. Can a lumbar disc herniation radiate to the foot? In some patients a lumbar disc herniation can spread the pain from the lower back to the hip and from the leg down to the foot. Pain reaching the foot often comes with numbness, tingling and…',
                'ru' => 'Грыжа поясничного диска • Симптомы • Лечение боли. Отдаёт ли грыжа поясничного диска в стопу? У части пациентов грыжа поясничного диска может распространять боль от поясницы в ягодицу и от ноги до стопы. Боль, доходящую до стопы, часто сопровождают онемение, покалывание и…',
            ],
            'seo_title' => [
                'en' => 'Can a Lumbar Disc Herniation Radiate to the Foot? | Prof. Dr. Hüsnü Süslü',
                'ru' => 'Отдаёт ли грыжа поясничного диска в стопу? — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Can a lumbar disc herniation radiate to the foot? Learn the signs of pain spreading to the foot, numbness, tingling and nerve compression.',
                'ru' => 'Отдаёт ли грыжа поясничного диска в стопу? Узнайте о признаках боли, отдающей в стопу, об онемении, покалывании и сдавлении нерва.',
            ],
        ],
    ],

    'boyun-fitigi-icin-hangi-doktora-gidilir' => [
        'fields' => [
            'title' => [
                'en' => 'Which Doctor Should You See for a Cervical Disc Herniation?',
                'ru' => 'К какому врачу идти при грыже шейного диска?',
            ],
            'slug' => [
                'en' => 'which-doctor-for-cervical-disc-herniation',
                'ru' => 'k-kakomu-vrachu-pri-gryzhe-shei',
            ],
            'excerpt' => [
                'en' => 'Cervical disc herniation • Choosing a specialist • Pain treatment. Which doctor should you see for a cervical disc herniation? Going to the right specialty when a cervical herniation is suspected speeds up the course of treatment. Pain, numbness, complaints radiating into the arm and…',
                'ru' => 'Грыжа шейного диска • Выбор специалиста • Лечение боли. К какому врачу идти при грыже шейного диска? Обращение к нужной специальности при подозрении на грыжу шейного диска ускоряет лечение. Боль, онемение, жалобы, отдающие в руку, и…',
            ],
            'seo_title' => [
                'en' => 'Which Doctor for a Cervical Disc Herniation? | Prof. Dr. Hüsnü Süslü',
                'ru' => 'К какому врачу идти при грыже шейного диска? — проф. д-р Хюсню Сюслю',
            ],
            'seo_description' => [
                'en' => 'Which doctor should you see for a cervical disc herniation? Learn when an algology specialist, a neurosurgeon or a neurologist is the right choice.',
                'ru' => 'К какому врачу идти при грыже шейного диска? Узнайте, в каких случаях выбирают алголога, нейрохирурга или невролога.',
            ],
        ],
    ],
];
