<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    const MIN_NAME = 5;
    
    const MAX_NAME = 200;

    const MAX_EMAIL = 200;

    const MIN_PASSWORD = 8;
    
    const MAX_PASSWORD = 25;

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
            'name' => [
                'required',
                'string',
                'min:' . self::MIN_NAME,
                'max:' . self::MAX_NAME
            ],
            'email' => [
                'required',
                'string',
                'max:' . self::MAX_EMAIL,
                'regex:/^([0-9a-zA-Z]+([_.-]?[0-9a-zA-Z]+)*@[0-9a-zA-Z]+[0-9,a-z,A-Z,.,-]*(.){1}[a-zA-Z]{2,4})+$/',
                'unique:App\Models\User,email,' . $id . ',id'
            ],
            'password' => [
                'required',
                'string',
                'min:' . self::MIN_PASSWORD,
                'max:' . self::MAX_PASSWORD,
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$!%*?&\.\(\)_\-\+=\[\]\{\}\|\:;\<\>\"\,\^~])[A-Za-z\d@#$!%*?&\.\(\)_\-\+=\[\]\{\}\|\:;\<\>\"\,\^~]{' . self::MIN_PASSWORD . ',}$/'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('The name field is required.'),
            'name.min' => __('The name must be at least :min characters.', ['min' => self::MIN_NAME]),
            'name.max' => __('The name may not be greater than :max characters.', ['max' => self::MAX_NAME]),

            'email.required' => __('The email field is required.'),
            'email.regex' => __('The email field format is invalid.'),
            'email.email' => __('The email field must be a valid email address.'),
            'email.max' => __('The name may not be greater than :max characters.', ['max' => self::MAX_EMAIL]),
            'email.unique' => __('There is already a user registered with this email. Log in to continue.'),

            'password.required' => __('The password field is required.'),
            'password.min' => __('The password must be at least :min characters.', ['min' => self::MIN_PASSWORD]),
            'password.max' => __('The name may not be greater than :max characters.', ['min' => self::MAX_PASSWORD]),
            'password.regex' => __('Password format is invalid. Must contain at least 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character.'),
        ];
    }
}
