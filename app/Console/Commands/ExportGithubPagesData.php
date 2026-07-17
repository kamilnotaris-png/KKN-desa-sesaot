<?php

namespace App\Console\Commands;

use App\Support\TitikWisataGeoJsonExporter;
use Illuminate\Console\Command;

class ExportGithubPagesData extends Command
{
    protected $signature = 'export:github-pages {--push : Langsung push ke GitHub lewat API, bukan cuma tulis file lokal}';

    protected $description = 'Export data titik wisata ke docs/data.json untuk static site GitHub Pages';

    public function handle(): int
    {
        $default = config('languages.default');
        $locales = array_keys(config('languages.supported'));

        foreach ($locales as $locale) {
            $json = TitikWisataGeoJsonExporter::toJson(withGeneratedAt: true, locale: $locale);

            $filename = $locale === $default ? 'docs/data.json' : "docs/data.{$locale}.json";
            file_put_contents(base_path($filename), $json);

            $count = count(json_decode($json, true)['features']);
            $this->info("{$filename} diperbarui ({$count} titik wisata aktif).");
        }

        if ($this->option('push')) {
            $results = app(\App\Services\GithubPagesSync::class)->pushAll();

            if ($results === []) {
                $this->warn('Push dilewati (sync belum dikonfigurasi - cek GITHUB_PAGES_SYNC_ENABLED/GITHUB_TOKEN di .env).');
            } else {
                foreach ($results as $locale => $ok) {
                    $ok
                        ? $this->info("Push {$locale}: berhasil.")
                        : $this->error("Push {$locale}: GAGAL - cek storage/logs/laravel.log untuk detail.");
                }

                if (in_array(false, $results, true)) {
                    return self::FAILURE;
                }
            }
        }

        return self::SUCCESS;
    }
}
