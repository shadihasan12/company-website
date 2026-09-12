<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_configured_step_renders_with_copy(): void
    {
        $response = $this->get('/en')->assertOk();

        foreach (config('site.process') as $step) {
            $key = $step['key'];

            $response->assertSee(__("home.process.steps.{$key}.title"));
            $response->assertSee(__("home.process.steps.{$key}.body"));
        }
    }

    public function test_steps_are_numbered_in_configured_order(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        $first = config('site.process')[0]['key'];

        $this->assertLessThan(
            strpos($html, __("home.process.steps.{$first}.title")) + 400,
            strpos($html, '01'),
        );
    }

    public function test_trimming_the_configuration_trims_the_timeline(): void
    {
        config()->set('site.process', [['key' => 'discovery', 'icon' => 'magnifier']]);

        $this->get('/en')
            ->assertOk()
            ->assertSee(__('home.process.steps.discovery.title'))
            ->assertDontSee(__('home.process.steps.release.title'));
    }

    public function test_the_section_disappears_when_no_steps_are_configured(): void
    {
        config()->set('site.process', []);

        $this->get('/en')->assertOk()->assertDontSee(__('home.process.title'));
    }
}
