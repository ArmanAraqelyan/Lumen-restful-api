<?php

namespace App\Providers;
use App\Services\CompanyRepository;
use App\Services\Contracts\CompanyRepositoryInterface;
use App\Services\Contracts\UserRepositoryInterface;
use App\Services\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
    }
}