<?php

namespace App\Modules\Loan\ServiceProviders;

use App\Modules\Loan\DataProvider\LoanDataProvider;
use App\Modules\Loan\DataProvider\ScheduledRepaymentProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as LaravelRouteServiceProvider;

class RouteServiceProvider extends LaravelRouteServiceProvider
{
    public function boot()
    {
        Route::bind('loan', function ($id) {
            /** @var LoanDataProvider $provider */
            $provider = app()->make(LoanDataProvider::class);
            $loan = $provider->getById($id);
            if (empty($loan)){
                throw new ModelNotFoundException('The loan you are finding is not exist or not belong to you!');
            }

            return $loan;
        });

        Route::bind('repayment', function ($id) {
            /** @var ScheduledRepaymentProvider $provider */
            $provider = app()->make(ScheduledRepaymentProvider::class);
            $repayment = $provider->getById($id);
            if (empty($repayment)){
                throw new ModelNotFoundException('The repayment you are finding is not exist or not belong to you!');
            }

            return $repayment;
        });

        $this->routes(function () {
            Route::prefix('api/v1/loans')
                ->name('loans.')
                ->middleware(['api', 'auth:api'])
                ->group(__DIR__ . '/../Routes/api.php');
        });
    }
}