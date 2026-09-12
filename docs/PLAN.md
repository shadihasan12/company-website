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
- **T0.3 — Admin panel.** Filament v4 so you can add clients/projects/posts without me. Auth, roles,
  media uploads, image conversions.

### Epic 1 — Homepage

- **T1.1** Hero: headline, subhead, dual CTA, background animation (performance-budgeted)
- **T1.2** Client logo strip / marquee
- **T1.3** Services grid (6 cards → service pages)
- **T1.4** Featured case studies
- **T1.5** Stats / credibility band with count-up animation
- **T1.6** "How we work" process timeline
- **T1.7** Tech stack strip
- **T1.8** Testimonials carousel
- **T1.9** Industries we serve
- **T1.10** Closing CTA band + named contact person

### Epic 2 — Services

- **T2.1** Services index page
- **T2.2** Service detail template (hero, what's included, process, stack, pricing, related work, FAQ + FAQ schema, CTA)
- **T2.3** Content pass for all 7 services

### Epic 3 — Portfolio / case studies

- **T3.1** Portfolio index with filtering by category + industry + tech
- **T3.2** Case study detail template (problem → solution → stack → results → gallery → testimonial → next project)
- **T3.3** Device mockup / screenshot gallery component with lightbox
- **T3.4** App Store / Google Play / live-site link buttons

### Epic 4 — Company

- **T4.1** About page (story, milestones timeline, values, real photos)
- ~~**T4.2** Team page~~ — *deferred at the client's request; the site does not
  reference the team or team size anywhere.*
- **T4.3** Careers page + application form *(optional)*

### Epic 5 — Lead capture — **the revenue path**

- **T5.1** Contact page: form (name, company, email, phone, service, budget, timeline, message), validation,
  honeypot + Turnstile, rate limiting, store in `leads`, email notification, thank-you page
- **T5.2** WhatsApp float button + click-to-call
- **T5.3** Booking-call embed
- **T5.4** Newsletter capture
- **T5.5** **Project cost estimator** (interactive multi-step quiz → estimate + captured lead). High-value
  differentiator — most competitors don't have this.
- **T5.6** Lead management in admin + CSV export

### Epic 6 — Insights / blog

- **T6.1** Blog index + categories + pagination
- **T6.2** Post detail (TOC, reading time, author, share, related posts, Article schema)
- **T6.3** Downloadable resource gated behind email *(optional)*

### Epic 7 — SEO, performance & AI visibility

- **T7.1** Meta/OG/Twitter tags, canonical URLs, dynamic OG image generation, `sitemap.xml`, `robots.txt`
- **T7.2** JSON-LD: Organization, Service, Article, BreadcrumbList, FAQPage, Review
- **T7.3** Performance: image optimization + WebP/AVIF, lazy loading, font preloading, deferred JS,
  caching, Core Web Vitals audit (target: LCP < 2.5s, INP < 200ms, CLS < 0.1)
- **T7.4** Accessibility pass (WCAG 2.2 AA), 404/500 pages, security headers

### Epic 8 — Internationalization *(folded into every epic — see §1.5)*

- **T8.1** Localization foundation: locale-prefixed routes, middleware, language switcher, `lang/en` + `lang/ar`,
  Arabic font stack, RTL logical-property audit. **Do this inside T0.1 — not later.**
- **T8.2** Translatable model fields + bilingual inputs in the Filament admin. **Do this inside T0.2/T0.3.**
- **T8.3** `hreflang` tags, per-locale sitemaps, Arabic meta/OG copy. *(part of T7.1)*
- **T8.4** RTL QA pass across every page, RTL-mirrored GSAP animations

### Epic 9 — Launch

- **T9.1** Analytics + Search Console + conversion event tracking
- **T9.2** Deployment: env config, production DB, queue worker, scheduler, backups, SSL, CI
- **T9.3** Pre-launch QA checklist (cross-browser, cross-device, forms, links, Lighthouse)

---

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
