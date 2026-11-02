<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairJobOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'device_id',
        'technician_id',
        'job_order_number',
        'reported_issue',
        'resolution_notes',
        'job_status',
        'parts_cost',
        'labor_cost',
        'total_repair_cost',
        'completion_date',
    ];

    protected $casts = [
        'parts_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'total_repair_cost' => 'decimal:2',
        'completion_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(JobPayment::class);
    }
}
