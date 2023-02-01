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
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['nullable', 'string', 'max:255'],
                'password' => ['required', 'string', new Password],
                'pin' => ['nullable', 'string'],
            ]);

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'pin' => $request->pin,
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,

            ], 'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error

            ], 'Authentication Failed', 500);
        }
    }


    public function redirectToGoogle()
    {
       
    }

    public function handleGoogleCallback()
    {
        try{ $user = Socialite::driver('google')->user();
        }catch(Exception $e){
            return route('googleLogin');
        }
       
        $existingUser = User::where('email', $user->email)->first();
        if ($existingUser) {
            // Login the existing user
            Auth::login($existingUser);

            $tokenResult = $user->createToken('authToken')->plainTextToken;

             return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

            
        } else {
            // Create a new user
            $newUser = new User;
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->save();
            Auth::login($newUser);
        }

    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);
            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 500);
        }
    }

    public function fetch(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::with(['categories', 'products'])->find($id);

        if ($user) {
            return ResponseFormatter::success(
                $user,
                'Data User berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data user tidak ada ',
                404
            );
        }
        // return ResponseFormatter::success($user, 'Data profile user berhasil diambil');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->all();

        $user =  User::find(Auth::id());
        $user->update($data);

        return ResponseFormatter::success($user, 'Profile updated');
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Token Revoked');
    }


    
}
