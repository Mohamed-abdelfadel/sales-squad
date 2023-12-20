<?php

namespace App\Http\Controllers;

use App\Exceptions\LeadNotFoundException;
use App\Http\Mappers\Mapper;
use App\Models\Lead;
use App\Models\User;
use App\Traits\HttpsResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    use HttpsResponses;

    public function syncData()
    {
        $admin = Auth::id();
        $leads = Lead::query()
            ->with("LeadStatus:id,name")
            ->with("User:id,name")
            ->get();

        foreach ($leads as $lead) {
            $data = [
                'id' => $lead->id,
                'name' => $lead->full_name,
                'email' => $lead->email,
                'phone' => (string)$lead->phone_number,
                'value' => $lead->value,
                'status' => optional($lead->LeadStatus)->name,
                'sales' => optional($lead->User)->name,
                'created_at' => $lead->created_at,
            ];

            $existingRecord = Http::get("https://sheetdb.io/api/v1/z8t8fd383gye6/search?email=$lead->email");

            if ($existingRecord->successful() && !empty($existingRecord->json())) {
                continue;
            }

            $response = Http::post('https://sheetdb.io/api/v1/z8t8fd383gye6', $data);
        }

        if (isset($response) && $response->successful()) {
            Log::channel("lead")->info("[LeadController::syncData] Admin with id: $admin accessed this function successfully", ['message' => 'Data added successfully']);
            return response()->json(['message' => 'Data added successfully']);
        } else {
            Log::channel("lead")->error("[LeadController::syncData] Admin with id: $admin accessed this function and returned error", ['error' => 'Failed to add data to SheetDB']);
            return response()->json(['error' => 'Failed to add data to SheetDB'], 500);
        }
    }

    public function getData()
    {
        $admin = Auth::id();
        $url = 'https://sheetdb.io/api/v1/z8t8fd383gye6';
        $response = Http::get($url);
        Log::channel("lead")->info("[LeadController::getData] Admin with id: $admin accessed this function successfully", ['message' => 'Data added successfully']);

        return $response->json();
    }

    public function getAll(): JsonResponse
    {
        $leads = Lead::query()->orderBy('id')->get();
        $admin = AdminController::getAdminProfile();
        Log::channel("lead")->info("[LeadController::getAll] Admin with id: $admin->id accessed this function successfully, and returned all leads");
        return $this->success(Mapper::leadMapper($leads), 'Leads retrieved successfully');
    }

    public function getNew(): JsonResponse
    {
        $leads = Lead::query()->orderBy('id')->whereIn("status_id", [1, 4])->get();
        $admin = AdminController::getAdminProfile();
        Log::channel("lead")->info("[LeadController::getNew] Admin with id: $admin->id accessed this function successfully, and returned the new leads");
        return $this->success(Mapper::leadMapper($leads), 'New leads retrieved successfully');
    }

    public function getDone(): JsonResponse
    {
        $leads = Lead::query()->orderBy('id')->whereIn("status_id", [2, 3])->get();
        $admin = AdminController::getAdminProfile();
        Log::channel("lead")->info("[LeadController::getDone] Admin with id: $admin->id accessed this function successfully, and returned the done leads");
        return $this->success(Mapper::leadMapper($leads), 'Done leads retrieved successfully');
    }

    public function getLost(): JsonResponse
    {
        $leads = Lead::query()->orderBy('id')->whereIn("status_id", [5, 6])->get();
        $admin = AdminController::getAdminProfile();
        Log::channel("lead")->info("[LeadController::getLost] Admin with id: $admin->id accessed this function successfully, and returned the lost leads");
        return $this->success(Mapper::leadMapper($leads), 'Lost leads retrieved successfully');
    }

    public function getById(int $id): JsonResponse
    {
        $admin = AdminController::getAdminProfile();
        try {
            $lead = Lead::query()->find($id);
            if (!$lead) {

                throw new LeadNotFoundException();
            }
            Log::channel("lead")->info("[LeadController::getById] Leads accessed by $admin->id and returned lead by id");
            return $this->success(Mapper::leadMapper($lead), 'Lead retrieved successfully');
        } catch (Exception $e) {
            Log::channel("lead")->error("[LeadController::getById] Admin with id: $admin->id accessed this function, returned with error.", ["message" => $e->getMessage(), "code" => $e->getCode()]);
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function addLeadsToUsers(): JsonResponse
    {

        $sales = SalesmanController::getSalesmen();
        $leads = LeadController::getNewLeads();

        $leadsPerSalesman = count($leads) / count($sales);
        $leadIndex = 0;

        foreach ($sales as $saleMan) {
            for ($i = 0; $i < $leadsPerSalesman; $i++) {
                if ($leadIndex < count($leads)) {
                    $lead = $leads[$leadIndex++];
                    $lead->update([
                        "sales_id" => $saleMan->id
                    ]);
                }
            }
        }
        if (empty(count($leads))) {
            if ($admin = Auth::id() != null) {
                Log::channel("lead")->info("[LeadController::addLeadsToUsers] Leads accessed by admin $admin no leads to fill");
            } else {
                Log::channel("lead")->info("[LeadController::addLeadsToUsers] Leads accessed automatically no leads to fill");
            }
            return $this->success([], "No leads to assign");
        }
        if ($admin = Auth::id() != null) {
            Log::channel("lead")->info("[LeadController::addLeadsToUsers] Leads accessed by admin $admin" . count($leads) . " leads to " . count($sales) . " salesman");
        } else {
            Log::channel("lead")->info("[LeadController::addLeadsToUsers] Leads accessed automatically assigned " . count($leads) . " leads to " . count($sales) . " salesman");
        }
        return $this->success([],
            "System has assigned new " . count($leads) . " leads to " . count($sales) . " salesman"
        );
    }

    public function createLead(Request $request): JsonResponse
    {
        $admin = Auth::id();
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'unique:leads,phone_number']
        ]);
        $user = Lead::query()->create([
            'full_name' => $request->input('full_name'),
            'phone_number' => $request->input('phone_number')
        ]);
        Log::channel("lead")->info("[LeadController::createLead] Leads with phone number $user->phone_number accessed by $admin successfully");
        return $this->success(['user' => $user], "Lead added successfully");
    }

