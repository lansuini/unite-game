<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MultipleGames implements Rule
{
    protected $v;

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
        $games = config('gm.game_alias');
        $values = explode(',', $value);
        foreach ($values as $gameId) {
            if (!isset($games[$gameId])) {
                $this->v = $gameId;
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
        return 'The :attribute not defined game [' . $this->v . ']';
    }
}
