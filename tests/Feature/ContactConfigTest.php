<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Guards the fallback chain behind the contact address.
 *
 * `.env.example` ships `LEADS_NOTIFY_EMAIL=` — the key is present but empty.
 * env()'s second argument only applies to a *missing* key, so reading it that
 * way resolved to an empty string and CaptureLead silently skipped every lead
 * notification on a fresh install.
 */
class ContactConfigTest extends TestCase
{
    /**
     * @var array<int, string>
     */
    private array $keys = ['LEADS_NOTIFY_EMAIL', 'SITE_EMAIL'];

    protected function tearDown(): void
    {
        foreach ($this->keys as $key) {
            unset($_SERVER[$key], $_ENV[$key]);
        }

        parent::tearDown();
    }

    /**
     * @param  array<string, string>  $env
     * @return array<string, mixed>
     */
    private function siteConfig(array $env): array
    {
        foreach ($env as $key => $value) {
            $_SERVER[$key] = $value;
        }

        return require config_path('site.php');
    }

    public function test_a_blank_notify_address_falls_back_to_the_site_email(): void
    {
        $config = $this->siteConfig([
            'LEADS_NOTIFY_EMAIL' => '',
            'SITE_EMAIL' => 'team@example.test',
        ]);

        $this->assertSame('team@example.test', $config['leads']['notify']);
    }

    public function test_a_blank_site_email_still_leaves_a_reachable_address(): void
    {
        $config = $this->siteConfig([
            'LEADS_NOTIFY_EMAIL' => '',
            'SITE_EMAIL' => '',
        ]);

        $this->assertNotSame('', $config['leads']['notify']);
        $this->assertNotSame('', $config['contact']['email']);
    }

    public function test_an_explicit_notify_address_wins(): void
    {
        $config = $this->siteConfig([
            'LEADS_NOTIFY_EMAIL' => 'leads@example.test',
            'SITE_EMAIL' => 'team@example.test',
        ]);

        $this->assertSame('leads@example.test', $config['leads']['notify']);
        $this->assertSame('team@example.test', $config['contact']['email']);
    }
}
