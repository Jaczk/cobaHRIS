<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {

            //Validate Request
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            //Find User by email
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error(
                    'Unauthorized',
                    401
                );
            }
            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new Exception('Invalid Password');
            }

            //Generate Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            //Return Response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Login Success');

        } catch (Exception $error) {
            return ResponseFormatter::error('Authentication Failed', 401);
        }
    }

    public function register(Request $request)
    {
        try {
            //Validate Request
            $request->validate([
                'name' => ['required','string','max:255'],
                'email' => ['required','string','email','unique:users','max:255'],
                'password' => ['required','string', new Password],
            ]);

            //Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            //Generate Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            //Return Response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Register Success');
        } catch (Exception $error) {
            //Return Error Response
            return ResponseFormatter::error($error->getMessage(), 401);
        }
    }

    public function logout(Request $request)
    {
        //TODO: Revoke Token
        $token = $request->user()->currentAccessToken()->delete();
        
        //TODO: Return Response
        return ResponseFormatter::success($token, 'Logout Success');
    }

    public function fetch(Request $request)
    {
        //GET User
        $user = $request->user();
        //Return Response
        return ResponseFormatter::success($user, 'Data Profile User Berhasil Diambil');
    }
}
