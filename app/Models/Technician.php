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
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function repairJobOrders(): HasMany
    {
        return $this->hasMany(RepairJobOrder::class);
    }

    public function isSuitableForDeviceType(?string $deviceType): bool
    {
        return blank($deviceType) || stripos($this->specialty ?? '', trim($deviceType)) !== false;
    }
}
