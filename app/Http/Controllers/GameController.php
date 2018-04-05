<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $guess = $request->input('guess');
        $userGame = Auth::user()->userGame($request->input('gameId'));
        if ($userGame->turn !== Auth::user()->id) {
            return response()->json(['error' => 'Not your turn.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        if (!$userGame) {
            return response()->json(['error' => 'Invalid game id.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        $opponent = \App\GameUser::findOpponentByUserGame($userGame);
        if (!$opponent) {
            return response()->json(['error' => 'Invalid opponent.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        $result = 0;
        if ($opponent->word === $guess) {
            // win
            $result = strlen($opponent->word);
        }
        else
        {
            $result = self::compareWord($opponent->word, $guess);
        }
        $this->saveWord($userGame, $guess, $result);
        $this->updateGameTurn($userGame, $userGame->opponent_id);

        return ['message' => "Saved move.", 'result' => $result, 'guess' => $guess];
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

    private function saveWord(\App\UserGames $userGame, $word, $result)
    {
        DB::table('user_moves')->insert([
            'game_id' => $userGame->game_id, 'user_id' => $userGame->player_id,
            'guess' => $word, 'result' => $result
        ]);
    }

    private function updateGameTurn(\App\UserGames $userGame, $playerId)
    {
        DB::table('games')->where('id', $userGame->game_id)->update(['turn' => $playerId]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function show(\App\Game $game)
    {
        $userGame = Auth::user()->userGame($game->id);
        return view('games.show', ['playerGame' => $userGame, 'moves' => []]);
    }

    public function moves(Request $request, $id)
    {
        file_put_contents('/tmp/rob.log', var_export($id, true), FILE_APPEND);
        
        $userGame = Auth::user()->userGame($id);
        if (!$userGame) {
            return response()->json(['error' => 'Invalid game for moves'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        $game = \App\Game::where('id', $id)->get()->first();
        $moves = $game->playerMoves(Auth::user()->id)->get();
        return $moves;
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
