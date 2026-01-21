<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Extensions\DynamicFeatures\HasDynamicFeatures;

class CourtCase extends Model
{
<<<<<<< HEAD
    use HasFactory, HasDynamicFeatures;
=======
    use HasFactory;
>>>>>>> origin/jule-12265746249537321065

    protected $fillable = [
        'matter_id',
        'court_name',
        'case_number',
        'judge_name',
        'opponent_name',
        'opponent_lawyer',
        'current_stage',
    ];

    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }

    public function hearings()
    {
        return $this->hasMany(Hearing::class);
    }
}
