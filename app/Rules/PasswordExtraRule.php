<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordExtraRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $isValid = true;
        $email = !is_null(request()->user()) ? request()->user()->email : request()->email;

        if (is_null($email)) {
            return false;
        }

        for ($i = 0; $i < strlen($value); $i++) {
            $a = substr($value, $i, 3);

            if (strlen($a) < 3) {
                break;
            }

            if (str_contains($email, $a)) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Do not contain words that is parts of email (3 consecutive characters).';
    }
}
