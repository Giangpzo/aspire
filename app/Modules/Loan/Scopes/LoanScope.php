<?php

namespace App\Modules\Loan\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class LoanScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // if has no auth user login or user is admin, allow getting all data
        if (!Auth::hasUser() || auth()->user()->is_admin){
            return;
        }

        $builder->where('customer_id', auth()->id());
    }
}