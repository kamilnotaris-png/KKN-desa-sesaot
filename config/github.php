<?php

return [

    /*
    |--------------------------------------------------------------------
    | Sinkronisasi data titik wisata ke docs/data.json di GitHub Pages
    |--------------------------------------------------------------------
    |
    | Supaya peta publik statis di GitHub Pages tidak bergantung uptime
    | VPS, tiap kali data titik wisata disimpan lewat admin panel,
    | docs/data.json otomatis di-commit ulang lewat GitHub API.
    |
    | Nonaktif secara default sampai GITHUB_TOKEN diisi di .env - Personal
    | Access Token dengan scope "repo" (Contents: Read and write) khusus
    | milik repo ini, JANGAN pakai token dengan akses lebih luas dari itu.
    |
    */

    'pages_sync' => [
        'enabled' => env('GITHUB_PAGES_SYNC_ENABLED', false),
        'token' => env('GITHUB_TOKEN'),
        'owner' => env('GITHUB_REPO_OWNER', 'kamilnotaris-png'),
        'repo' => env('GITHUB_REPO_NAME', 'KKN-desa-sesaot'),
        'branch' => env('GITHUB_REPO_BRANCH', 'main'),
        'path' => env('GITHUB_DATA_PATH', 'docs/data.json'),
    ],

];
