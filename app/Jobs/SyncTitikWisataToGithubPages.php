<?php

namespace App\Jobs;

use App\Services\GithubPagesSync;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Unique selama masih di antrian/diproses supaya beberapa save beruntun
 * (mis. db:seed yang updateOrCreate banyak record sekaligus) tidak
 * masing-masing dispatch job sendiri-sendiri - kalau tidak, semuanya
 * rebutan menulis file docs/data*.json yang sama ke GitHub secara
 * bersamaan dan saling gagal dengan 409 Conflict (sha berubah di antara
 * fetch dan write).
 */
class SyncTitikWisataToGithubPages implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $uniqueFor = 60;

    public function uniqueId(): string
    {
        return 'github-pages-sync';
    }

    public function handle(GithubPagesSync $sync): void
    {
        $sync->push();
    }
}
