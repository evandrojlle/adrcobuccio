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
                'unique:App\Models\Wallet,owner_id,' . $id . ',id'
            ],
            'amount' => [
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
            'owner_id.unique' => __('This user already has an active wallet.'),

            'amount' => __('The amount field is required.'),
            'amount.decimal' => __('The value must have up to 8 digits with 2 decimals (10 digits).'),
        ];
    }
}
