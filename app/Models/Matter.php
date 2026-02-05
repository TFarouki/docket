<?php

namespace App\Models;

use App\Extensions\DynamicFeatures\HasDynamicFeatures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matter extends Model
{
    use HasDynamicFeatures, HasFactory;

    protected $fillable = [
        'party_id',
        'responsible_lawyer_id',
        'parent_id',
        'title',
        'year',
        'reference_number',
        'type',
        'case_type',
        'court_name',
        'status',
        'agreed_fee',
        'description',
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function responsibleLawyer()
    {
        return $this->belongsTo(User::class, 'responsible_lawyer_id');
    }

    public function courtCases()
    {
        return $this->hasMany(CourtCase::class);
    }
}
