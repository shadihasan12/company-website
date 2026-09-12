# Software Company Website — Research, Requirements & Backlog

Status: planning. Stack in repo: Laravel 13 (PHP 8.5), Tailwind 4, Vite 8, Laravel Boost installed.

---

## 1. What the research says

Sources are listed at the bottom. Grounded on teardowns of Netguru and Simform (two of the highest-converting
software-agency sites) plus B2B CRO and trust-signal research.

### 1.1 The numbers that matter

| Metric | Benchmark |
|---|---|
| Average B2B website conversion rate | ~2.2% |
| Top-performing B2B sites | up to ~11.7% |
| Lift from social proof (logos, case studies, media) | up to +42% conversions |
| Core Web Vitals | LCP, CLS, INP are confirmed ranking factors (INP replaced FID) |

Translation: the difference between a pretty site and a site that brings customers is **proof and speed**,
not animation.

### 1.2 Homepage section order used by top agencies

Netguru and Simform converge on nearly the same order. This is the template to follow:

1. **Hero** — one sharp positioning line + subhead + primary CTA. Netguru: "AI-native commerce."
   Not "We are a leading software company." Say what you *do* and *for whom*.
2. **Client logo strip** — immediately under the hero, above the fold if possible.
3. **Services / capability cards** — 4–6 cards, each linking to a real service page.
4. **Featured case studies** — 3–6, with client name and a number.
5. **Stats / credibility band** — Netguru shows: NPS 73 · 2500+ projects · 400+ people · 17+ years.
6. **Testimonials** — real names, real titles, real companies, photo if possible.
7. **Differentiator section** — why us, in a way a competitor can't copy-paste.
8. **Industries served** — lets a prospect self-identify.
9. **Insights / blog** — 3 latest posts.
10. **Closing CTA** — often with a named human + photo (Simform names an actual executive).
11. **Footer** — links, socials, review badges (Clutch / G2 / Google).

### 1.3 Navigation structure used by top agencies

- Services (mega-menu, grouped by capability)
- Industries
- Work / Case studies / Clients
- Resources (blog, case studies, whitepapers, newsletter)
- Why us / About
- Contact (as a button, visually distinct)

### 1.4 Trust signals — ranked by impact

1. **Case studies with measurable outcomes.** Structure: which company → which problem *in the client's own
   words* → what specifically we did → what changed → the numbers. Group/filter by industry so a prospect
   sees what applies to them.
2. **Client logos** (with written permission).
3. **Named testimonials** — name, title, company, photo.
4. **Third-party review badges** — Clutch, G2, GoodFirms, Google.
5. **Real photography.** One authentic photo of the real team/office outperforms ten polished stock images.
6. **Certifications & partnerships** — AWS/GCP/Azure partner, Odoo, Meta, Apple.
7. **Company milestones**, team bios, leadership expertise.
8. **SSL** — presence correlates with +29% conversion.

### 1.5 What actually generates leads

- Multiple conversion paths, not one: contact form, book-a-call, WhatsApp, download, newsletter.
- **Soft asks and hard asks on every page.** Hard = "Book a discovery call." Soft = "Download the ERP
  migration checklist."
- **Interactive content converts**: project cost estimator, quiz/configurator, ROI calculator. This is a
  differentiator most agency sites skip — strongly recommended for us.
- Mobile-first. B2B buyers increasingly evaluate vendors on phones.
- Per-service and per-industry landing pages beat one generic services page for both SEO and conversion.

### 1.6 SEO in 2026 (changed — read this)

- **Structured data (JSON-LD) is now the primary language for both search engines AND LLMs.** ChatGPT,
  Perplexity, Gemini and AI Overviews are a real referral channel. Schema is how you tell them what the
  company does and why it's authoritative. Non-optional: `Organization`, `Service`, `Article`, `BreadcrumbList`,
  `FAQPage`, `Review`.
- Entity-rich content + intelligent internal linking > keyword stuffing.
- Comparison / "alternative to" / "X vs Y" page templates contribute the most pipeline per page in B2B.
- Core Web Vitals are a competitive differentiator, not just a checkbox.

### 1.7 Design direction for 2026

Clean, minimalist, ultra-modern. Glassmorphism (liquid-glass panes), subtle gradients, smooth curves.
Animation via GSAP / Framer Motion / Three.js. Dark-mode-first is common in this sector.
**Caution:** heavy animation is the #1 cause of bad LCP/INP. Budget it deliberately (see T7.2).

