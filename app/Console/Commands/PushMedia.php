<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Copies locally stored media onto another disk — the one-off move to
 * object storage, and the way to re-sync afterwards.
 *
 * Paths are preserved exactly. The database addresses every image by
 * relative path, so `projects/gallery/wakil-topup-01.webp` must land at
 * that same key on the target or every reference breaks.
 */
class PushMedia extends Command
{
    protected $signature = 'media:push
        {--from=public : Disk to copy from}
        {--to=s3 : Disk to copy to}
        {--force : Overwrite files that already exist on the target}
        {--dry-run : List what would be copied without writing anything}';

    protected $description = 'Copy uploaded media onto another filesystem disk';

    public function handle(): int
    {
        $from = Storage::disk($this->option('from'));
        $to = Storage::disk($this->option('to'));
        $dryRun = (bool) $this->option('dry-run');

        $files = collect($from->allFiles())
            // .gitignore lives in the media root to keep the directory in
            // version control; it is not media.
            ->reject(fn (string $path) => str_starts_with(basename($path), '.'));

        if ($files->isEmpty()) {
            $this->components->warn("No files on the [{$this->option('from')}] disk.");

            return self::SUCCESS;
        }

        $copied = 0;
        $skipped = 0;

        foreach ($files as $path) {
            if (! $this->option('force') && $to->exists($path)) {
                $skipped++;

                continue;
            }

            $this->line(sprintf('  %s <fg=gray>(%s)</>', $path, $this->humanise($from->size($path))));

            if (! $dryRun) {
                $to->put($path, $from->get($path));
            }

            $copied++;
        }

        $this->newLine();
        $this->components->info(sprintf(
            '%s %d file(s)%s.',
            $dryRun ? 'Would copy' : 'Copied',
            $copied,
            $skipped ? ", skipped {$skipped} already present" : '',
        ));

        if (! $dryRun && $copied > 0) {
            $this->components->warn('Set MEDIA_DISK='.$this->option('to').' to serve them from there.');
        }

        return self::SUCCESS;
    }

    protected function humanise(int $bytes): string
    {
        return $bytes > 1024 * 1024
            ? round($bytes / 1024 / 1024, 1).'MB'
            : round($bytes / 1024).'KB';
    }
}
