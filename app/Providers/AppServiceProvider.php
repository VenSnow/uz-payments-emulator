<?php

namespace App\Providers;

use App\Repositories\Payme\TransactionRepository;
use App\Services\Payme\Handlers\CreateTransactionHandler;
use App\Services\Payme\PaymeDebugScenarioHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymeDebugScenarioHandler::class, function ($app) {
            return new PaymeDebugScenarioHandler(
                new CreateTransactionHandler($app->make(TransactionRepository::class))
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
