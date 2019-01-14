<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DictWord implements Rule
{
    private $wordFile;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->wordFile = env('WORD_FILE');
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
        if (!strlen(trim($value))) {
            return true;
        }
        $test = `grep -e '^$value\$' {$this->wordFile}`;
        return trim($test) === $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'That word is not a cromulent word.';
    }
}
