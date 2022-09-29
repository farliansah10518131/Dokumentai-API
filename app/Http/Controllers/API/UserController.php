<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function register(Request $request)
    {
        try {

            $request->validate([
                'name' => ['required', 'string', 'max: 255'],
                'email' => ['required', 'email', 'string', 'max: 255', 'unique:users'],
                'username' => ['required', 'string', 'max: 255'],
                'phone' => ['required', 'string', 'max: 13'],
                'password' => ['required', 'string', new Password],
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $users = User::where('email', $request->email)->first();
            $tokenResult = createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'Access Token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $users,
            ], 'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            $credensials = request(['email', 'password']);
            if (!Auth::attempt($credensials)) {
                ResponseFormatter::error([
                    'Message' => 'Unauthorized',
                ], 'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credensials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Authenticatited', 500);
        } catch (Exception $error) {
            ResponseFormatter::error(
                [
                    'message' => 'something',
                    'error' => $error,
                ],
                'Authentication Failed',
                500
            );
        }
    }
}
