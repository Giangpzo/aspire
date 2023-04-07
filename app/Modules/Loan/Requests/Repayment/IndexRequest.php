<?php

namespace App\Modules\Loan\Requests\Repayment;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize()
    {
        $loan = $this->route('loan');

        // if user can view loan --> he can view loan's repayment details
        return auth()->user()->can('view', $loan);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}