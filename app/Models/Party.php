<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use App\Extensions\DynamicFeatures\HasDynamicFeatures;
=======
use Illuminate\Database\Eloquent\SoftDeletes;
>>>>>>> origin/jule-12265746249537321065
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
<<<<<<< HEAD
    use HasFactory, HasDynamicFeatures;
=======
    use HasFactory, SoftDeletes;
>>>>>>> origin/jule-12265746249537321065

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
<<<<<<< HEAD
=======

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
>>>>>>> origin/jule-12265746249537321065
}
