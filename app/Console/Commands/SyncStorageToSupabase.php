<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SyncStorageToSupabase extends Command
{
    protected $signature = 'storage:sync-supabase {--force : Overwrite existing files on Supabase}';
    protected $description = 'Sync all local storage public files (photos, ids, pdfs, questions) to Supabase Storage';

    public function handle()
    {
        $localBase = storage_path('app/public');
        if (!is_dir($localBase)) {
            $this->warn("Local storage directory does not exist: {$localBase}");
            return Command::SUCCESS;
        }

        $this->info("Scanning local files in {$localBase}...");

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($localBase, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $supabase = Storage::disk('supabase');
        $force = $this->option('force');

        $uploaded = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($it as $file) {
            if (!$file->isFile()) continue;
            if ($file->getFilename() === '.gitignore') continue;

            $full = $file->getRealPath();
            $rel = ltrim(str_replace('\\', '/', substr($full, strlen($localBase))), '/');

            try {
                if (!$force && $supabase->exists($rel)) {
                    $skipped++;
                    continue;
                }

                $stream = fopen($full, 'r');
                $mime = mime_content_type($full) ?: 'application/octet-stream';
                $supabase->put($rel, $stream, [
                    'ContentType' => $mime,
                    'visibility'  => 'public',
                ]);
                if (is_resource($stream)) {
                    fclose($stream);
                }

                $this->line("<info>Synced:</info> {$rel}");
                $uploaded++;
            } catch (\Throwable $e) {
                $this->error("Failed {$rel}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("Sync completed: {$uploaded} uploaded, {$skipped} already present, {$errors} errors.");
        return Command::SUCCESS;
    }
}
