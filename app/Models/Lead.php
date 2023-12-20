<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    protected $fillable = [
        'sales_id',
        'full_name',
        'email',
        'value',
        'phone_number',
        'company_name',
        'job_title',
        'comment',
        'response_time',
        'status_id',
        'address'
    ];

    use HasFactory;

    public function LeadStatus(): HasOne
    {
        return $this->hasOne(LeadStatus::class, "id", "status_id");
    }

    public function User(): HasOne
    {
        return $this->hasOne(User::class, "id", "sales_id");
    }
}
