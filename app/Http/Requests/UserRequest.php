<?php

namespace App\Http\Requests;

use App\Enums\User;
use App\Rules\PasswordExtraRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'name' => ['required'],
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users'),
                'max:100',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                // 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                'required_with:password_confirmation',
                'same:password_confirmation',
                new PasswordExtraRule(),
            ],
            'password_confirmation' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * @param null $keys
     * @return array
     */
    public function all($keys = null)
    {
        $params = parent::all();
        $params['email'] = Str::lower($params['email']);

        return $params;
    }
}
