<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Extensions\DynamicFeatures\HasDynamicFeatures;

class Hearing extends Model
{
<<<<<<< HEAD
    use HasFactory, HasDynamicFeatures;
=======
    use HasFactory;
>>>>>>> origin/jule-12265746249537321065

    protected $fillable = [
        'court_case_id',
        'session_date',
        'outcome',
        'notes',
    ];

    public function courtCase()
    {
        return $this->belongsTo(CourtCase::class);
    }
}
