<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function friends()
    {
        return view('user.friends');
    }

    public function apiFriends() {
        return Auth::user()->friends();
    }

    public function inviteFriend(Request $request) {

    }

    public function info(Request $request)
    {
        return Auth::user();
    }

    public function requests(Request $request)
    {
        $friendRequests = DB::table('friends')->join('users', function(JoinClause $join) {
            $join->on('friends.user_id', '=', 'users.id');
        })
        ->where('friends.friend_id', Auth::id())
        ->where('status', 'requested')
        ->get();
        return $friendRequests;
    }

    public function addFriend(Request $request)
    {
        $friend = User::where('email', $request->email)->get()->first();
        if (is_object($friend))
        {
            $inFriendList = DB::table('friends')->where('user_id', Auth::id())->where('friend_id', $friend->id)->count();
            if (!$inFriendList)
            {
                DB::table('friends')->insert(['user_id' => Auth::id(), 'friend_id' => $friend->id]);
            }
        }
        return ['message' => 'Friend request sent'];
    }

    public function friendRespond(Request $request)
    {
        DB::table('friends')->where('user_id', $request->input('friend_id'))->where('friend_id', Auth::id())->update(['status' => $request->input('response')]);
        if ($request->input('response') === 'confirmed') {
            DB::table('friends')->insert(['user_id' => Auth::id(), 'friend_id' => $request->input('friend_id'), 'status' => 'confirmed']);
        }
        return ['message' => 'Friend request processed.'];
    }

    public function changepw(Request $request)
    {
        if (!(Hash::check($request->get('curPW'), Auth::user()->password))) {
            return response()->json(['error' => 'Wrong password.'])->setStatusCode(Response::HTTP_UNAUTHORIZED, Response::$statusTexts[Response::HTTP_UNAUTHORIZED]);
        }

        $validatedData = $request->validate([
            'curPW' => 'required',
            'newPassword' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->password = bcrypt($request->get('newPassword'));
        $user->save();

        return ['message' => 'New password saved'];
    }

    public function friendGames(Request $request, $friendId)
    {
        $friend = DB::table('friends')->where('user_id', Auth::id())->where('friend_id', $friendId)->get()->first();
        if (!$friend)
        {
            return response()->json(['error' => 'Invalid friend'])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        $list = Auth::user()->game_list(null, $friendId);
        return ['game_list' => $list];
    }
}
