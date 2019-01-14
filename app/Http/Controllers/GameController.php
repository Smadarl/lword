<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Database\Seeds\GamesTableSeeder;
use App\Http\Requests\NewGame;
use App\Http\Requests\AcceptGame;

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
        $gameList = Auth::user()->game_list('started');
        $pending = Auth::user()->game_list('pendingForMe');
        $waiting = Auth::user()->game_list('startedByMe');
        return view('games.index', ['gameList' => $gameList, 'pending' => $pending, 'waiting' => $waiting]);
    }

    public function pending(Request $request, $id) {
        $userGame = Auth::user()->userGame($id);
        if (!$userGame) {
            return response()->json(['error' => 'Invalid game id.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        if ($userGame->status != 'pending') {
            return response()->json(['error' => 'Invalid game state.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        return view('games.pending', ['game' => $userGame]);
    }

    public function accept(AcceptGame $request, $id) {
        $userGame = Auth::user()->userGame($id);
        if (!$userGame) {
            return response()->json(['error' => 'Invalid game id.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        if ($userGame->started_by == Auth::id()) {
            return response()->json(['error' => 'Invalid accept request.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        if ($userGame->status != 'pending') {
            return response()->json(['error' => 'Invalid game state.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        if ($request->input('origination') == 'random') {
            $word = $this->randomWord($userGame->max_length, $userGame->max_recurrance);
        } else {
            $word = $request->input('chooseWord.word');
        }
        $gameUser = \App\GameUser::findPlayerByUserGame($userGame);
        $gameUser->word = $word;
        $gameUser->save();
        $game = \App\Game::where('id', $userGame->game_id)->get()->first();
        $game->status = 'started';
        $game->started_at = date('Y-m-d H:i:s');
        $game->save();
        return ['game' => $game];
    }

    public function cancel(Request $request, $id) {
        $userGame = Auth::user()->userGame($id);
        if (!$userGame) {
            return response()->json(['error' => 'Invalid game id.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        if ($userGame->status != 'pending') {
            return response()->json(['error' => 'Invalid game state.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        $gameUser = \App\GameUser::findPlayerByUserGame($userGame);
        $gameOpp = \App\GameUser::findOpponentByUserGame($userGame);
        $game = \App\Game::where('id', $userGame->game_id)->get()->first();
        $gameUser->delete();
        $gameOpp->delete();
        $game->delete();
        return ['message' => 'Game canceled'];
    }

    public function new() {
        return view('games.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(NewGame $request)
    {
        $gameId = DB::table('games')->insertGetId([
            'max_length' => $request->input('max_length'),
            'max_recurrance' => $request->input('max_recurrance'),
            'started_by' => Auth::id(),
            'created_at' => date('Y-m-d H:i:s'),
            'turn' => array(Auth::id(), $request->input('opponent_id'))[rand(0,1)],
            'status' => 'pending',
        ]);
        if ($request->input('chooseWord.type') == 'choose') {
            $word = $request->input('chooseWord.word');
        } else {
            $word = $this->randomWord($request->input('max_length'), $request->input('max_recurrance'));
        }
        DB::table('game_user')->insert([
            'game_id' => $gameId,
            'user_id' => Auth::id(),
            'word' => $word
        ]);
        DB::table('game_user')->insert([
            'game_id' => $gameId,
            'user_id' => $request->input('opponent_id'),
            'word' => 'none'
        ]);
        return ['game' => Auth::user()->userGame($gameId), 'word' => $word];
    }

    private function randomWord($maxlen, $maxrecur) {
        $wf = env('WORD_FILE');
        $words = `shuf -n 500 $wf`;
        foreach(explode("\n", $words) as $word) {
            if ((strlen($word) > $maxlen) || (strlen($word) < env('MIN_WORD_LENGTH')))
                continue;
            $chars = count_chars($word, 1);
            if (!is_array($chars) || (count($chars) === 0))
                continue;
            if (max($chars) > $maxrecur)
                continue;
            echo $word; exit();
        }
        return $word;
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
        if (!$userGame) {
            return response()->json(['error' => 'Invalid game id.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        if ($userGame->turn !== Auth::user()->id) {
            return response()->json(['error' => 'Not your turn.'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
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
        if (substr($request->path(), -1 * strlen('update')) == 'update') {
            return [
                'game' => $this->getGameArray($userGame)
            ];
        }
        $game = \App\Game::where('id', $id)->get()->first();
        $moves = $game->playerMoves(Auth::user()->id)->get();
        return [
            'game' => $this->getGameArray($userGame),
            'letters' => $userGame->letters,
            'moves' => $moves,
            'user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'friends' => [],
            ],
        ];
    }

    private function getGameArray(\App\UserGames $userGame)
    {
        return [
                    'id' => $userGame->game_id,
                    'opponentId' => $userGame->opponent_id,
                    'opponent' => $userGame->opponent_name,
                    'maxSize' => $userGame->max_length,
                    'maxRecur' => $userGame->max_recurrance,
                    'turn' => $userGame->turn
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

    public function test()
    {
        return view('games.test');
    }

    public function testPost(NewGame $request) {
        $validated = $request->validated();
        return ['Status' => 200, 'message' => "Successfully submitted data"];
    }
}
