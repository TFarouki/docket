<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory, SoftDeletes;

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
}
