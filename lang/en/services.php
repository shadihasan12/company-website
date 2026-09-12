<?php

/*
| Keyed by the `key` in config('site.services'). Keep the keys in sync.
|
| `body`, `inclusions`, `timeline` and `faqs` are seeded into the database
| by ServiceSeeder and are editable from the admin panel afterwards. This
| file is the starting draft, not the live copy.
|
| Pricing is deliberately absent. Showing a starting price filters out
| time-wasters and raises qualified enquiries, but no figures have been
| supplied, so every service currently ends on "request a quote".
*/

return [
    'mobile-apps' => [
        'title' => 'Mobile Apps',
        'tagline' => 'iOS & Android, one codebase',
        'excerpt' => 'Production Flutter apps shipped through App Store and Play Store review — real-time chat, in-app calling, payments and subscriptions.',
        'body' => 'Most mobile projects do not fail in development. They fail at review, or three months later when nobody can safely change the code. We build in Flutter on clean architecture with BLoC state management, so one codebase serves iOS and Android and a new developer can find their way around it. We take the app through store review ourselves — including compliance fixes and rejection turnarounds — and wire up crash reporting before launch rather than after the first bad rating.',
        'timeline' => '8–16 weeks for a first release',
        'inclusions' => [
            'Flutter app for iOS and Android from a single codebase',
            'Clean architecture with BLoC, dependency injection and type-safe models',
            'Real-time features: chat, WebRTC calling, push notifications',
            'Payments and subscriptions via Apple Pay and Google Play Billing',
            'Offline persistence and resilient API integration',
            'App Store and Play Store submission, including compliance fixes',
            'Crash reporting and release automation wired in before launch',
        ],
        'faqs' => [
            [
                'question' => 'Do we get one app or two?',
                'answer' => 'One codebase, two native apps. iOS and Android are built from the same Flutter project, so a feature is written once and a bug is fixed once — which is where the cost saving actually comes from.',
            ],
            [
                'question' => 'Who handles App Store and Play Store submission?',
                'answer' => 'We do. That includes the store listings, privacy declarations, compliance fixes and turning around rejections. Store review is our problem, not yours.',
            ],
            [
                'question' => 'What happens if the app is rejected?',
                'answer' => 'We fix it and resubmit. Rejections are routine on first submission, and handling them is part of the engagement rather than a change request.',
            ],
            [
                'question' => 'Can you take over an existing app?',
                'answer' => 'Often, yes. We start with a short audit of the codebase and tell you honestly whether continuing it or rebuilding is the better use of your money.',
            ],
        ],
    ],

    'web-development' => [
        'title' => 'Web Development',
        'tagline' => 'Fast, findable websites',
        'excerpt' => 'Marketing sites and web platforms that load quickly, rank well and turn visitors into qualified leads.',
        'body' => 'A website that takes four seconds to load is a website most people never see. We build server-rendered sites that are fast on a mid-range phone over mobile data, structured so search engines and AI assistants can actually understand what your business does, and instrumented so you can tell which pages bring enquiries and which do not.',
        'timeline' => '4–10 weeks',
        'inclusions' => [
            'Design and build, or build from your existing design',
            'Server-rendered pages with Core Web Vitals held inside budget',
            'Structured data so search engines and AI assistants understand the site',
            'A content admin panel so your team edits pages without a developer',
            'Contact and lead capture wired into email and your CRM',
            'Analytics and conversion tracking configured, not just installed',
        ],
        'faqs' => [
            [
                'question' => 'Will we be able to edit it ourselves?',
                'answer' => 'Yes. Every project ships with an admin panel covering the content you are likely to change — pages, posts, case studies, team-facing settings.',
            ],
            [
                'question' => 'Do you do the SEO as well?',
                'answer' => 'We handle the technical side: site structure, performance, structured data, sitemaps, internal linking. Ongoing content and link building is a separate conversation.',
            ],
            [
                'question' => 'Can you work with our existing brand?',
                'answer' => 'Yes. Send us the brand guidelines or the Figma file and we build to it. If there is no brand yet, we can propose a direction first.',
            ],
        ],
    ],

    'dashboards' => [
        'title' => 'Dashboards',
        'tagline' => 'Your data, finally readable',
        'excerpt' => 'Admin panels and analytics dashboards that give your team the numbers they need without waiting on a developer.',
        'body' => 'Most operational data already exists somewhere — it is just spread across a database, three spreadsheets and someone\'s inbox. We build the layer that pulls it together and makes it answerable: filters that match how your team actually asks questions, exports that do not need cleaning up, and permissions so people see what they should.',
        'timeline' => '4–10 weeks',
        'inclusions' => [
            'Role-based access so each team sees only what it should',
            'Filtering, search and sorting built around real questions',
            'Charts and metrics chosen for decisions, not decoration',
            'CSV and Excel export that needs no cleaning up',
            'Bulk actions and audit trails on anything destructive',
            'Responsive down to phone width for people away from a desk',
        ],
        'faqs' => [
            [
                'question' => 'Can it read from our existing database?',
                'answer' => 'Usually. We can connect to an existing database directly, or sync through an API where a live connection is not safe or practical.',
            ],
            [
                'question' => 'How do you handle permissions?',
                'answer' => 'Role-based, defined with you up front. Destructive actions carry an audit trail, so you can always see who changed what.',
            ],
        ],
    ],

    'custom-systems' => [
        'title' => 'Custom Systems',
        'tagline' => 'Built around how you work',
        'excerpt' => 'Bespoke internal software for the processes no off-the-shelf product covers.',
        'body' => 'Off-the-shelf software is usually the right answer. When it is not — when the process is the thing that makes your business work, and every product on the market forces you to do it a different way — building it is cheaper than bending the company around a tool. We start by mapping the process as it actually runs, including the parts that live in people\'s heads, and build only what earns its place.',
        'timeline' => '10–20 weeks, in staged releases',
        'inclusions' => [
            'Process mapping workshops before any code is written',
            'Staged delivery so the first useful piece ships early',
            'Integrations with the systems you already run',
            'Role-based access, audit trails and data export',
            'Documentation and handover written for your team',
            'Automated tests on the logic your business depends on',
        ],
        'faqs' => [
            [
                'question' => 'How do we know custom is the right call?',
                'answer' => 'Often it is not, and we will say so. The discovery phase exists partly to establish whether an existing product would serve you better — that answer costs you a fraction of a build.',
            ],
            [
                'question' => 'What happens if we want to bring it in-house later?',
                'answer' => 'You own the code throughout. Handover includes documentation, environment setup and a walkthrough with whoever takes it on.',
            ],
        ],
    ],

    'erp' => [
        'title' => 'ERP Systems',
        'tagline' => 'One system, whole business',
        'excerpt' => 'Inventory, finance, HR and operations connected in a single system, with migration from whatever you run today.',
        'body' => 'ERP projects fail on migration and adoption far more often than on features. We stage the rollout module by module so the business keeps running, move your historical data with reconciliation you can check, and train the people who will use it daily rather than only the managers who signed it off.',
        'timeline' => '16–32 weeks, rolled out in modules',
        'inclusions' => [
            'Module-by-module rollout so operations never stop',
            'Data migration from your current system, with reconciliation reports',
            'Inventory, purchasing, sales, finance and HR modules as needed',
            'Role-based access and approval workflows',
            'Reporting for the numbers leadership actually asks for',
            'Training for daily users, not just administrators',
        ],
        'faqs' => [
            [
                'question' => 'Can you migrate our existing data?',
                'answer' => 'Yes, and the reconciliation reports are part of the deliverable — you should be able to verify that nothing was lost rather than take our word for it.',
            ],
            [
                'question' => 'Do we have to move everything at once?',
                'answer' => 'No, and you should not. We roll out module by module so the business keeps running and each stage can be corrected before the next.',
            ],
            [
                'question' => 'Why build rather than buy?',
                'answer' => 'For many companies, buying is right. Building is worth it when your operation genuinely does not fit the shape of the products available, or when licensing costs at your headcount outweigh the build over a few years.',
            ],
        ],
    ],

    'ai-solutions' => [
        'title' => 'AI Solutions',
        'tagline' => 'AI that earns its place',
        'excerpt' => 'Assistants, recognition and automation built into your product where they measurably save time — not as a demo.',
        'body' => 'Most AI features are demos that never survive contact with real users. The ones that last are narrow, measurable and wired into an existing workflow. We have shipped face-search photo matching over AWS Rekognition and in-app assistants to production apps, and we start every AI engagement by asking what it would save and how you would know.',
        'timeline' => '6–14 weeks',
        'inclusions' => [
            'A defined success metric before development starts',
            'In-app assistants and conversational interfaces',
            'Image and face recognition, search and matching',
            'Document extraction, classification and summarisation',
            'Evaluation harness so quality is measured, not assumed',
            'Cost controls and fallbacks for when a model is slow or unavailable',
        ],
        'faqs' => [
            [
                'question' => 'How do we know the AI is actually working?',
                'answer' => 'We agree a success metric before development starts and build an evaluation harness alongside the feature. If it cannot be measured, we would rather talk you out of it.',
            ],
            [
                'question' => 'What about our data?',
                'answer' => 'We scope data handling explicitly at the start — what leaves your infrastructure, what does not, and which providers are acceptable to you.',
            ],
            [
                'question' => 'What does it cost to run?',
                'answer' => 'Model usage is an ongoing cost, so we estimate it during discovery and build in caps and fallbacks rather than leaving you exposed to a surprise bill.',
            ],
        ],
    ],

    'backend-cloud' => [
        'title' => 'Backend & Cloud',
        'tagline' => 'Infrastructure that stays up',
        'excerpt' => 'APIs, real-time services, cloud architecture and CI/CD pipelines designed to scale and to be handed over cleanly.',
        'body' => 'The backend is the part nobody notices until it breaks. We build APIs and real-time services with the boring things in place from the start: automated tests, CI pipelines, monitoring, backups and a deployment process that does not depend on one person remembering the steps.',
        'timeline' => '6–14 weeks',
        'inclusions' => [
            'REST and real-time APIs with documented contracts',
            'WebSocket and WebRTC services for live features',
            'Cloud architecture sized to real traffic, not worst-case guesses',
            'CI/CD pipelines with automated tests and safe rollbacks',
            'Monitoring, error tracking and alerting from day one',
            'Backups, restore drills and a documented runbook',
        ],
        'faqs' => [
            [
                'question' => 'Which cloud do you work with?',
                'answer' => 'AWS most often, and Google Cloud or a plain VPS where that is a better fit. We will recommend based on your team and budget rather than habit.',
            ],
            [
                'question' => 'Can you take over infrastructure someone else built?',
                'answer' => 'Yes. We start with an audit covering security, cost and reliability, and give you a prioritised list before changing anything.',
            ],
        ],
    ],
];
