<?php

namespace App\Modules\Loan\Transformers;

use App\Modules\Auth\Transformers\UserTransformer;
use App\Modules\Loan\Models\Loan;
use League\Fractal\TransformerAbstract;

class LoanShowTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'customer'
    ];

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

    public function includeCustomer(Loan $loan)
    {
        $customer = $loan->customer;

        if (!$customer){
            return $this->item(null);
        }

        return $this->item($customer, new UserTransformer());
    }
}