<?php

namespace App\Services;

use App\Support\TitikWisataGeoJsonExporter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GithubPagesSync
{
    /**
     * Push docs/data.json terbaru ke GitHub lewat Contents API.
     * Return false (tanpa exception) kalau sync belum dikonfigurasi,
     * supaya gagal-nya tidak pernah menjatuhkan alur simpan di admin panel.
     */
    public function push(): bool
    {
        $config = config('github.pages_sync');

        if (! $config['enabled'] || empty($config['token'])) {
            Log::info('GithubPagesSync: dilewati, GITHUB_PAGES_SYNC_ENABLED/GITHUB_TOKEN belum diisi.');

            return false;
        }

        $http = Http::withToken($config['token'])
            ->withHeaders(['Accept' => 'application/vnd.github+json']);

        $default = config('languages.default');
        $ok = true;

        foreach (array_keys(config('languages.supported')) as $locale) {
            $path = $locale === $default
                ? $config['path']
                : preg_replace('/\.json$/', ".{$locale}.json", $config['path']);

            $ok = $this->pushFile($http, $config, $path, $locale) && $ok;
        }

        return $ok;
    }

    private function pushFile(\Illuminate\Http\Client\PendingRequest $http, array $config, string $path, string $locale): bool
    {
        $base = "https://api.github.com/repos/{$config['owner']}/{$config['repo']}/contents/{$path}";
        $sha = $this->currentFileSha($http, $base, $config['branch']);

        $content = TitikWisataGeoJsonExporter::toJson(withGeneratedAt: true, locale: $locale);

        $response = $http->put($base, array_filter([
            'message' => 'Update data titik wisata via admin panel ('.now()->toDateTimeString().')',
            'content' => base64_encode($content),
            'branch' => $config['branch'],
            'sha' => $sha,
        ]));

        if ($response->failed()) {
            Log::error('GithubPagesSync: gagal push ke GitHub.', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return false;
        }

        return true;
    }

    private function currentFileSha(\Illuminate\Http\Client\PendingRequest $http, string $url, string $branch): ?string
    {
        $response = $http->get($url, ['ref' => $branch]);

        return $response->ok() ? $response->json('sha') : null;
    }
}