// STOPPED HERE

    /**
     * @note fill the lead with the data by salesman
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function leadFill(int $id, Request $request): JsonResponse
    {
        try {
            $user = SalesmanController::getSalesmenProfile();
            $lead = Lead::query()->where('sales_id', $user->id)->whereIn("status_id", [1, 4])->find($id);
            if (!$lead) {
                throw new LeadNotFoundException("This lead is not found ,or not assigned to you, or already done");
            }
            $timeTaken = $lead->updated_at->diff(now())->format('%y years, %m months, %d days, %H:%I:%S');
            $sales = SalesmanController::getSalesmenProfile();
            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:leads,email',
                'value' => 'required|numeric',
                'company_name' => 'string',
                'job_title' => 'string',
                'address' => 'string',
                'comment' => 'string',
                'status_id' => 'required|integer',
            ]);
            $lead->fill([
                'full_name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'value' => $request->input('value'),
                'company_name' => $request->input('company_name'),
                'response_time' => $timeTaken,
                'job_title' => $request->input('job_title'),
                'address' => $request->input('address'),
                'status_id' => $request->input('status_id'),
                'sales_id' => $sales->id,
                'comment' => $request->input('comment')
            ]);
            if ($request->input('status_id') == 1 || $request->input('status_id') == 4) {
                $lead->update([
                    'sales_id' => null
                ]);
            } elseif ($request->input('status_id') == 2 || $request->input('status_id') == 3) {
                SalesmanController::leadAdded($user, $request->input('value'));
            }
            $lead->save();
            return $this->success(Mapper::leadMapper($lead), "Lead submitted successfully by $sales->name with repose time = $timeTaken");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    // STATIC FUNCTIONS.
    public static function getNewLeads()
    {
        return Lead::query()->whereIn("status_id", [1, 4])->whereNull("sales_id")->get();
    }

    public static function getUsersDoneLeads(User $user)
    {
        return Lead::query()->where('sales_id', $user->id)->whereIn("status_id", [2, 3])->get();
    }
}
