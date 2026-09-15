# Portfolio content — Clean Cody

Extracted from the supplied CV.

Ready to seed in **T0.2**. Client names are placeholders per your instruction and become
editable from the admin panel in T0.3. Nothing here references the team or team size.

> **Seeded as of T0.2.** All six projects are in the database with their real stacks, services and
> industries. Run `php artisan content:audit` at any time for a live list of what is still missing.
>
> **Deliberately not seeded:** metrics, testimonials and blog posts. No figures were supplied, and
> putting invented numbers or fabricated client quotes on a live site is a real liability, not a
> placeholder. They live in `DemoContentSeeder` and must be asked for explicitly:
> `php artisan db:seed --class=DemoContentSeeder` — every string there is prefixed `SAMPLE`.

> **Still needed from you — screenshots.** Yes, screenshots matter more than almost anything else
> on the page. A case study without them reads as a claim; with them it reads as evidence, and the
> research is unambiguous that visual proof is what converts. Per project I need **3–8 images**:
> app screens, dashboard views, or the live site. Until they arrive T0.2 seeds generated brand
> gradients so layouts are reviewable, and T0.3 gives you upload fields to replace them yourself.

---

## Projects

### 1. Hollo AI — The Twin Platform
- **Placeholder client:** Northwind AI
- **Category:** Mobile app · AI solutions
- **Industry:** Consumer AI
- **Stack:** Flutter, WebRTC, WebSockets, BLoC, GetIt, Clean Architecture
- **Summary:** AI twin platform with live voice and video, real-time communication,
  audio/video processing, payment gateways and subscription management.
- **Links:** App Store, Google Play *(URLs needed)*
- **Screenshots:** ❌ needed
- **Results:** ❌ needed — downloads, rating, concurrent sessions, latency

### 2. Wakil Topup
- **Placeholder client:** Topline Digital
- **Category:** Mobile app · Website · Dashboard
- **Industry:** Fintech / payments
- **Stack:** Flutter, Flutter for Web, SQLite, GetIt/Injectable, Freezed
- **Summary:** Digital-products marketplace for mobile top-ups and gift cards, with wallet,
  transaction history and an admin dashboard.
- **Links:** **https://wakilcard.com** ✅ · App Store, Google Play *(URLs needed)*
- **Screenshots:** ❌ needed
- **Results:** ❌ needed — transactions, GMV, users
- **Note:** the only project currently covering mobile + web + dashboard in one story. Good
  candidate for the featured slot on the homepage.

### 3. Al Bustan
- **Placeholder client:** Bustan Retail Group
- **Category:** Mobile app · Backend
- **Industry:** E-commerce / retail
- **Stack:** Flutter, Express.js, TypeScript
- **Summary:** E-commerce app with product catalog, shopping cart, secure checkout and
  payment integration.
- **Links:** Google Play *(URL needed)*
- **Screenshots:** ❌ needed

### 4. Captain Car
- **Placeholder client:** Metro Mobility
- **Category:** Mobile app
- **Industry:** Transport / mobility
- **Stack:** Flutter, Dart
- **Summary:** Taxi-booking app with on-demand and pre-scheduled rides, referral rewards
  and trip management.
- **Links:** Google Play *(URL needed)*
- **Screenshots:** ❌ needed

### 5. Reset — Lasting Weight Loss
- **Placeholder client:** Reset Health
- **Category:** Mobile app
- **Industry:** Health & fitness
- **Stack:** Flutter, Dart
- **Summary:** Health and fitness companion app supporting a structured weight-loss programme
  for members.
- **Links:** Google Play *(URL needed)*
- **Screenshots:** ❌ needed

### 6. Reserva
- **Placeholder client:** Reserva Hospitality
- **Category:** Mobile app
- **Industry:** Hospitality / restaurants
- **Stack:** Flutter, Firebase
- **Summary:** Restaurant reservation app with real-time booking, an interactive calendar,
  user profiles and notifications.
- **Links:** ❌ none on file
- **Screenshots:** ❌ needed

---

## Capability coverage

What the six projects prove, and where the portfolio is thin:

| Service | Covered by | Strength |
|---|---|---|
| Mobile apps | All six | Very strong |
| AI solutions | Hollo AI | **Thin — one example** |
| Backend & cloud | Al Bustan, Hollo AI | Good |
| Websites | Wakil Topup (wakilcard.com) | **Thin — one example** |
| Dashboards | Wakil Topup admin | **Thin — one example** |
| Custom systems | — | **Not covered** |
| ERP systems | — | **Not covered** |

Two services are advertised with no proof behind them. Options, in order of preference:

1. Send more projects that cover websites, dashboards, custom systems and ERP.
2. Narrow the advertised services to what the portfolio can back.
3. Keep all seven services but present the uncovered ones as capability pages rather than
   as case-study-backed work.

Worth deciding before Epic 2, since it changes what the services pages claim.

---

## Technology tags for filtering

Flutter · Dart · Kotlin · TypeScript · JavaScript · Python · BLoC · Clean Architecture ·
GetIt/Injectable · Freezed · SQLite · Firebase (FCM, Analytics, Crashlytics) · Sentry ·
WebRTC · WebSockets · REST APIs · AWS Rekognition · Google Play Billing · Apple Pay ·
NestJS · Express.js · CI/CD
