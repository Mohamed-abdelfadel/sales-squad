<?php

namespace App\Http\Controllers;

use App\Http\Mappers\Mapper;
use App\Traits\HttpsResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    use HttpsResponses;

    public function getUserProfile(): JsonResponse
    {
        $user = Auth::user();
        Log::channel('user')->info("[UserController::getUserProfile] User ID: {$user->id} accessed this function.",  ['user' => $user]);
        return $this->success(Mapper::userMapper($user));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'phone_number' => 'string',
        ]);

        $user->update($request->only(['name', 'email', 'phone_number']));
        Log::channel('user')->info("[UserController::updateProfile] User ID: {$user->id} updated profile.", ['user' => $user, 'request' => $request->all()]);
        return $this->success(Mapper::userMapper($user), "User updated successfully");
    }
}
