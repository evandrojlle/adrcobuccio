<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletRequest extends FormRequest
{

    const TYPES = [
        'CREDIT',
        'DEBIT',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->request->get('wallet_id');
        return [
            'wallet_id' => [
                ($this->isMethod('put') ? 'required' : 'nullable'),
                'integer'
            ],
            'owner_id' => [
                'required',
                'integer',
            ],
            'amount_transaction' => [
                'required',
                'decimal:2'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'wallet_id.required' => __('The wallet id field is required.'),
            'owner_id.required' => __('The owner id field is required.'),
            'amount_transaction' => __('The amount transaction field is required.'),
            'amount_transaction.decimal' => __('The amount transaction must have up to 8 digits with 2 decimals (10 digits).'),
        ];
    }
}
