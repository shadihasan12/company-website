<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;

class MakeAdmin extends Command
{
    protected $signature = 'make:admin {email?} {--name=} {--password=}';

    protected $description = 'Create or promote a user who can access the admin panel';

    public function handle(): int
    {
        $email = $this->argument('email') ?: text('Email', required: true);

        $existing = User::where('email', $email)->first();

        if ($existing) {
            $existing->update(['is_admin' => true]);
            $this->components->info("Promoted [{$email}] to admin.");

            return self::SUCCESS;
        }

        // Generated rather than prompted so a password is never typed into
        // shell history on first setup.
        $password = $this->option('password') ?: Str::password(16);

        User::create([
            'name' => $this->option('name') ?: Str::before($email, '@'),
            'email' => $email,
            'password' => $password,
            'is_admin' => true,
        ]);

        $this->components->info("Created admin [{$email}].");

        if (! $this->option('password')) {
            $this->newLine();
            $this->components->warn('Password (shown once — save it now):');
            $this->line("  <fg=cyan;options=bold>{$password}</>");
            $this->newLine();
        }

        return self::SUCCESS;
    }
}
