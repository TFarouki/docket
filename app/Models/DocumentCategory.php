<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Extensions\DynamicFeatures\HasDynamicFeatures;

class DocumentCategory extends Model
{
    use HasFactory, HasDynamicFeatures;

    protected $fillable = [
        'name',
        'type',
        'description',
    ];
}
