# Outstanding from the client

Live status of content gaps: `php artisan content:audit`.
Nothing here blocks building — every section ships with a placeholder or hides itself.
These determine whether the site is real or a mockup.

## Legal — **required before launch**

`php artisan launch:check` blocks until these are done.

- [ ] **Have a lawyer review `lang/en/legal.php`.** The privacy policy accurately describes what the
      site collects, but it is not legal advice. Remove the `TODO-LEGAL-REVIEW` marker once reviewed.
- [ ] **Governing law** for the terms of use — replace `TODO-GOVERNING-LAW`.
- [ ] Company registration details, if your jurisdiction requires them on the site.

## Brand

- [ ] Logo SVG → drop at `public/images/logo-mark.svg`, picked up automatically
- [ ] Exact brand hexes → replace the four source values at the top of `resources/css/app.css`
- [ ] Confirm the public phone / WhatsApp number (currently `+961 76 928 097`, taken from the CV)

## Homepage (Epic 1)

- [ ] **T1.5** Real numbers: projects delivered, clients, years operating, app rating, uptime.
      *No headcount — the site references neither team nor team size.*
- [ ] **T1.6** Your delivery process in your own words. A six-step draft is live
      (Discovery → Design → Build → Quality → Release → Support) in `lang/en/home.php`
      under `process.steps` — read it and correct anything that is not how you actually work.
- [ ] **T1.8** 2–3 real testimonials: quote + name + title + company (+ photo if possible).
      Deliberately never invented — fabricated social proof on a live site is a liability.
- [ ] **T1.9** Which industries you want to target

## About page (Epic 4)

- [ ] **Your real story.** A three-paragraph draft is live in `lang/en/about.php` — rewrite it.
- [ ] **Values.** Four are drafted (`about.values`). Confirm or replace.
- [ ] **Milestones.** The timeline is hidden because none were supplied. Send year + one line each
      and add them to `config('site.about.milestones')`.
- [ ] **Real photos** of the office or the work. Research is clear that one authentic photo beats
      ten stock images.
- [ ] **Are you hiring?** Careers is built but switched off. `CAREERS_ENABLED=true` publishes it.

## Blog (Epic 6)

- [ ] **Who writes it, and does it happen?** The blog is built and hidden from navigation until a
      post is published. An abandoned blog is worse than none.
- [ ] First 2–3 posts. Categories available: Engineering, Mobile, AI, Product, Company.

## Services (Epic 2)

Per service — mobile apps, web, dashboards, custom systems, ERP, AI, backend & cloud:

- [x] ~~What's included~~ — drafted, 5–7 bullets each. **Review and correct.**
- [x] ~~Typical timeline~~ — drafted. **Review and correct.**
- [ ] **Starting price or range.** Every page currently reads "Priced per project". Showing a figure
      filters out time-wasters and raises qualified enquiries.
- [ ] **Decision needed:** ERP and Custom Systems have zero case studies behind them.
      Send covering work, drop the service, or present it as a capability-only page.

## Website case studies — **need your input**

Four are live with a captured screenshot each: Mirsam, Nova Dental Care,
Ethos Music Academy, Ethos Greek Bistro.

Their summaries describe only what each site verifiably **is**, taken from the site itself.
Nobody has told me what Clean Cody actually built, so these are all empty:

- [ ] What you built and how (`solution`) — the only field that makes it a case study
- [ ] Tech stack per site
- [ ] Real client names, and permission to name them (all four are the business name as a placeholder)
- [ ] Results with numbers
- [ ] **Honeystone is deliberately not added.** That link is another agency's own portfolio page,
      describing work *they* did with *their* client testimonial. If Clean Cody built it as a
      subcontractor, send the live URL (`mobilephonetradein.co.uk`) and say so.
- [ ] **Weather Live°** is published by Mosaic S.r.l. and is not in the CV — what was your role?

## Portfolio (Epic 3) — the largest quality gap

- [x] ~~Screenshots for the five store-listed apps~~ — imported from the App Store and Google Play
      with `php artisan portfolio:import-screenshots`, downscaled and re-encoded to WebP.
      **Still needed:** Captain Car and Reset have no store link, so no screenshots.
- [ ] **Permission to publish store screenshots.** They are the publisher's marketing assets, not
      automatically Clean Cody's — same question as naming the clients.
- [ ] Store / live URLs for Captain Car and Reset
- [ ] Results with numbers: downloads, rating, % faster, hours saved, revenue, uptime
- [ ] Real client names **and written permission to publish them** — all seven are
      placeholders currently set to publish
- [ ] Client logo files
- [ ] `problem` and `outcome` copy per case study

## Lead capture (Epic 5)

- [ ] **SMTP credentials, or Resend / Mailgun / Postmark.** Until these are set, leads are still
      saved to the database — only the notification email is missing.
- [ ] **Cloudflare Turnstile site key + secret** (`TURNSTILE_SITE_KEY`, `TURNSTILE_SECRET`).
      Optional: honeypot, fill-time check and rate limiting run without it.
- [x] ~~Budget ranges~~ — five bands drafted in `config/site.php` under `leads.budget_ranges`.
      **Confirm or change them.**
- [ ] Booking link (Calendly / Cal.com) — set `SITE_BOOKING_URL` and it appears on the contact page.
- [ ] `LEADS_NOTIFY_EMAIL` if new leads should go somewhere other than the public address.

## SEO (Epic 7)

- [ ] **A 1200×630 social sharing image** at `public/images/og-default.png`. Without it, pages with
      no image of their own share as a plain text card.
- [ ] Confirm nothing should stay out of the sitemap before launch.
- [ ] **Delete the SAMPLE demo content** before going live (`php artisan content:audit` will still
      show the real gaps).

## Launch (Epic 9)

Run `php artisan launch:check` for the live version of this list.

- [ ] Domain DNS access
- [ ] Hosting: VPS, Forge, Cloudways? See [DEPLOY.md](DEPLOY.md) for requirements.
- [ ] **Production database: MySQL or PostgreSQL?** SQLite is the local default and is not suitable.
- [ ] **Backup target** — must cover the database *and* `storage/app/public`, which holds every
      uploaded logo, screenshot and cover image and is not in git.
- [ ] Analytics: GA4 measurement id or Plausible domain, plus Search Console access
- [ ] Social profile URLs and any Clutch / GoodFirms / G2 profiles for review badges
- [ ] Certifications and partnerships (AWS, Google Cloud, Microsoft, Odoo, Apple)
