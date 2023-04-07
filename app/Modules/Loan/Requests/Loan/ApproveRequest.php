<?php

namespace App\Modules\Loan\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;

class ApproveRequest extends FormRequest
{
    public function authorize()
    {
        $loan = $this->route('loan');
        return auth()->user()->can('approve', $loan);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'notes' => ['nullable', 'string', 'max:255']
        ];
    }
}