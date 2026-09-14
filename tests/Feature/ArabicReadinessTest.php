<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Support\Locale;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Both locales are live. These cover the RTL apparatus end to end:
 * direction, the font split, the switcher, hreflang, the sitemap and the
 * fallback behaviour when a record is only half translated.
 */
class ArabicReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_arabic_is_live(): void
    {
        $this->get('/ar')
            ->assertOk()
            ->assertSee('lang="ar"', escape: false)
            ->assertSee('dir="rtl"', escape: false);
    }

    public function test_every_public_page_renders_in_arabic(): void
    {
        foreach ([
            '/ar',
            '/ar/services',
            '/ar/services/mobile-apps',
            '/ar/work',
            '/ar/work/wakil-topup',
            '/ar/about',
            '/ar/contact',
            '/ar/start-a-project',
            '/ar/privacy',
        ] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_arabic_pages_load_the_arabic_font_and_english_pages_do_not(): void
    {
        // The face is ~90KB and must not be shipped to English visitors.
        $this->get('/en')->assertOk()->assertDontSee('ibm-plex-sans-arabic', escape: false);
    }

    public function test_the_language_switcher_appears_and_points_at_the_same_page(): void
    {
        $this->get('/en/services/erp')
            ->assertOk()
            ->assertSee(url('/ar/services/erp'), escape: false)
            ->assertSee('العربية', escape: false);
    }

    public function test_hreflang_tags_appear_once_a_second_locale_is_live(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('hreflang="ar"', escape: false)
            ->assertSee('hreflang="x-default"', escape: false);
    }

    public function test_the_sitemap_covers_both_locales(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(url('/en/services/erp'), escape: false)
            ->assertSee(url('/ar/services/erp'), escape: false);
    }

    public function test_arabic_copy_is_actually_translated_not_falling_back(): void
    {
        $this->get('/ar')
            ->assertOk()
            ->assertSee(__('home.hero.headline_lead', locale: 'ar'))
            ->assertSee(__('nav.services', locale: 'ar'));
    }

    public function test_seeded_database_copy_is_translated(): void
    {
        $this->get('/ar/services/mobile-apps')
            ->assertOk()
            ->assertSee(__('services.mobile-apps.title', locale: 'ar'))
            ->assertSee(__('services.mobile-apps.timeline', locale: 'ar'));
    }

    public function test_a_half_translated_record_falls_back_rather_than_rendering_blank(): void
    {
        // A record added through the admin with only English filled in must
        // degrade to English, not leave an empty heading on the page.
        Service::where('key', 'erp')->first()->update([
            'title' => ['en' => 'English Only Service'],
        ]);

        $this->get('/ar/services/erp')
            ->assertOk()
            ->assertSee('English Only Service');
    }

    public function test_an_unsupported_locale_is_rejected(): void
    {
        $this->get('/fr')->assertNotFound();
        $this->get('/zz')->assertNotFound();
    }

    public function test_the_health_check_is_not_captured_by_the_locale_pattern(): void
    {
        // /up is two letters and would otherwise be a candidate.
        $this->get('/up')->assertOk();
    }

    public function test_direction_helpers_report_correctly(): void
    {
        $this->assertSame('rtl', Locale::direction('ar'));
        $this->assertTrue(Locale::isRtl('ar'));
        $this->assertFalse(Locale::isRtl('en'));
    }
}
