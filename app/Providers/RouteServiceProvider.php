<?php

namespace App\Providers;

use App\Repositories\AdminRepository;
use App\Repositories\DepositRepository;
use App\Repositories\FundRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';


    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('admin')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/user.php'));

            Route::prefix('sys')
                ->middleware('sys')
                ->namespace($this->namespace)
                ->group(base_path('routes/sys.php'));
        });

        $pattern = '^[1-9]\d*$';
        Route::patterns([
            'admin' => $pattern,
            'bank_card' => $pattern,
            'news' => $pattern,
            'user' => $pattern,
			'child' => $pattern,
            'fund' => $pattern,
            'payment' => $pattern
        ]);

        Route::bind('admin', function ($value) {
            return app(AdminRepository::class)->whereDoesntHave('roles', function (Builder $query) {
                $query->whereKey(1);
            })->findOrFail($value);
        });

        Route::bind('user', function ($value) {
            return app(UserRepository::class)->whereId($value)->whereNotNull('api_key')->firstOrFail();
        });

        Route::bind('child', function ($value) {
            return app(UserRepository::class)->whereId($value)->whereApiKey(null)->firstOrFail();
        });

        Route::bind('fund', function ($value) {
            return app(FundRepository::class)->whereId($value)->whereUnfrozen(false)->has('user')->firstOrFail();
        });

        Route::bind('deposit', function ($value) {
            return app(DepositRepository::class)->whereId($value)->whereStatus(0)->has('user')->firstOrFail();
        });

        Route::bind('payment', function ($value) {
            $repository = app(PaymentRepository::class);
            switch (request()->route()->getName()) {
                case 'admin.payments.task.lock':
                    return $repository->hasNo('admin')->whereStatus(0)->findOrFail($value);
                case 'admin.payments.update':
                    return $repository->whereStatus(0)->whereNotNull('admin_id')->findOrFail($value);
                case 'admin.payments.payback-stamps.store':
                    return $repository->hasNo('paybackStamp')->whereStatus(1)->findOrFail($value);
                case 'admin.payments.rewind-stamps.store':
                    return $repository->hasNo('rewindStamp')->whereStatus(-1)->findOrFail($value);
            }

            abort(404);
            throw new Exception();
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
