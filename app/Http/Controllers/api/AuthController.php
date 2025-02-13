<?php

namespace App\Http\Controllers\api;

use App\Events\NewUserRegistered;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

class AuthController extends Controller
{
    public function register(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,customer,vendor',
            'store_name' => 'required_if:role,vendor|string|max:255',
            'address' => 'required_if:role, vendor|string|max: 500',
            'phone_number' => 'required_if:role,vendor|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        
        if($request->role === 'vendor'){
            Vendor::create([
                'user_id' => $user->id,
                'store_name' => $request->store_name,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
            ]);
        }
        Log::info('User registered: ' . $user->email);
        // Mail::to($user->email)->send(new WelcomeEmail($user));
        event(new NewUserRegistered($user));
        Log::info('NewUserRegistered event dispatched.');
        return response()->json([
            'message' => ucfirst($request->role). ' registered successfully',
            'user' => $user,
        ], 201);

    }

    public function login(Request $request){
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json(['message'=> 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];
        if($user->role === 'vendor'){
            $vendor = Vendor::where('user_id', $user->id)->first();
            $response['vendor'] = $vendor;
        }
        return response()->json($response, 200);
    }   

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
