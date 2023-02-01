<?php

namespace App\Providers;

use App\Http\Controllers\API\PaymentProcessController;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->app->bind(PaymentProcessController::class, function($app) {
		// 	return new PaymentProcessController();
		// });
    }
}
