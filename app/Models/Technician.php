<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialty',
        'phone',
        'email',
    ];

    public function repairJobOrders(): HasMany
    {
        return $this->hasMany(RepairJobOrder::class);
    }
}
