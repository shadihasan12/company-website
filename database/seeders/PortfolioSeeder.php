<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Industry;
use App\Models\Project;
use App\Models\Service;
use App\Models\Technology;
use Illuminate\Database\Seeder;

/**
 * The seven shipped projects, taken from the supplied CV.
 *
 * The engineering facts here are real. Deliberately absent:
 *
 *   - metrics    — no download counts, ratings or revenue figures were
 *                  supplied, and inventing them would put fabricated
 *                  numbers on a live page.
 *   - gallery    — screenshots are still outstanding.
 *   - store URLs — only wakilcard.com was supplied.
 *
 * `php artisan content:audit` lists exactly what is still missing.
 *
 * Client names are placeholders pending the real names and their written
 * permission to be published. Edit them from the admin panel (T0.3).
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->projects() as $index => $data) {
            $client = Client::updateOrCreate(
                ['slug' => $data['client_slug']],
                [
                    'name' => $data['client_name'],
                    // Placeholder names are shown as-is so the site is
                    // reviewable; swap in the real name and confirm
                    // permission before launch.
                    'is_named' => true,
                    'is_featured' => $data['featured'],
                    'sort_order' => $index,
                ],
            );

            $project = Project::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'client_id' => $client->id,
                    'industry_id' => Industry::where('slug', $data['industry'])->value('id'),
                    'summary' => ['en' => $data['summary'], 'ar' => $data['summary_ar']],
                    'problem' => null,
                    'solution' => ['en' => $data['solution'], 'ar' => $data['solution_ar']],
                    'outcome' => null,
                    'metrics' => [],
                    'gallery' => [],
                    'website_url' => $data['website_url'] ?? null,
                    'app_store_url' => $data['app_store_url'] ?? null,
                    'google_play_url' => $data['google_play_url'] ?? null,
                    'duration' => null,
                    'is_featured' => $data['featured'],
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );

            $project->services()->sync(
                Service::whereIn('key', $data['services'])->pluck('id'),
            );

            $project->technologies()->sync(
                Technology::whereIn('slug', $data['technologies'])->pluck('id'),
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function projects(): array
    {
        return [
            [
                'slug' => 'hollo-ai',
                'name' => 'Hollo AI — The Twin Platform',
                'client_slug' => 'northwind-ai',
                'client_name' => 'Northwind AI',
                'industry' => 'consumer-ai',
                'featured' => true,
                'summary' => 'An AI twin platform with live voice and video, real-time communication, audio and video processing, payment gateways and subscription management.',
                'summary_ar' => 'منصة توأم رقمي بالذكاء الاصطناعي مع صوت وفيديو مباشرين، وتواصل فوري، ومعالجة للصوت والصورة، وبوابات دفع وإدارة اشتراكات.',
                'solution' => 'Built on clean architecture with BLoC state management and GetIt dependency injection. WebRTC handles in-app calling, WebSockets carry real-time messaging, and the billing layer covers both Google Play Billing and Apple Pay with full subscription lifecycle handling.',
                'solution_ar' => 'بُنيت على بنية نظيفة مع إدارة حالة بـBLoC وحقن تبعيات عبر GetIt. يتولى WebRTC المكالمات داخل التطبيق، وتنقل WebSockets الرسائل الفورية، وتغطي طبقة الفوترة كلًّا من Google Play Billing وApple Pay مع إدارة كاملة لدورة حياة الاشتراك.',
                'services' => ['mobile-apps', 'ai-solutions'],
                'technologies' => ['flutter', 'dart', 'webrtc', 'websockets', 'bloc', 'getit', 'clean-architecture', 'apple-pay', 'google-play-billing'],
            ],
            [
                'slug' => 'nam-community',
                'name' => 'NAM — Your Maronite Community',
                'client_slug' => 'cedar-community-foundation',
                'client_name' => 'Cedar Community Foundation',
                'industry' => 'community',
                'featured' => true,
                'summary' => 'A community platform with real-time chat, events with face-search photo matching, an AI assistant and push notifications.',
                'summary_ar' => 'منصة مجتمعية بمحادثة فورية، وفعاليات مع مطابقة الصور بالبحث عن الوجوه، ومساعد ذكي، وإشعارات فورية.',
                'solution' => 'Members find themselves in event photography through AWS Rekognition face search rather than scrolling thousands of images. Firebase Cloud Messaging drives notifications, and an in-app AI assistant answers member questions.',
                'solution_ar' => 'يعثر الأعضاء على صورهم في تغطية الفعاليات عبر البحث عن الوجوه بـAWS Rekognition بدل تصفّح آلاف الصور. ويدير Firebase Cloud Messaging الإشعارات، بينما يجيب مساعد ذكي داخل التطبيق عن أسئلة الأعضاء.',
                'services' => ['mobile-apps', 'ai-solutions', 'backend-cloud'],
                'technologies' => ['flutter', 'dart', 'firebase', 'aws-rekognition', 'bloc', 'nestjs', 'sentry'],
            ],
            [
                'slug' => 'wakil-topup',
                'name' => 'Wakil Topup',
                'client_slug' => 'topline-digital',
                'client_name' => 'Topline Digital',
                'industry' => 'fintech',
                'featured' => true,
                'summary' => 'A digital-products marketplace for mobile top-ups and gift cards, with wallet functionality, transaction history and an admin dashboard.',
                'summary_ar' => 'متجر منتجات رقمية لتعبئة أرصدة الجوال وبطاقات الهدايا، مع محفظة وسجل معاملات ولوحة تحكم إدارية.',
                'solution' => 'One Flutter codebase serving iOS, Android and web, with offline persistence in SQLite, type-safe models via Freezed, and an operator-facing dashboard for catalogue, orders and reconciliation.',
                'solution_ar' => 'شيفرة Flutter واحدة تخدم iOS وأندرويد والويب، مع تخزين محلي يعمل دون اتصال عبر SQLite، ونماذج محددة الأنواع بـFreezed، ولوحة تحكم للمشغّلين تغطي الكتالوج والطلبات والتسويات.',
                'website_url' => 'https://wakilcard.com',
                'services' => ['mobile-apps', 'web-development', 'dashboards'],
                'technologies' => ['flutter', 'dart', 'sqlite', 'getit', 'injectable', 'freezed'],
            ],
            [
                'slug' => 'al-bustan',
                'name' => 'Al Bustan',
                'client_slug' => 'bustan-retail-group',
                'client_name' => 'Bustan Retail Group',
                'industry' => 'retail',
                'featured' => false,
                'summary' => 'An e-commerce app with product catalog, shopping cart, secure checkout and payment integration.',
                'summary_ar' => 'تطبيق تجارة إلكترونية بكتالوج منتجات وسلة شراء وإتمام طلب آمن وتكامل مع بوابات الدفع.',
                'solution' => 'Flutter client against a TypeScript and Express.js backend, covering catalogue, cart, checkout and payment capture end to end.',
                'solution_ar' => 'واجهة Flutter تعمل على خلفية بـTypeScript وExpress.js، تغطي الكتالوج والسلة وإتمام الطلب وتحصيل الدفع من طرف إلى طرف.',
                'services' => ['mobile-apps', 'backend-cloud'],
                'technologies' => ['flutter', 'dart', 'expressjs', 'typescript'],
            ],
            [
                'slug' => 'captain-car',
                'name' => 'Captain Car',
                'client_slug' => 'metro-mobility',
                'client_name' => 'Metro Mobility',
                'industry' => 'mobility',
                'featured' => false,
                'summary' => 'A taxi-booking app supporting on-demand and pre-scheduled rides, referral rewards and trip management.',
                'summary_ar' => 'تطبيق لحجز سيارات الأجرة يدعم الرحلات الفورية والمجدولة، ومكافآت الإحالة، وإدارة الرحلات.',
                'solution' => 'Ride lifecycle from request through dispatch to completion, with scheduled bookings and a referral rewards loop to drive repeat usage.',
                'solution_ar' => 'دورة حياة كاملة للرحلة من الطلب إلى الإسناد إلى الإنهاء، مع حجز مسبق ونظام مكافآت إحالة يحفّز الاستخدام المتكرر.',
                'services' => ['mobile-apps'],
                'technologies' => ['flutter', 'dart'],
            ],
            [
                'slug' => 'reset-weight-loss',
                'name' => 'Reset — Lasting Weight Loss',
                'client_slug' => 'reset-health',
                'client_name' => 'Reset Health',
                'industry' => 'health',
                'featured' => false,
                'summary' => 'A health and fitness companion app supporting a structured weight-loss programme for members.',
                'summary_ar' => 'تطبيق مرافق للصحة واللياقة يدعم برنامجًا منظّمًا لإنقاص الوزن للأعضاء.',
                'solution' => 'Programme tracking, progress logging and member-facing guidance delivered in a single Flutter app.',
                'solution_ar' => 'متابعة البرنامج وتسجيل التقدّم وإرشاد موجّه للأعضاء، في تطبيق Flutter واحد.',
                'services' => ['mobile-apps'],
                'technologies' => ['flutter', 'dart'],
            ],
            [
                'slug' => 'reserva',
                'name' => 'Reserva',
                'client_slug' => 'reserva-hospitality',
                'client_name' => 'Reserva Hospitality',
                'industry' => 'hospitality',
                'featured' => false,
                'summary' => 'A restaurant reservation app with real-time booking, an interactive calendar, user profiles and notifications.',
                'summary_ar' => 'تطبيق لحجز الطاولات في المطاعم مع حجز فوري وتقويم تفاعلي وملفات مستخدمين وإشعارات.',
                'solution' => 'Firebase-backed real-time availability so two guests can never take the same table, with an interactive calendar and notification-driven reminders.',
                'solution_ar' => 'توفّر فوري مدعوم بـFirebase فلا يمكن أبدًا أن يحجز ضيفان الطاولة نفسها، مع تقويم تفاعلي وتذكيرات عبر الإشعارات.',
                'services' => ['mobile-apps'],
                'technologies' => ['flutter', 'dart', 'firebase'],
            ],
        ];
    }
}
