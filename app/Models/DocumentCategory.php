<?php

namespace App\Models;

use App\Extensions\DynamicFeatures\HasDynamicFeatures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    use HasDynamicFeatures, HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
    ];
}