---

## 1.5 Decisions made (locked 2026-09-12)

| Decision | Choice |
|---|---|
| Frontend | **Blade + Tailwind 4 + Alpine.js + GSAP**, server-rendered. Best Core Web Vitals, best SEO, simplest hosting. |
| Content management | **Filament v4 admin panel** at `/admin`. You manage clients, case studies, testimonials, posts and leads yourselves. |
| Languages | **Arabic + English, full RTL.** Built bilingual from day one — not bolted on later. |
| Visual direction | **Bold & animated.** Dark-mode-first, gradient accents, glassmorphism, scroll-triggered GSAP — under a strict performance budget. |

**Consequences of the bilingual decision** — Epic 8 is no longer a separate phase, it is folded into every
task. All of this is already built and stays in force while Arabic is switched off:

- Every string goes through `__()` / `lang/{en,ar}` from the first line of code. No hardcoded copy.
- All models with public copy get translatable fields (title, excerpt, body) in both locales.
- Tailwind uses **logical properties only** (`ms-`/`me-`/`ps-`/`pe-`/`start-`/`end-`), never `ml-`/`mr-`/`left-`/`right-`.
- GSAP animations read direction from `dir` and mirror their x-axis in RTL.
- Two font stacks: a Latin display/body pair and an Arabic pair (e.g. IBM Plex Sans Arabic / Rubik / Cairo).
- URLs are locale-prefixed: `/en/...` and `/ar/...`, with `hreflang` tags and per-locale sitemaps.

**Round 3 decisions (2026-09-12)**

| Decision | Choice |
|---|---|
| Company | **Clean Cody** — cleancody.com |
| Logo | Supplied as raster; SVG to follow. A drawn approximation of the C-ring-and-orbit mark stands in — drop `public/images/logo-mark.svg` and it is picked up automatically. |
| Palette | Locked from the supplied swatches. The wordmark navy `#2a2560` is exactly `ink-700`. |
| Arabic | **Deferred.** `ar` is commented out in `config/site.php` and `vite.config.js`; `lang/ar/*` and every logical-property/RTL decision stay in the codebase. Re-enabling is two uncomments plus a content pass. |

URLs keep the `/en` prefix even with one language live, so nothing breaks when Arabic arrives.

**Round 2 decisions (2026-09-12)**

| Decision | Choice |
|---|---|
| Logo | Postponed. A gradient monogram placeholder stands in; swap `components/ui/logo.blade.php`. |
| Colours | Approximated from the supplied palette image. Exact hexes to follow — see `resources/css/app.css`. |
| Portfolio content | Extracted from the supplied CV into [CONTENT.md](CONTENT.md); seeded in T0.2. |
| Client names | Placeholder names, editable from the admin panel once T0.3 ships. |
| Screenshots | **Still required** — see the note in CONTENT.md. Placeholder gradients until then. |
| Team | Not referenced anywhere on the site. No team page, no team size, no headcount stat. |

**Consequences of the bold-animation decision** — hard performance budget enforced in T7.3:

- LCP < 2.5s, INP < 200ms, CLS < 0.1 on a mid-range Android over 4G.
- Total JS ≤ 150KB gzipped. GSAP core + ScrollTrigger only, no full plugin bundle.
- Every animation respects `prefers-reduced-motion`.
- Hero animation must never be the LCP element.

---

## 2. What I need from you

Nothing here blocks me from starting — I can build with realistic placeholder content and we swap it in.
But the more of this you give me, the sooner the site is genuinely usable.

### 2.1 Brand & identity — *needed for Task 0.1*

- [ ] Company legal name + brand name + tagline
- [ ] Logo files (SVG strongly preferred; light + dark variants)
- [ ] Brand colors (hex) — or tell me to propose a palette
- [ ] Preferred fonts — or tell me to propose
- [ ] Year founded, office location(s), full address (for the map + schema)
- [ ] Do you want dark mode, light mode, or both? *(default: dark-first with a light toggle)*
- [ ] Arabic brand name + Arabic tagline (needed for the `ar` locale)

### 2.2 Contact & social

- [ ] Public email, phone, WhatsApp number
- [ ] Booking link (Calendly / Cal.com) if you have one
- [ ] LinkedIn, GitHub, X, Instagram, Facebook, YouTube, Behance/Dribbble
- [ ] Clutch / GoodFirms / G2 profile URLs (for review badges)
- [ ] Certifications & partnerships (AWS, Google Cloud, Microsoft, Odoo, Apple Developer, Meta…)

