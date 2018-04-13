<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
    public function create(Request $request)
    {
        $this->validate(request(), [
            'opponentid' => 'required|integer',
            'maxrecur' => 'required|integer|min:1|max:4',
            'maxlength' => 'required|integer|min:6|max:12',
            'origination' => 'required'
        ]);
        if ($request->input('origination') == 'choose') {
            $this->validate(request(), [
                'myword' => 'required|min:6|max:' . $request->input('maxlength')
            ]);
        }
        $gameId = DB::table('games')->insertGetId([
            'max_length' => $request->input('maxlength'),
            'max_recurrance' => $request->input('maxrecur'),
            'started_by' => Auth::id(),
            'started_at' => date('Y-m-d H:i:s'),
            'turn' => array(Auth::id(), $request->input('opponentid'))[rand(0,1)],
            'status' => 'pending',
        ]);
        if ($request->input('origination') == 'choose') {
            $word = $request->input('myword');
        } else {
            $word = 'internationalization'; // TODO: randomize this
        }
        DB::table('game_user')->insert([
            'game_id' => $gameId,
            'user_id' => Auth::id(),
            'word' => $word
        ]);
        DB::table('game_user')->insert([
            'game_id' => $gameId,
            'user_id' => $request->input('opponentid'),
            'word' => 'none'
        ]);
        return Auth::user()->userGame($gameId);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $this->validate(request(), [
            'guess' => 'required|min:4|max:20',
        ]);
        $guess = strtolower($request->input('guess'));
        $error = $this->checkValidWord($guess);
        if ($error) {
            return response()->json(['error' => $error])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        $userGame = Auth::user()->userGame($id);
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

    public function gameData(Request $request, $id)
    {
        $userGame = Auth::user()->userGame($id);
        $game = \App\Game::where('id', $id)->get()->first();
        $moves = $game->playerMoves(Auth::user()->id)->get();
        return [
            'game' => [
                'id' => $userGame->game_id,
                'opponentId' => $userGame->opponent_id,
                'opponent' => $userGame->opponent_name,
                'maxSize' => $userGame->max_length,
                'maxRecur' => $userGame->max_recurrance,
                'turn' => $userGame->turn
            ],
            'letters' => $userGame->letters,
            'moves' => $moves,
            'user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'friends' => [],
            ],
        ];
    }

    public function getLetters($gameId)
    {
        $playerGame = Auth::user()->userGame($gameId);
        if (!$playerGame)
        {
            return [];
        }
        return $playerGame->letters;
    }

    public function saveLetters(Request $request, $gameId)
    {
        $userId = Auth::id();
        DB::table('game_user')
            ->where('game_id', $gameId)
            ->where('user_id', $userId)
            ->update([
            'letters' => json_encode($request->input('letters'))
        ]);
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

    private function checkValidWord($word)
    {
        $cmd = "grep -w $word " . env('WORD_FILE');
        exec($cmd, $output, $return);
        if ($return)
        {
            return "Invalid word.  Please use a dictionary word.";
        }
        return false;
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
        $userGame = Auth::user()->userGame($id);
        if (!$userGame) {
            return response()->json(['error' => 'Invalid game for moves'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        $game = \App\Game::where('id', $id)->get()->first();
        $moves = $game->playerMoves(Auth::user()->id)->get();
        return $moves;
    }

    public function info(Request $request, $id)
    {
        $userGame = Auth::user()->userGame($id);
        return $userGame;
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
