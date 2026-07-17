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
        $json = TitikWisataGeoJsonExporter::toJson(withGeneratedAt: true);
        file_put_contents(base_path('docs/data.json'), $json);

        $count = count(json_decode($json, true)['features']);
        $this->info("docs/data.json diperbarui ({$count} titik wisata aktif).");

        if ($this->option('push')) {
            $pushed = app(\App\Services\GithubPagesSync::class)->push();
            $this->info($pushed ? 'Berhasil push ke GitHub.' : 'Push dilewati (sync belum dikonfigurasi - cek .env).');
        }

        return self::SUCCESS;
    }
}
