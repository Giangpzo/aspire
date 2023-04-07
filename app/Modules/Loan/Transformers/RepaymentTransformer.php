<?php

namespace App\Modules\Loan\Transformers;

use App\Modules\Loan\Models\ScheduledRepayment;
use League\Fractal\TransformerAbstract;

class RepaymentTransformer extends TransformerAbstract
{
    public function transform(ScheduledRepayment $repayment)
    {
        return [
            'id' => $repayment->id,
            'loan_id' => $repayment->loan_id,
            'repayment_date' => $repayment->repayment_date,
            'amount' => $repayment->amount,
            'actual_repayment_date' => $repayment->actual_repayment_date,
            'actual_amount' => $repayment->actual_amount,
            'status' => $repayment->status
        ];
    }
}