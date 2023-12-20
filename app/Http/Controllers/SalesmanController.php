<?php

namespace App\Http\Controllers;

use App\Http\Mappers\Mapper;
use App\Models\User;
use App\Traits\HttpsResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SalesmanController extends Controller
{
    use HttpsResponses;

    public function getUserLeads(): JsonResponse
    {
        return $this->success(["salesman" => Mapper::userLeadsMapper(Auth::user())]);
    }

    public function getUserNewLeads(): JsonResponse
    {
        return $this->success(["salesman" => Mapper::userNewLeadsMapper(Auth::user())]);
    }

    public function getUserDoneLeads()
    {
        return $this->success(["salesman" => Mapper::userDoneLeadsMapper(Auth::user())]);
    }

    public function getTarget(): JsonResponse
    {
        $user = Auth::user();
        $target = $user->target;
        $targetStatus = "uncompleted";
        $leadHistory = LeadController::getUsersDoneLeads($user);
        $currentValue = $leadHistory->sum('value');
        if ($currentValue >= $target) {
            $targetStatus = "completed";
        }
        return $this->success(['user' => Mapper::userMapper($user),
            'target' => $target, 'current_done' => $currentValue,
            "targetStatus" => $targetStatus]);
    }

    public function getUserLostLeads(): JsonResponse
    {
        return $this->success(["salesman" => Mapper::userLostLeadsMapper(Auth::user())]);
    }

    // STATIC FUNCTIONS.
    public static function getSalesmen()
    {
        return User::whereIn("role_id", [2,3])->get();
    }

    public static function leadAdded(User $user, $value): void
    {
        $newValue = $user->current + $value;
        $user->update([
            'current' => $newValue
        ]);
        $user->increment('call_count');
        $user->save();
    }

    public static function getSalesmenProfile(): User
    {
        return Auth::user();
    }

    public static function getSalesman($id)
    {
        return User::where("role_id", 2)->find($id);
    }
}
