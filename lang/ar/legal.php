<?php

/*
| Written to describe what this application actually does — the fields the
| forms collect, the data stored alongside them, and the third parties
| involved. It is accurate, not legal advice.
|
| TODO markers below are detected by `php artisan launch:check`, which
| blocks until they are replaced. Have a lawyer review both documents and
| fill in the registration details before going live.
*/

return [
    'updated' => 'Last updated',
    'review_notice' => 'TODO-LEGAL-REVIEW',

    'privacy' => [
        'title' => 'Privacy policy',
        'intro' => 'This policy explains what :company collects through this website, why, and what you can ask us to do about it.',

        'sections' => [
            [
                'heading' => 'What you send us',
                'body' => 'When you use the contact form we collect your name, email address and message, and — if you choose to provide them — your phone number, company, the service you are interested in, your budget range and your timeline. The project scoping form additionally records the platforms, features and project stage you select. The newsletter form collects only an email address. If a careers page is open, an application collects your name, email, the role you are interested in, a link you provide and your message.',
            ],
            [
                'heading' => 'What we record automatically',
                'body' => 'With each submission we store your IP address, browser user agent, the page you came from and the language you were browsing in. These are used to detect automated submissions and to limit how often a single source can submit. We do not build profiles from them and we do not use them for advertising.',
            ],
            [
                'heading' => 'Cookies and local storage',
                'body' => 'The site sets a session cookie so forms can be submitted securely, and stores your light or dark theme preference in your browser. Neither is used for tracking. If analytics is enabled, that provider may set its own cookies; where we use Plausible, no cookies are set and no personal data is collected.',
            ],
            [
                'heading' => 'Who else is involved',
                'body' => 'Spam protection may be handled by Cloudflare Turnstile, which receives your IP address to verify that you are not a bot. Email notifications are delivered through our email provider. Analytics, where enabled, is provided by Google Analytics or Plausible. We do not sell your data and we do not share it with anyone else.',
            ],
            [
                'heading' => 'How long we keep it',
                'body' => 'Enquiries are kept for as long as they are commercially relevant, and then deleted. You can ask us to delete yours sooner at any time.',
            ],
            [
                'heading' => 'Your rights',
                'body' => 'You can ask us for a copy of what we hold about you, ask us to correct it, or ask us to delete it. Email :email and we will respond within thirty days.',
            ],
            [
                'heading' => 'Changes',
                'body' => 'If this policy changes materially we will update the date at the top of this page.',
            ],
        ],
    ],

    'terms' => [
        'title' => 'Terms of use',
        'intro' => 'These terms cover your use of this website. They do not cover work we carry out for clients, which is governed by a separate signed agreement.',

        'sections' => [
            [
                'heading' => 'Using this site',
                'body' => 'You may browse and share this site freely. You may not attempt to gain unauthorised access to it, interfere with its operation, or submit automated or misleading enquiries through its forms.',
            ],
            [
                'heading' => 'Our work and our clients',
                'body' => 'Case studies describe work we delivered. Client names appear only where we have permission to name them; where we do not, the client is described without being identified. Product names, logos and trademarks belong to their respective owners.',
            ],
            [
                'heading' => 'Our content',
                'body' => 'The text, design and code of this site belong to :company unless stated otherwise. You may quote from it with attribution; you may not republish it wholesale.',
            ],
            [
                'heading' => 'Accuracy',
                'body' => 'We keep this site current, but timelines, capabilities and descriptions are indicative rather than contractual. Nothing here forms an offer or a quotation — a proposal is always made in writing.',
            ],
            [
                'heading' => 'External links',
                'body' => 'Links to app stores and client websites are provided for reference. We are not responsible for their content.',
            ],
            [
                'heading' => 'Governing law',
                'body' => 'These terms are governed by the laws of TODO-GOVERNING-LAW.',
            ],
            [
                'heading' => 'Contact',
                'body' => 'Questions about these terms can be sent to :email.',
            ],
        ],
    ],
];
