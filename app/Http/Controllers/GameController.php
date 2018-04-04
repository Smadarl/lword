<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gameList = Auth::user()->game_list();
        return view('games.index', ['gameList' => $gameList]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'gameId' => 'required',
            'guess' => 'required|min:4|max:20',
        ]);
        $playerGame = Auth::user()->game($request->input('gameId'));
        if (!$playerGame) {
            return response('', 401)->json(['error' => 'Invalid game id']);
        }
        $opponent = \App\GameUser::findOpponentByUserGame($playerGame);
        if (!$opponent) {
            return response('', 422)->json(['error' => 'Invalid opponent']);
        }
        $result = 0;
        if ($opponent->word === $request->input('guess')) {
            // win
            $result = strlen($opponent->word);
        }
        else
        {
            $result = self::compareWord($opponent->word, $request->input('guess'));
        }

        return ['message' => "Successful ($result)", 'result' => $result];
    }

    static public function compareWord($gameWord, $guess)
    {
        $gameLetters = str_split($gameWord);
        $eachLetter = str_split($guess);
        $count = 0;
        foreach($eachLetter as $letter)
        {
            if (($idx = array_search($letter, $gameLetters, true)) !== false) {
                $count++;
                unset($gameLetters[$idx]);
            }
        }
        return $count;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function show(\App\Game $game)
    {
        $playerGame = Auth::user()->game($game->id);
        file_put_contents("/tmp/rob.log", print_r($playerGame, true), FILE_APPEND);
        $moves = $game->playerMoves(Auth::user()->id)->get();
        return view('games.show', ['playerGame' => $playerGame, 'moves' => $moves]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function destroy(Game $game)
    {
        //
    }
}
