<?php

namespace App\Providers;

use App\Repositories\TransactionRepository;
use App\Services\Payme\Handlers\CreateTransactionHandler as PaymeCreateTransactionHandler;
use App\Services\Uzum\Handlers\CreateTransactionHandler as UzumCreateTransactionHandler;
use App\Services\Payme\PaymeDebugScenarioHandler;
use App\Services\Uzum\UzumDebugScenarioHandler;
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
                new PaymeCreateTransactionHandler($app->make(TransactionRepository::class))
            );
        });

        $this->app->when(UzumDebugScenarioHandler::class)
            ->needs(UzumCreateTransactionHandler::class)
            ->give(function ($app) {
                return new UzumCreateTransactionHandler($app->make(TransactionRepository::class));
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
