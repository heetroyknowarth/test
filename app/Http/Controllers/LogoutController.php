<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /*
    Logout function
    */
     public function logout(Request $request)
        {
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Successfully logged out']);
        }
}
