<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_job_order_id',
        'amount',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function repairJobOrder(): BelongsTo
    {
        return $this->belongsTo(RepairJobOrder::class);
    }
}
