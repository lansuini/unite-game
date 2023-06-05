<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Rule;

class MultipleIPs implements Rule
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
        $values = explode(',', $value);
        foreach ($values as $ip) {
            $validator = Validator::make(['ip' => $ip], [
                'ip' => 'required|ip'
            ]);

            if ($validator->fails()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute format must be [ip1,ip2,ip3,..] no space';
    }
}
