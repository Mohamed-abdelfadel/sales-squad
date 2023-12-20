<?php

namespace App\Http\Controllers;

use App\Exceptions\TeamNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Http\Mappers\Mapper;
use App\Models\Team;
use App\Traits\HttpsResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class TeamLeaderController extends Controller
{
    use HttpsResponses;

    public function assignSalesmanAutomatically(): JsonResponse
    {
        $teams = Team::all();
        $salesmen = SalesmanController::getSalesmen();

        foreach ($teams as $team) {
            $salesman = $salesmen->pop();
            $salesman->update(['team_id' => $team->id]);
        }
        return $this->success([], "System has separated " . count($salesmen) . " salesmen into " . count($teams) . " teams");
    }

    public function getAll(): JsonResponse
    {
        $teams = Team::all();
        return $this->success(Mapper::teamMapper($teams));
    }

    public function create(Request $request): JsonResponse
    {
        $request->validate([
            "name" => 'string'
        ]);
        Team::create([
            'name' => $request->input('name'),
        ]);
        return $this->success([], "Team created successfully");
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $team_id = $request->input('id');
            $team = Team::find($team_id);

            if (!$team) {
                throw new TeamNotFoundException();
            }
            $request->validate(['name' => 'string|max:255',]);
            $team->update(['name' => $request->input('name'),]);
            return $this->success([], "Team updated successfully");
        } catch (TeamNotFoundException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws TeamNotFoundException
     */
    public function delete(Request $request): JsonResponse
    {
        try {
            $team = Team::query()->find($request->input('id'));
            if (!$team) {
                throw new TeamNotFoundException();
            }
            $team->delete();
            return $this->success([], "Team deleted successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function assignSalesman(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|numeric',
                'team_id' => 'required|numeric'
            ]);
            $salesman = SalesmanController::getSalesman($request->user_id);
            $team = Team::query()->find($request->input('team_id'));
            if (!$salesman) {
                throw new UserNotFoundException();
            }
            if (!$team) {
                throw new TeamNotFoundException();
            }
            $salesman->update(['team_id' => $team->id]);
            return $this->success(Mapper::userMapper($salesman), 'Salesman assigned to team successfully');

        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function fireSalesman(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required|numeric'
            ]);
            $salesman = SalesmanController::getSalesman($request->input('user_id'));
            if (!$salesman) {
                throw new UserNotFoundException();
            }
            $salesman->update(['team_id' => null]);
            return $this->success(Mapper::userMapper($salesman), 'Salesman assigned to team successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
