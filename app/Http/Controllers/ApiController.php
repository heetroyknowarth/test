<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{


        public function register(Request $request)
        {
            $validate = $this->validateRequest();

            if ($validate){
              echo 'validated';
                $user = new User([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password)
                ]);
                $user->save();
            }

            return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
        }



        public function login(Request $request)
        {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
                'remember_me' => 'boolean'
            ]);
            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
            $user = $request->user();

            $tokenResult = $user->createToken('Personal Access Token');

            $token = $tokenResult->token;

            if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
            return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);
        }




        public function logout(Request $request)
        {
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Successfully logged out']);
        }




        public function user(Request $request)
        {
            return response()->json($request->user());
        }



         public function updateProfile(Request $request){
            if ($request->has('avatar')){

                $avatar = $request->file('avatar');
                $avatar->store('lol');
                $file = $avatar->getClientOriginalName();
            }


        $data = [
            'avatar' => $file
        ];
        Auth::user()->update($data);
        return response()->json($request->user());
     }



        public function validateRequest(){
            return tap(
                \request()->validate([
                    'name' => 'required|string',
                    'email' => 'required|string|email|unique:users',
                    'password' => 'required|string',
                    'remember_me' => 'boolean'
                ]),
                function () {
                    if (\request()->hasFile('avatar')) {
                        \request()->validate(['avatar'=>'file|image|max:5000',]);
                    }
                });
        }



}
