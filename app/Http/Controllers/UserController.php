<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function friends(Request $request)
    {
        return Auth::user()->friends();
    }
}
