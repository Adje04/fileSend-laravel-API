<?php

namespace App\Providers;

use App\Interfaces\InvitationInterface;
use App\Repositories\InvitationRepository;
use Illuminate\Support\ServiceProvider;

class InvitationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(InvitationInterface::class, InvitationRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
