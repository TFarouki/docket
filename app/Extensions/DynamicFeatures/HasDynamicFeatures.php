<?php

namespace App\Extensions\DynamicFeatures;

use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

trait HasDynamicFeatures
{
    /**
     * Boot the trait.
     */
    public static function bootHasDynamicFeatures()
    {
        // 1. User Tracking
        static::creating(function ($model) {
            if (FeatureManager::isEnabled(get_class($model), 'user_tracking')) {
                if (Auth::check()) {
                    if (! $model->created_by) {
                        $model->created_by = Auth::id();
                    }
                    if (! $model->updated_by) {
                        $model->updated_by = Auth::id();
                    }
                }
            }
        });

        static::updating(function ($model) {
            if (FeatureManager::isEnabled(get_class($model), 'user_tracking')) {
                if (Auth::check()) {
                    $model->updated_by = Auth::id();
                }
            }
        });

        // 2. Soft Deletes (Global Scope)
        if (FeatureManager::isEnabled(static::class, 'soft_delete')) {
            static::addGlobalScope(new SoftDeletingScope);
        }
    }

    /**
     * Perform the actual delete query on this model instance.
     * Overriding Laravel's internal delete logic to handle dynamic soft deletes.
     *
     * @return void
     */
    public function delete()
    {
        if (FeatureManager::isEnabled(get_class($this), 'soft_delete')) {
            // Logic similar to SoftDeletes trait
            if (is_null($this->{$this->getDeletedAtColumn()})) {
                $this->{$this->getDeletedAtColumn()} = $this->freshTimestamp();

                // If tracking is on, we might want to record who deleted it?
                // For now, standard soft delete.

                return $this->save();
            }

            return true;
        }

        return parent::delete();
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getDeletedAtColumn()
    {
        return defined('static::DELETED_AT') ? static::DELETED_AT : 'deleted_at';
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedDeletedAtColumn()
    {
        return $this->qualifyColumn($this->getDeletedAtColumn());
    }
}
