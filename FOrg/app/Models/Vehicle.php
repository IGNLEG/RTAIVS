<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vehicle extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'warehouse_id',
        'license_plate',
        'make',
        'model',
        'amount_total',
        'amount_available',
        'gearbox',
        'mileage',
        'mpg',
        'price_per_km',
        'profit',
        'problems',
        'insurance_until',
        'inspection_until'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'events_vehicles', 'vehicle_id', 'events_id');
    }
}