### 2.3 Clients & portfolio — **the single highest-impact item**

This is what turns visitors into customers. For **each** project, send me:

| Field | Notes |
|---|---|
| Client name | + written permission to name them publicly. If no permission → "a leading logistics company in the Gulf" |
| Client logo | SVG or high-res PNG |
| Project name | |
| Category | mobile app / website / dashboard / ERP / AI / backend & infra / custom system |
| Industry | fintech, healthcare, retail, logistics, education, government… |
| **Live links** | website URL, App Store link, Google Play link — these are the proof |
| The problem | 2–3 sentences, in the client's language |
| What we built | 3–5 bullets |
| Tech stack | Flutter, Laravel, Node, Postgres, AWS… |
| **Results with numbers** | users, downloads, app rating, % faster, hours saved, revenue, uptime |
| Screenshots | app screens, dashboard shots, device mockups — 3–8 per project |
| Duration | "4 months" — team size is deliberately not shown |
| Testimonial | quote + person's name + title + photo (optional but powerful) |

**Minimum viable:** 6 projects. **Good:** 10–12. **Start with your 3 strongest** — I'll build the case-study
template around those.

I noticed some Flutter/Node projects in your other working directories (a mobile app with a face-recognition
backend, and a parishes API integration). **Are those yours to show?** If yes they're strong portfolio pieces —
tell me the client name and permission status.

### 2.4 Services

You listed: mobile apps, websites, dashboards, systems, ERP systems, AI solutions, backend & server services.
For **each**, I need:

- [ ] One-sentence pitch
- [ ] 4–6 bullets of what's included
- [ ] Tech stack you use for it
- [ ] Typical timeline ("6–12 weeks")
- [ ] Pricing: starting price, a range, or "request a quote"? (Showing a starting price filters out
      time-wasters and increases qualified leads — recommended)
- [ ] 2–3 related projects from §2.3

### 2.5 Content decisions

- [ ] Blog / insights section — yes or no? Who writes it?
- [ ] Careers page — are you hiring?
- [ ] Your delivery process (discovery → design → build → QA → launch → support) — describe it in your words
- [ ] Industries you want to target

### 2.6 Technical

- [ ] Domain name
- [ ] Hosting: VPS? Laravel Forge / Cloud? Cloudways? Shared?
- [ ] Production database: MySQL or PostgreSQL? (repo currently defaults to SQLite)
- [ ] Transactional email: SMTP credentials, or Resend / Mailgun / Postmark account
- [ ] Analytics: GA4 or Plausible? Google Search Console access?
- [ ] Spam protection: Cloudflare Turnstile (free, recommended) or reCAPTCHA — I'll need site keys
- [ ] CRM to push leads into (HubSpot, Pipedrive) — or just database + email notification?

---

## 3. Backlog

Epics are ordered by dependency. Ask for tasks one at a time — e.g. "do T0.1".

### Epic 0 — Foundation (do first)

- **T0.1 — Design system & shell.** ✅ **Done.** Tailwind 4 theme tokens, Latin + Arabic font stacks,
  dark/light themes, bilingual locale routing with full RTL, base layout, header with services mega-menu,
  mobile nav, footer, and the `ui.*` component library. Styleguide at `/{locale}/styleguide`.
- **T0.2 — Data model.** ✅ **Done.** 10 tables, 8 models, factories and seeders for `services`,
  `projects`, `clients`, `testimonials`, `technologies`, `industries`, `posts`, `leads` and the two
  pivots. Every public-facing text column is per-locale JSON, so Arabic needs a content pass rather
  than a migration. Seeded with the seven real projects from the CV.
  Also ships `php artisan content:audit`, which lists every gap blocking launch.
- **T0.3 — Admin panel.** ✅ **Done.** Filament v5 at `/admin`, branded to the Clean Cody palette.
  Resources for case studies, services, clients, testimonials, posts, industries, technologies and
  leads. Translatable fields render one input per active locale and become tabs automatically when
  Arabic is switched on. Dashboard widget mirrors `content:audit`.
  Panel access is gated on `users.is_admin` — authentication alone is not enough.
  Create an admin with `php artisan make:admin <email>`.

### Epic 1 — Homepage ✅ **Complete**

