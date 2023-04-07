<?php

namespace App\Modules\Loan\Transformers;

use App\Modules\Loan\Models\Loan;
use League\Fractal\TransformerAbstract;

class LoanShowTransformer extends TransformerAbstract
{
    public function transform(Loan $loan)
    {
        return [
            'id' => $loan->id,
            'customer_id' => $loan->customer_id,
            'amount' => $loan->amount,
            'term' => $loan->term,
            'status' => $loan->status,
            'approver_id' => $loan->approver_id,
            'approver_notes' => $loan->approver_notes
        ];
    }
}