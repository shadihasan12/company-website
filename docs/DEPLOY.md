# Deployment runbook

Written for whoever puts Clean Cody live and keeps it running.

Nothing here is hosting-specific guesswork — the choices that depend on the
actual server are marked **decide**.

---

## 1. Requirements

- PHP **8.3+** with `mbstring`, `pdo`, `intl`, `gd`, `zip`
- Composer 2
- Node 20+ (build only — the server does not need Node at runtime)
- A database: **decide** MySQL 8 or PostgreSQL 15. SQLite is the local
  default and is not suitable for production here.
- HTTPS. `launch:check` blocks on a non-HTTPS `APP_URL`.

## 2. First deploy

```sh
git clone <repo> && cd company-website
cp .env.example .env
php artisan key:generate
```

Fill in `.env` — at minimum:

| Key | Why |
|---|---|
| `APP_ENV=production`, `APP_DEBUG=false` | `APP_DEBUG=true` in production leaks stack traces and environment variables |
| `APP_URL=https://cleancody.com` | Canonical URLs, sitemap and OG tags are built from it |
| `DB_*` | Production database |
| `MAIL_*` | Without it leads are still saved, but nobody is emailed |
| `LEADS_NOTIFY_EMAIL` | Where enquiries go |
| `TURNSTILE_SITE_KEY` / `TURNSTILE_SECRET` | Optional; the honeypot and rate limiting work without it |
| `GA4_MEASUREMENT_ID` or `PLAUSIBLE_DOMAIN` | Optional; no analytics script loads until one is set |

Then:

```sh
composer deploy          # install, migrate, build, cache, and run launch:check
php artisan db:seed      # first deploy only — services, industries, technologies, the seven case studies
php artisan make:admin you@cleancody.com
```

`composer deploy` finishes by running `launch:check`. **Do not go live until it
reports no blocking issues.**

## 3. Every deploy after that

```sh
php artisan down --render="errors::503"
git pull
composer deploy
php artisan up
```

`composer deploy` runs `php artisan optimize`, which caches config, routes,
events and views. That means **`.env` changes do not take effect until the
next deploy** — or until `php artisan optimize:clear` is run.

## 4. Web server

Document root is `public/`. Everything else must be unreachable.

- `storage/` and `bootstrap/cache/` must be writable by the web user.
- `php artisan storage:link` must have run, or uploaded images 404.
- Do **not** place a static `robots.txt` in `public/` — the application
  serves it dynamically and a file would shadow the route.

## 5. Queues and scheduling

Lead notifications are sent **synchronously**, on purpose: with no worker
running, a queued notification would silently never arrive, and a lost
enquiry costs more than a second of response time.

To move them to a queue, set `QUEUE_CONNECTION=database`, add
`implements ShouldQueue` to `App\Notifications\LeadReceived`, and run a
worker — **and only then**, because the two changes are not safe apart.

Nothing is scheduled yet. If a scheduler is added later:

```
* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
```

## 6. Backups — **decide**

Not implemented, because a backup that is never restored is not a backup and
the right target depends on the host (S3, Spaces, the provider's snapshots).

Whatever is chosen must cover **both**:

- the database, and
- `storage/app/public` — every client logo, screenshot and post image lives
  there, and none of it is in git.

Test a restore before relying on it.

## 7. After launch

- Submit `https://cleancody.com/sitemap.xml` in Google Search Console.
- Set `GOOGLE_SITE_VERIFICATION` to the meta-tag value, then redeploy.
- Run Lighthouse against production. Targets: **LCP < 2.5s, INP < 200ms,
  CLS < 0.1**.
- Delete the SAMPLE demo content if it was ever seeded (`launch:check`
  blocks on this).

## 8. Useful commands

| Command | What it does |
|---|---|
| `php artisan launch:check` | Configuration and readiness, blocking vs warning |
| `php artisan content:audit` | Editorial gaps: screenshots, results, unproven services |
| `php artisan make:admin <email>` | Create or promote an admin |
| `php artisan optimize:clear` | Drop all caches after an `.env` change |
| `php artisan db:seed --class=DemoContentSeeder` | SAMPLE content for layout work — never in production |
