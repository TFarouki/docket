<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyType extends Model
{
    protected $fillable = ['name', 'slug'];

    public function parties()
    {
        return $this->hasMany(Party::class);
    }
}
