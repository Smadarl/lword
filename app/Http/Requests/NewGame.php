<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DictWord;

class NewGame extends FormRequest
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
            'opponent_id' => 'required|integer|min:1',
            'max_length' => 'required|integer|min:6|max:12',
            'max_recurrance' => 'required|integer|min:1|max:4',
            'chooseWord.type' => 'required|min:6',
            'chooseWord.word' => [
                'required_if:chooseWord.type,choose',
                'min:6',
                'max_chars_field:max_length',
                'max_dup_chars_field:max_recurrance',
                new DictWord
            ]
        ];
    }

    // public function values()
    // {
    //     return [
    //         'chooseWord.type' => [
    //             'random' => 'random',
    //             'choose' => 'not random'
    //         ]
    //     ];
    // }

    // public function attribuates() {
    //     return [
    //         'chooseWord.word' => 'word',
    //         'chooseWord.type' => 'type'
    //     ];
    // }

    public function messages() {
        return [
            'chooseWord.word.required_if' => 'Either choose Random or enter a word',
            'chooseWord.word.min' => 'The word must be at least 6 characters'
        ];
    }
}
