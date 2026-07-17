<?php

namespace App\Observers;

use App\Jobs\SyncTitikWisataToGithubPages;
use App\Models\TitikWisata;

class TitikWisataObserver
{
    public function saved(TitikWisata $titikWisata): void
    {
        SyncTitikWisataToGithubPages::dispatch();
    }

    public function deleted(TitikWisata $titikWisata): void
    {
        SyncTitikWisataToGithubPages::dispatch();
    }
}
