<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotFoundException;
use App\Http\Mappers\Mapper;
use App\Models\User;
use App\Traits\HttpsResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    use HttpsResponses;

    public function getUsers(): JsonResponse
    {
        $admin = Auth::id();
        $users = User::query()
            ->whereIn("role_id", [2, 3])
            ->get();
        Log::channel("user")->info("[AdminController::getUsers] Admin with id: $admin->id accessed this function");
        return $this->success(Mapper::userMapper($users));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $admin = Auth::id();
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $id,
            'phone_number' => 'string',
        ]);

        try {
            $user = User::whereIn('role_id', [2, 3])->find($id);
            if (!$user) {
                throw new UserNotFoundException();
            }
            $user->update($request->only(['name', 'email', 'phone_number']));
            Log::channel("user")->info("[AdminController::update] Admin with id: $admin accessed this function, updated user data with id $user->id", ['name' => $user->name, 'email' => $user->email, 'phone' => $user->phone_number]);
            return $this->success(Mapper::userMapper($user), "User update successfully");
        } catch (Exception $e) {
            Log::channel("user")->error("[AdminController::update] Admin with id: $admin accessed this function, returned with error.", ["message" => $e->getMessage(), "code" => $e->getCode()]);
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function setUserRole(Request $request, $id): JsonResponse
    {
        $admin = Auth::id();
        try {
            $request->validate(['role_id' => "numeric"]);
            $user = User::find($id);
            if (!$user) {
                throw new UserNotFoundException();
            }
            $user->update($request->only(['role_id']));
            Log::channel("user")->info("[AdminController::setUserRole] Admin with id: $admin accessed this function successfully, set target for user with id $user->id to $user->role_id");
            return $this->success(Mapper::userMapper($user), "role set successfully");
        } catch (Exception $e) {
            Log::channel("user")->error("[AdminController::setUserRole] Admin with id: $admin accessed this function, returned with error error.", ["message" => $e->getMessage(), "code" => $e->getCode()]);
            return $this->error($e->getMessage());
        }
    }

    public function delete(int $id): JsonResponse
    {
        $admin = Auth::id();
        try {
            $user = User::where('role_id', 2)->find($id);
            if (!$user) {
                throw new UserNotFoundException();
            }
            $user->delete();
            Log::channel("user")->info("[AdminController::delete] Admin with id: $admin accessed this function successfully, deleted user with id $user->id");
            return response()->json(['message' => 'User deleted successfully']);
        } catch (Exception $e) {
            Log::channel("user")->error("[AdminController::delete] Admin with id: $admin accessed this function, returned with error.", ["message" => $e->getMessage(), "code" => $e->getCode()]);
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getUserTarget(int $id): JsonResponse
    {
        $admin = Auth::id();
        try {
            $user = User::query()->whereIn("role_id", [2, 3])->find($id);
            if (!$user) {
                throw new UserNotFoundException();
            }
            $target = $user->target;
            $targetStatus = "uncompleted";
            $leadHistory = LeadController::getUsersDoneLeads($user);
            if ($leadHistory == null) {
                $currentValue = 0;
            } else {
                $currentValue = $leadHistory->sum('value');
            }
            if ($currentValue >= $target) {
                $targetStatus = "completed";
            }
            if ($user->current != $currentValue) {
                Log::channel("user")->emergency("[AdminController::getUserTarget] Admin with id: $admin for user id:$user->id calculated target and actual target aren't the same!", ["calculated" => $user->current, "actual" => $currentValue]);
            }
            Log::channel("user")->info("[AdminController::getUserTarget] Admin with id: $admin accessed this function successfully, get user's with id: $user->id target ");
            return $this->success(['user' => Mapper::userMapper($user),
                'target' => $target, 'current_done' => $currentValue,
                "targetStatus" => $targetStatus]);
        } catch (Exception $e) {
            Log::channel("user")->error("[AdminController::getUserTarget] Admin with id: $admin accessed this function, returned with error.", ["message" => $e->getMessage(), "code" => $e->getCode()]);
            return $this->error($e->getMessage());
        }
    }

    public function getUsersById(int $id): JsonResponse
    {
        $admin = Auth::id();
        try {
            $user = User::find($id);
            if (!$user) {
                throw new UserNotFoundException();
            }
            Log::channel("user")->info("[AdminController::getUsersById] Admin with id: $admin accessed this function successfully, get user's with id: $user->id");
            return $this->success(Mapper::userMapper($user));
        } catch (Exception $e) {
            Log::channel("user")->error("[AdminController::getUsersById] Admin with id: $admin accessed this function, returned with error.", ["message" => $e->getMessage(), "code" => $e->getCode()]);
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function setTarget(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'target' => 'required',
        ]);
        $admin = Auth::id();

        try {
            $user = User::query()
                ->find($id);
            if (!$user) {
                throw new UserNotFoundException();
            }
            $user->update([
                'target' => $request->input('target'),
            ]);
            Log::channel("user")->info("[AdminController::setTarget] Admin with id: $admin accessed this function successfully, set user's with id: $user->id target to $user->target ");

            return response()->json(['message' => 'Target set successfully']);
        } catch (Exception $e) {
            Log::channel("user")->error("[AdminController::setTarget] Admin with id: $admin accessed this function, returned with error.", ["message" => $e->getMessage(), "code" => $e->getCode()]);
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
// TODO test if it's SCUDUALAR command
    public function reset(): JsonResponse
    {
        $admin = Auth::id();
        User::query()->update([
            'target' => 0,
            'current' => 0,
            'call_count' => 0
        ]);
        if ($admin){
            Log::channel("user")->info("[AdminController::reset] Admin with id: $admin accessed this function successfully, resets all users ");
        }
        else{
            Log::channel("user")->info("[AdminController::reset]  This function accessed using scheduler successfully, resets all users ");

        }
        return $this->success([], "All users data reset successfully");
    }


    // STATIC FUNCTIONS.

    public static function getAdminProfile(): User
    {
        Log::channel("user")->info("[AdminController::getAdminProfile]  function called this static function successfully");
        return Auth::user();
    }

    //STOPPED HERE

}
