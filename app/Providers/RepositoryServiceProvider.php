<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Repositories\PaymentRepository::class, \App\Repositories\PaymentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\MerchantSettingRepository::class, \App\Repositories\MerchantSettingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WalletRepository::class, \App\Repositories\WalletRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TransactionRepository::class, \App\Repositories\TransactionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\DepositRepository::class, \App\Repositories\DepositRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AdminRepository::class, \App\Repositories\AdminRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ActivityRepository::class, \App\Repositories\ActivityRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\BankCardRepository::class, \App\Repositories\BankCardRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaybackStampRepository::class, \App\Repositories\PaybackStampRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\NewsRepository::class, \App\Repositories\NewsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ContractRepository::class, \App\Repositories\ContractRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\NodeRepository::class, \App\Repositories\NodeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AuthorizerRepository::class, \App\Repositories\AuthorizerRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WhiteIpRepository::class, \App\Repositories\WhiteIpRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\RewindStampRepository::class, \App\Repositories\RewindStampRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AccountOperationRepository::class, \App\Repositories\AccountOperationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FundRepository::class, \App\Repositories\FundRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PermissionRepository::class, \App\Repositories\PermissionRepositoryEloquent::class);
        //:end-bindings:
    }
}
