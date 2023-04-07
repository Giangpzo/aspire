<?php

namespace App\Modules\Loan\ServiceProviders;

use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Observers\LoanObserver;
use Illuminate\Support\ServiceProvider;

class LoanServiceProvider extends ServiceProvider
{
    public function boot(){
        Loan::observe(LoanObserver::class);
    }
}