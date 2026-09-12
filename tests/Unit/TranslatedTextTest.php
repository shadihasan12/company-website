<?php

namespace Tests\Unit;

use App\Support\TranslatedText;
use Tests\TestCase;

class TranslatedTextTest extends TestCase
{
    public function test_it_resolves_the_active_locale(): void
    {
        $text = new TranslatedText(['en' => 'Mobile Apps', 'ar' => 'تطبيقات الجوال']);

        $this->assertSame('Mobile Apps', $text->in('en'));
        $this->assertSame('تطبيقات الجوال', $text->in('ar'));
    }

    public function test_it_falls_back_rather_than_rendering_a_blank(): void
    {
        // A half-translated record must still show something on the page.
        $text = new TranslatedText(['en' => 'Dashboards', 'ar' => '']);

        $this->assertSame('Dashboards', $text->in('ar'));
    }

    public function test_it_falls_back_to_any_populated_locale(): void
    {
        $text = new TranslatedText(['de' => 'Nur Deutsch']);

        $this->assertSame('Nur Deutsch', $text->in('en'));
    }

    public function test_it_casts_to_string_for_blade(): void
    {
        $text = new TranslatedText(['en' => 'Clean Cody']);

        $this->assertSame('Clean Cody', (string) $text);
    }

    public function test_an_empty_value_stringifies_to_an_empty_string(): void
    {
        $text = new TranslatedText;

        $this->assertTrue($text->isEmpty());
        $this->assertSame('', (string) $text);
    }

    public function test_a_plain_string_is_treated_as_the_fallback_locale(): void
    {
        $text = TranslatedText::make('Backend & Cloud');

        $this->assertSame(['en' => 'Backend & Cloud'], $text->toArray());
    }
}
