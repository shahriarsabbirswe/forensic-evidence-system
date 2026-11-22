<?php

namespace App\Providers;

use App\Models\Evidence;
use App\Models\EvidenceTransfer;
use App\Observers\EvidenceObserver;
use App\Observers\EvidenceTransferObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Evidence::observe(EvidenceObserver::class);

        EvidenceTransfer::observe(EvidenceTransferObserver::class);
    }
}