- **T1.1** ✅ **Done.** Hero: eyebrow, headline with gradient accent, subhead, dual CTA, live shipped
  count, scroll cue. Animated orbit rings echoing the logo mark plus drifting brand glows — transform
  only, so they stay on the compositor. The h1 is never faded in, because an element at opacity 0
  does not count as painted and a fade would delay LCP by the full animation duration.
- **T1.2** ✅ **Done.** Client logo strip: seamless CSS marquee, paused on hover/focus, wrapping
  instead of scrolling under reduced motion. Shows only clients we are permitted to name, and falls
  back to a text wordmark until a logo file is uploaded.
- **T1.3** ✅ **Done.** Services grid: all seven services from the database with icon, tagline,
  excerpt and hover state, plus an "All services" CTA.
- **T1.4** ✅ **Done.** Featured case studies: industry badge, live-link count, client, summary,
  metrics and stack per card. Projects without a screenshot render a branded orbit panel rather than
  an empty box, so the layout reads as deliberate while artwork is outstanding.
- **T1.5** ✅ **Done.** Credibility band with count-up animation. Projects, years and industries are
  computed from real records; clients, rating, uptime and NPS come from `config('site.stats')` and are
  omitted entirely while unset. The band hides below two figures — one lonely statistic reads worse
  than none.
- **T1.6** ✅ **Done.** Six-step process timeline with a gradient rail, driven by
  `config('site.process')` — reorder or trim the array to change it. Copy is a first draft in
  `lang/en/home.php` under `process.steps`; **rewrite it in your own words.**
- **T1.7** ✅ **Done.** Stack section: technologies from the database grouped into Languages,
  Frameworks, Services and Tooling. Empty categories are skipped.
- **T1.8** ✅ **Built, and currently hidden.** Testimonial cards with rating, quote, photo or
  initials, name, title and client. Renders nothing until real quotes exist — never seeded, because
  fabricated social proof on a live site is a liability. Only clients we may name are credited.
- **T1.9** ✅ **Done.** Industries grid with icons and a live case-study count. Only sectors with
  published work appear — the section asserts experience, so it must be backed by a case study.
- **T1.10** ✅ **Done.** Closing CTA band with gradient ring, orbit glows, primary CTA and WhatsApp.
  *Named contact person dropped — the site references no individuals.*

### Epic 2 — Services ✅ **Complete**

- **T2.1** ✅ Services index at `/{locale}/services`, driven by the database.
- **T2.2** ✅ Detail template: breadcrumb, hero, engagement facts panel (timeline + pricing), overview,
  what's included, process, stack, related work, FAQ accordion with `FAQPage` JSON-LD, other services, CTA.
- **T2.3** ✅ Full draft copy for all seven services — body, timeline, 5–7 inclusions and 2–4 FAQs each —
  in `lang/en/services.php`, seeded to the database and editable in the admin panel.

Still open: **starting prices** (every service currently reads "Priced per project") and the
**ERP / Custom Systems decision** — both are advertised with no case study, so their Related work
section is simply absent.

### Epic 3 — Portfolio / case studies ✅ **Complete**

- **T3.1** ✅ Index at `/{locale}/work` with service, industry and technology filters. Filtering is
  server-side on query parameters, so a filtered view is a real shareable, indexable URL. Only facets
  that would return results are offered; impossible combinations get an empty state with a reset.
- **T3.2** ✅ Detail template: breadcrumb, hero, facts panel, results band with count-ups, problem →
  solution → outcome, gallery, testimonial, clickable stack, next case study (wraps at the end), CTA.
- **T3.3** ✅ Gallery with keyboard-driven lightbox (arrows, Escape, body scroll lock).
  *Device mockup frames deferred — they need real screenshots to tune the aspect ratios.*
- **T3.4** ✅ Website / App Store / Google Play buttons, each hidden when its URL is absent.

Every case study currently hides its gallery, results and narrative sections, because that content
does not exist yet. See [NEEDED.md](NEEDED.md).

### Epic 4 — Company ✅ **Complete**

- **T4.1** ✅ About page: story, values, stats, process, client strip and CTA.
  The **milestones timeline is hidden** — no real company milestones were supplied, and inventing a
  founding story would be fabrication. Add entries to `config('site.about.milestones')` and it appears.
  *Real photos still outstanding.*
- ~~**T4.2** Team page~~ — *deferred at the client's request; the site does not
  reference the team or team size anywhere.*
