<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function friends(Request $request)
    {
        return Auth::user()->friends();
    }

    public function info(Request $request)
    {
        return Auth::user();
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
}
