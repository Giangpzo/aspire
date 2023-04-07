<?php

namespace App\Modules\Loan\Policies;

use App\Modules\Auth\Models\User;
use App\Modules\Loan\Models\Loan;

class LoanPolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Loan $loan
     * @return bool
     */
    public function view(User $user, Loan $loan): bool
    {
        if ($user->is_admin) {
            return true;
        }

        if ($user->is_customer && $loan->customer_id == auth()->id()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can approve the model.
     *
     * @param User $user
     * @param Loan $loan
     * @return bool
     */
    public function approve(User $user, Loan $loan): bool
    {
        /**
         *  must satisfy two conditions:
         *  1. approver is admin
         *  2. loan is pending
         */
        return $user->is_admin && $loan->status == Loan::STATUS_PENDING;
    }

    /**
     * Determine whether the user can reject the model.
     *
     * @param User $user
     * @param Loan $loan
     * @return bool
     */
    public function reject(User $user, Loan $loan): bool
    {
        /**
         *  must satisfy two conditions:
         *  1. approver is admin
         *  2. loan is pending
         */
        return $user->is_admin && $loan->status == Loan::STATUS_PENDING;
    }

    /**
     * Determine whether the user can request loan
     *
     * @param User $user
     * @return mixed
     */
    public function requestLoan(User $user)
    {
        return $user->is_customer;
    }
}
