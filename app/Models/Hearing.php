<?php

namespace App\Models;

use App\Extensions\DynamicFeatures\HasDynamicFeatures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hearing extends Model
{
    use HasDynamicFeatures, HasFactory;

    protected $fillable = [
        'court_case_id',
        'date_time',
        'hall_number',
        'procedure_result',
        'next_hearing_date',
        'notes',
    ];

    public function courtCase()
    {
        return $this->belongsTo(CourtCase::class);
    }
}
