<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\DanhMucRepository;
use App\Repositories\Interfaces\DanhMucRepositoryInterface;
use App\Services\DanhMucService;
use App\Services\Interfaces\DanhMucServiceInterface;

class DanhMucServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(DanhMucRepositoryInterface::class, DanhMucRepository::class);
        $this->app->bind(DanhMucServiceInterface::class, DanhMucService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
