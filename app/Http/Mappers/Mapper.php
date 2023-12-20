<?php

namespace App\Http\Mappers;

use Illuminate\Support\Collection;

class Mapper
{
    public static function leadMapper($lead): array
    {
        if ($lead instanceof Collection) {
            return $lead->map(function ($item) {
                return self::mapLead($item);
            })->toArray();
        } else {
            return self::mapLead($lead);
        }
    }

    private static function mapLead($lead): array
    {
        return [
            'id' => $lead->id,
            'name' => $lead->full_name,
            'email' => $lead->email,
            'phone' => $lead->phone_number,
            'value' => $lead->value,
            'response_time' => $lead->response_time,
            'company_name' => $lead->company_name,
            'job_title' => $lead->job_title,
            'comment' => $lead->comment,
            'status' => self::mapLeadStatus($lead),
            'sales' => self::mapLeadUser($lead),
            'created_at' => $lead->created_at,
        ];
    }

    public static function mapLeadStatus($lead): ?string
    {
        return optional($lead->LeadStatus)->name;
    }

    public static function mapLeadUser($lead): ?string
    {
        return optional($lead->User)->name;
    }


    public static function userMapper($user): array
    {
        if ($user instanceof Collection) {
            return $user->map(function ($item) {
                return self::mapUser($item);
            })->toArray();
        } else {
            return self::mapUser($user);
        }
    }

    private static function mapUser($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phoneNumber' => $user->phone_number,
            'target' => $user->target,
            'current' => $user->current,
            'role' => optional($user->Role)->name,
            'status' => optional($user->UserStatues)->name,
            'team' => optional($user->Team)->name,
        ];
    }


    public static function adminMapper($admin): array
    {
        if ($admin instanceof Collection) {
            return $admin->map(function ($item) {
                return self::mapAdmin($item);
            })->toArray();
        } else {
            return self::mapAdmin($admin);
        }
    }

    private static function mapAdmin($admin): array
    {
        return [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'phoneNumber' => $admin->phone_number,
        ];
    }
    public static function teamMapper($team): array
    {
        if ($team instanceof Collection) {
            return $team->map(function ($item) {
                return self::mapTeam($item);
            })->toArray();
        } else {
            return self::mapTeam($team);
        }
    }

    private static function mapTeam($team): array
    {
        return [
            'id' => $team->id,
            'name' => $team->name,
        ];
    }


    public static function userLeadsMapper($user): array
    {
        $leads = $user->leads->map(function ($lead) {
            return self::leadMapper($lead);
        });

        return array_merge(
            self::userMapper($user),
            ['leads' => $leads->toArray()]
        );
    }

    public static function userNewLeadsMapper($user): array
    {
        $filteredLeads = $user->leads
            ->whereIn("status_id", [1, 4]);

        $leads = $filteredLeads->map(function ($lead) {
            return self::leadMapper($lead);
        });

        return array_merge(
            self::userMapper($user),
            ['leads' => $leads->values()->toArray()]
        );
    }

    public static function userDoneLeadsMapper($user): array
    {
        $filteredLeads = $user->leads
            ->whereIn("status_id", [2, 3]);

        $leads = $filteredLeads->map(function ($lead) {
            return self::leadMapper($lead);
        });
        return array_merge(
            self::userMapper($user),
            ['leads' => $leads->values()->toArray()]
        );
    }

    public static function userLostLeadsMapper($user): array
    {
        $filteredLeads = $user->leads
            ->whereIn("status_id", [5, 6]);

        $leads = $filteredLeads->map(function ($lead) {
            return self::leadMapper($lead);
        });
        return array_merge(
            self::userMapper($user),
            ['leads' => $leads->values()->toArray()]
        );
    }
}
