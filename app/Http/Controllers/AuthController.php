<?php

namespace App\Http\Controllers;

use App\Http\Mappers\Mapper;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpsResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use HttpsResponses;
    public static string $welcome = "Welcome to Sales Squad";

    public function register(StoreUserRequest $request): JsonResponse
    {
        $admin = Auth::id();
        $request->validated($request->all());
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'password' => Hash::make($request->input('password')),
            'remember_token' => Str::random(10)
        ]);
        Log::channel("user")->info("[AuthController::register] Admin with id: $admin accessed this function successfully, registered user with id $user->id");
        return $this->success([
            'user' => Mapper::userMapper($user),
            'token' => $user->createToken('API token of ' . $user->name)->plainTextToken
        ], self::$welcome);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $request->validated($request->all());
        if (!Auth::attempt($request->only("email", "password"))) {
            Log::channel("user")->error("[AuthController::login] User with email $request->email tried to login, returned with error.");
            return $this->error('Credentials does not match ', 401);
        }
        $user = User::query()
            ->where("email", $request->input('email'))
            ->first();
        Log::channel("user")->info("[AuthController::login] user $user->name with id: $user->id accessed this function successfully");
        return $this->success(
            ['user' => Mapper::userMapper($user),
                'token' => $user->createToken('API token of' . $user->name)->plainTextToken
            ], self::$welcome);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();
            Log::channel("user")->info("[AuthController::logout] user " . $user->name . " with id: " . $user->id . " accessed this function successfully");
        }
        return $this->success([], 'Logout successful');
    }
    // STOPPED HERE
}
