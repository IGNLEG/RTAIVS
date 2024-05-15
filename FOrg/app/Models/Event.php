<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'location',
        'user_id',
        'phone',
        'client_id',
        'start_date',
        'end_date',
        'event_type',
        'event_subtype',
        'description',
        'event_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function files(): HasMany
    {
        return $this->hasMany(EventFile::class);
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'events_equipment', 'events_id', 'equipment_id')->withPivot('equipment_amount');
    }
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'events_users', 'events_id', 'users_id');
    }
    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'events_vehicles', 'events_id', 'vehicles_id')->withPivot('vehicle_amount');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(EventMessage::class);
    }
}
