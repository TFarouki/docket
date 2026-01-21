<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use App\Extensions\DynamicFeatures\HasDynamicFeatures;
=======
use Illuminate\Database\Eloquent\SoftDeletes;
>>>>>>> origin/jule-12265746249537321065
use Illuminate\Database\Eloquent\Model;

class Matter extends Model
{
<<<<<<< HEAD
    use HasFactory, HasDynamicFeatures;
=======
    use HasFactory, SoftDeletes;
>>>>>>> origin/jule-12265746249537321065

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
