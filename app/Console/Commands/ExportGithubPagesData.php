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
            $pushed = app(\App\Services\GithubPagesSync::class)->push();
            $this->info($pushed ? 'Berhasil push ke GitHub.' : 'Push dilewati (sync belum dikonfigurasi - cek .env).');
        }

        return self::SUCCESS;
    }
}
