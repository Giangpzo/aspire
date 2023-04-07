<?php

namespace App\Modules\Loan\Requests\Repayment;

use App\Modules\Loan\Models\ScheduledRepayment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RepayRequest extends FormRequest
{
    public function authorize()
    {
        $loan = $this->route('loan');
        $authUser = auth()->user();

        // if cannot view loan --> cannot repay
        if (!$authUser->can('view', $loan)){
            return false;
        }

        // if auth user is not loan's owner --> cannot repay
        if ($authUser->id != $loan->customer_id){
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => ['required', 'integer']
        ];
    }

    public function withValidator(Validator $validator)
    {
        $this->validateAmount($validator);
        $this->validateStatus($validator);
    }

    private function validateAmount($validator)
    {
        $amount = $this->get('amount');
        $repayment = $this->route('repayment');
        return $validator->after(function ($validator) use ($amount,$repayment){
            if ($amount < $repayment->amount){
                $validator->errors()->add('amount', 'Repayment amount has to greater than '.$repayment->amount);
            }
        });
    }

    private function validateStatus($validator){
        $repayment = $this->route('repayment');
        return $validator->after(function ($validator) use ($repayment){
            if ($repayment->status != ScheduledRepayment::STATUS_UNPAID){
                $validator->errors()->add('repayment_status', 'Cannot repay paid repayment');
            }
        });
    }
}