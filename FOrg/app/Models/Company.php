<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'owner',
        'phone',
        'email',
        'audio_subrent',
        'video_subrent',
        'vehicle_subrent',
        'storage_subrent',
        'stage_subrent'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    //Clients here mean the company's workers
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
