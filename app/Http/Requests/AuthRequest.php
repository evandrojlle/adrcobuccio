<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRequest extends FormRequest
{
    const PASS_MIN = 8;

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
        $id = $this->request->get('user_id');
        return [
            'email' => [
                'required',
                'string',
                'regex:/^([0-9a-zA-Z]+([_.-]?[0-9a-zA-Z]+)*@[0-9a-zA-Z]+[0-9,a-z,A-Z,.,-]*(.){1}[a-zA-Z]{2,4})+$/'
            ],
            'password' => [
                'required',
                'string',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$!%*?&\.\(\)_\-\+=\[\]\{\}\|\:;\<\>\"\,\^~])[A-Za-z\d@#$!%*?&\.\(\)_\-\+=\[\]\{\}\|\:;\<\>\"\,\^~]{' . self::PASS_MIN . ',}$/'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('The email field is required.'),
            'email.regex' => __('The email field format is invalid.'),
            'email.email' => __('The email field must be a valid email address.'),
            'password.required' => __('The password field is required.'),
            'password.min' => __('The password must be at least :min characters.', ['min' => self::PASS_MIN]),
            'password.regex' => __('Password format is invalid. Must contain at least 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character.'),
        ];
    }
}