- **T4.3** ✅ Careers page and open application form, **switched off by default**. The client has not
  said whether they are hiring, and a careers page with no roles behind it is worse than none. Set
  `CAREERS_ENABLED=true` to publish it; the route is not even registered otherwise, so it cannot
  appear in navigation or a sitemap. Applications capture into `leads` with source `career`.
  *Asks for a portfolio link rather than a CV upload — accepting arbitrary documents from the public
  internet is a security surface this site does not need yet.*

### Epic 5 — Lead capture ✅ **Complete**

- **T5.1** ✅ Contact page with full validation, three-layer spam defence, rate limiting, database
  capture, email notification and a thank-you page. Arriving from a service page pre-selects it.
- **T5.2** ✅ WhatsApp float button (appears past the hero) plus click-to-call and mailto in the aside.
- **T5.3** ✅ Booking link surfaces automatically once `SITE_BOOKING_URL` is set; hidden until then.
- **T5.4** ✅ Newsletter capture in the footer, stored as its own lead source.
- **T5.5** ✅ **Scoping wizard** at `/start-a-project` — six steps capturing service, platforms,
  features, stage, timeline and budget into the lead payload. All primary CTAs point here.
  *It captures scope, not a price: publishing an automatic estimate would mean inventing figures.
  Once real pricing exists the same answers can drive one.*
- **T5.6** ✅ Lead pipeline in the admin with status filters, a new-lead badge and streaming CSV export
  of the current filter.

**Spam defence, in layers:** honeypot field, encrypted minimum-fill-time token, per-IP rate limiting,
and Cloudflare Turnstile which activates automatically once keys are configured.

### Epic 6 — Insights / blog ✅ **Complete** (T6.3 deferred)

- **T6.1** ✅ Index at `/{locale}/insights` with category filtering and pagination. Only categories
  with published posts are offered. Drafts and scheduled posts are hidden from the listing **and**
  unreachable by direct URL.
- **T6.2** ✅ Post detail: anchored headings, table of contents (shown from three headings up),
  reading time, author, date, LinkedIn/X/copy-link sharing, related posts topped up to three, and
  `Article` JSON-LD. Body HTML is styled by element in `.prose-content` using the site's own tokens
  rather than pulling in the typography plugin.
- **T6.3** ⏸ **Deferred: gated download.** There is no lead magnet to gate — building the gate,
  file storage and delivery before the asset exists would be scaffolding with nothing behind it.
  The newsletter capture from Epic 5 already covers email collection. Revisit when a resource exists.

The **Insights link only appears in the navigation once a post is published**, so it never leads to
an empty page. Run `php artisan db:seed --class=DemoContentSeeder` to see the design with sample posts.

### Epic 7 — SEO, performance & AI visibility ✅ **Complete** (one item deferred)

- **T7.1** ✅ Title, description, canonical (query string stripped, so filtered and paginated views
  do not compete with the clean listing), Open Graph, Twitter cards, hreflang (emitted only once a
  second locale is live), dynamic `sitemap.xml` and `robots.txt`.
  **Indexing defaults to the production environment**, so staging cannot be indexed by accident and
  launch does not depend on anyone flipping a switch. `SITE_INDEXABLE` overrides either way.
  ⏸ *Dynamic OG image generation deferred:* it needs a licensed TTF to render text into a PNG, which
  is a brand asset rather than code. Drop a 1200×630 `public/images/og-default.png` and it is used
  automatically; per-record images (case study hero, post cover) already take priority.
- **T7.2** ✅ Organization (every page, with `@id` so other nodes reference it), Service,
  CreativeWork, Article, BreadcrumbList, FAQPage. **AggregateRating is emitted only when genuinely
  rated testimonials exist** — fabricated review data is a manual-action risk, not a placeholder.
- **T7.3** ✅ `fetchpriority="high"` on LCP images, lazy loading and `decoding="async"` elsewhere,
  aspect-ratio containers so images cannot shift layout, three font preloads, module-deferred JS.
  Budget holding at **64.7KB JS / 10.7KB CSS gzipped** against 150KB.
  *A real Lighthouse run against production hardware is still outstanding.*
