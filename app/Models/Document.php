<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Extensions\DynamicFeatures\HasDynamicFeatures;

class Document extends Model
{
    use HasDynamicFeatures;
    protected $fillable = [
        'title',
        'document_category_id',
        'category', // Keeping for backward compatibility or migration
        'file_path',
        'file_type',
        'file_size',
        'description',
        'uploader_id',
        'documentable_id',
        'documentable_type',
    ];

    /**
     * Get the parent documentable model (User, Matter, etc.).
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    /**
     * Get the category of the document.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }
}
