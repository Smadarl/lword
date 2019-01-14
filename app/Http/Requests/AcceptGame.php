<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DictWord;
use App\GameUser;
use App\Game;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;

class AcceptGame extends FormRequest
{
    private static $game;

    public function __construct(ValidationFactory $vFactory) {
        $vFactory->extend(
            'checkLengths',
            function ($attr, $val, $params) {
                file_put_contents('/tmp/object.log', var_export($params, true));
            },
            'Failed lengths'
        );
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (self::$game->status != 'pending') {
            return false;
        }
        if (self::$game->started_by == Auth::id()) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $game_id = $this->route('id');
        $userGame = GameUser::find(['game_id' => $game_id, 'user_id' => Auth::id()]);
        if (!$userGame) {
            return false;
        }
        self::$game = Game::find($game_id);
        return [
            'chooseWord.type' => 'required|min:6',
            'chooseWord.word' => [
                'required_if:chooseWord.type,choose',
                'min:6',
                'max:' . self::$game->max_length,
                'max_dup_chars:' . self::$game->max_recurrance,
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
            'chooseWord.word.min' => 'The word must be at least :min characters',
            'chooseWord.word.max' => 'The word must be no more than :max characters',
        ];
    }
}
