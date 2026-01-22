<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Extensions\DynamicFeatures\HasDynamicFeatures;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory, HasDynamicFeatures;

    protected $fillable = [
        'type',
        'full_name',
        'phone',
        'email',
        'national_id',
        'address',
        'notes',
    ];

    public function matters()
    {
        return $this->hasMany(Matter::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
