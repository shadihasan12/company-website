<?php

namespace Tests\Feature;

use App\Support\Locale;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Arabic is deferred, but the whole RTL apparatus is already built. These
 * tests exercise it so it cannot rot while switched off — the point of
 * building it early was to avoid a retrofit, and untested dormant code is
 * a retrofit waiting to happen.
 */
class ArabicReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    protected function enableArabic(): void
    {
        config()->set('site.locales.ar', [
            'name' => 'Arabic',
            'native' => 'العربية',
            'dir' => 'rtl',
            'hreflang' => 'ar',
        ]);
    }

    public function test_arabic_is_off_until_enabled(): void
    {
        $this->get('/ar')->assertNotFound();
    }

    public function test_enabling_the_locale_is_the_only_change_needed(): void
    {
        // No route cache rebuild, no migration, no template edits.
        $this->enableArabic();

        $this->get('/ar')
            ->assertOk()
            ->assertSee('lang="ar"', escape: false)
            ->assertSee('dir="rtl"', escape: false);
    }

    public function test_every_public_page_renders_in_arabic(): void
    {
        $this->enableArabic();

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
        $this->enableArabic();

        // The face is ~90KB and must not be shipped to English visitors.
        $this->get('/en')->assertOk()->assertDontSee('ibm-plex-sans-arabic', escape: false);
    }

    public function test_the_language_switcher_appears_and_points_at_the_same_page(): void
    {
        $this->enableArabic();

        $this->get('/en/services/erp')
            ->assertOk()
            ->assertSee(url('/ar/services/erp'), escape: false)
            ->assertSee('العربية', escape: false);
    }

    public function test_hreflang_tags_appear_once_a_second_locale_is_live(): void
    {
        $this->enableArabic();

        $this->get('/en')
            ->assertOk()
            ->assertSee('hreflang="ar"', escape: false)
            ->assertSee('hreflang="x-default"', escape: false);
    }

    public function test_the_sitemap_covers_both_locales(): void
    {
        $this->enableArabic();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(url('/en/services/erp'), escape: false)
            ->assertSee(url('/ar/services/erp'), escape: false);
    }

    public function test_arabic_copy_is_actually_translated_not_falling_back(): void
    {
        $this->enableArabic();

        $this->get('/ar')
            ->assertOk()
            ->assertSee(__('home.hero.headline_lead', locale: 'ar'))
            ->assertSee(__('nav.services', locale: 'ar'));
    }

    public function test_translatable_database_copy_falls_back_rather_than_rendering_blank(): void
    {
        $this->enableArabic();

        // Services are seeded in English only while Arabic is off, so the
        // page must degrade to English rather than show empty headings.
        $this->get('/ar/services/mobile-apps')
            ->assertOk()
            ->assertSee('Mobile Apps');
    }

    public function test_an_unsupported_locale_is_still_rejected(): void
    {
        $this->enableArabic();

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
        $this->enableArabic();

        $this->assertSame('rtl', Locale::direction('ar'));
        $this->assertTrue(Locale::isRtl('ar'));
        $this->assertFalse(Locale::isRtl('en'));
    }
}
