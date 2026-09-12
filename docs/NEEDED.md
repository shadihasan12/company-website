# Outstanding from the client

Live status of content gaps: `php artisan content:audit`.
Nothing here blocks building — every section ships with a placeholder or hides itself.
These determine whether the site is real or a mockup.

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

## Services (Epic 2)

Per service — mobile apps, web, dashboards, custom systems, ERP, AI, backend & cloud:

- [x] ~~What's included~~ — drafted, 5–7 bullets each. **Review and correct.**
- [x] ~~Typical timeline~~ — drafted. **Review and correct.**
- [ ] **Starting price or range.** Every page currently reads "Priced per project". Showing a figure
      filters out time-wasters and raises qualified enquiries.
- [ ] **Decision needed:** ERP and Custom Systems have zero case studies behind them.
      Send covering work, drop the service, or present it as a capability-only page.

## Portfolio (Epic 3) — the largest quality gap

- [ ] **Screenshots — 3–8 per project.** Currently zero. Single biggest upgrade available.
- [ ] Store / live URLs for the six projects missing them (`wakilcard.com` is on file)
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

## Launch (Epics 7 & 9)

- [ ] Domain DNS access
- [ ] Hosting: VPS, Forge, Cloudways?
- [ ] Production database: MySQL or PostgreSQL? (currently SQLite)
- [ ] Analytics: GA4 or Plausible, plus Search Console access
- [ ] Social profile URLs and any Clutch / GoodFirms / G2 profiles for review badges
- [ ] Certifications and partnerships (AWS, Google Cloud, Microsoft, Odoo, Apple)