- **T7.4** ✅ Branded 403/404/419/429/500/503 pages, always `noindex`. Security headers on every
  response: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`,
  `X-Permitted-Cross-Domain-Policies`, and HSTS over HTTPS only.
  *No strict CSP:* Alpine evaluates expressions with `new Function`, so any policy tight enough to
  matter needs `unsafe-eval`; one carrying both `unsafe-eval` and `unsafe-inline` implies protection
  it does not provide. Adding one means moving to Alpine's CSP build first.

### Epic 8 — Internationalization *(folded into every epic — see §1.5)*

- **T8.1** Localization foundation: locale-prefixed routes, middleware, language switcher, `lang/en` + `lang/ar`,
  Arabic font stack, RTL logical-property audit. **Do this inside T0.1 — not later.**
- **T8.2** Translatable model fields + bilingual inputs in the Filament admin. **Do this inside T0.2/T0.3.**
- **T8.3** `hreflang` tags, per-locale sitemaps, Arabic meta/OG copy. *(part of T7.1)*
- **T8.4** RTL QA pass across every page, RTL-mirrored GSAP animations

### Epic 9 — Launch ✅ **Complete** (backups left as a hosting decision)

- **T9.1** ✅ GA4 and Plausible, both config-gated — no third-party script reaches a visitor until a
  provider is chosen, and **analytics never loads outside production**, so local and staging traffic
  cannot pollute the client's reports. Every lead-capture path flashes a `lead_submitted` conversion
  carrying source, service and budget; it is read once from the server flash, so refreshing the
  thank-you page cannot inflate it. Search Console verification tag included.
- **T9.2** ✅ `composer deploy`, a GitHub Actions CI workflow (Pint + tests + build), and
  [DEPLOY.md](DEPLOY.md) covering requirements, first deploy, routine deploys, web server, queues
  and post-launch steps.
  *Queues:* lead notifications are intentionally synchronous. With no worker running, a queued
  notification would silently never arrive, and a lost enquiry costs more than a second of response
  time. The runbook documents the two changes that must be made together to move them.
  *Backups:* deliberately not implemented — the right target depends on the host, and a backup
  nobody has restored is not a backup. The runbook states what must be covered, including
  `storage/app/public`, which holds every uploaded image and is not in git.
- **T9.3** ✅ `php artisan launch:check` — separates **blocking** issues (debug on, localhost URL,
  log mail driver, no admin, SAMPLE content still present) from **warnings** (SQLite, no Turnstile,
  no analytics, missing logo or OG image, no client logos). `composer deploy` runs it last.
  *A real Lighthouse run against production hardware is still outstanding.*

---

## Status

All nine epics complete, plus a full audit pass. 243 tests passing. JS 64.7KB / CSS 10.7KB gzipped, against a 150KB budget.

Privacy and terms pages are live (required — the site collects personal data) and
`launch:check` blocks until their legal review markers are removed.

What remains is content and credentials, not code — see [NEEDED.md](NEEDED.md), and run
`php artisan launch:check` and `php artisan content:audit` for the live picture.

## Sources

- [Netguru homepage](https://www.netguru.com/) · [Simform homepage](https://www.simform.com/)
- [12 Best Software and App Development Agencies to Hire in 2026 — DesignRush](https://news.designrush.com/top-software-app-dev-agencies-hire-2026)
- [Top 31 Software Development Websites — Web Design Awards](https://www.webdesignawards.io/categories/software-development)
- [B2B Website Conversion Rate Optimization Guide — O8](https://www.o8.agency/blog/cro/unlock-your-b2b-websites-potential-achieve-top-conversion-rates-today)
- [8 Trust Signals You Need to Have on Your Website — Webstacks](https://www.webstacks.com/blog/trust-signals)
- [Best B2B Website Trust Signals 2026](https://sgdigitalbusinessdevelopment.com/b2b-website-trust-signals-2026/)
- [Website Trust Signal Statistics 2026 — Scalify](https://www.scalify.ai/blog/website-trust-signal-statistics-what-makes-visitors-stay-2026)
- [Trust Signals Now Have to Be Huge — Everything.design](https://www.everything.design/blog/trust-signals-b2b-website)
- [Programmatic SEO 2026 Playbook — Nico Digital](https://www.nicodigital.com/digital-marketing/programmatic-seo-2026-playbook/)
- [Best Technical SEO Agencies 2026 — eSEOspace](https://eseospace.com/blog/best-technical-seo-agencies-2026/)
- [A Guide to Writing Case Studies for Agency Websites — New Media Campaigns](https://www.newmediacampaigns.com/blog/tips-for-writing-agency-website-case-studies)
- [Top Software Development Companies — Clutch](https://clutch.co/developers)
