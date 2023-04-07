<?php

namespace App\Modules\Loan\Observers;

use App\Modules\Loan\Jobs\RepaymentGeneratingJob;
use App\Modules\Loan\Models\Loan;

class LoanObserver
{
    public function saved(Loan $loan)
    {
       $this->shouldGenerateRepaymentSchedule($loan);
    }

    /**
     * if loan is approved, generate repayment schedules
     *
     * @param Loan $loan
     */
    private function shouldGenerateRepaymentSchedule(Loan $loan){
        if (!$loan->isDirty('status') || $loan->status != Loan::STATUS_APPROVED){
            return;
        }

        RepaymentGeneratingJob::dispatch($loan);
    }
}