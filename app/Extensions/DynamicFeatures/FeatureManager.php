<?php

namespace App\Extensions\DynamicFeatures;

use App\Models\ModelSetting;
use Illuminate\Support\Facades\Cache;

class FeatureManager
{
    protected static $cache = [];

    /**
     * Check if a feature is enabled for a specific model class.
     *
     * @param string $modelClass
     * @param string $feature
     * @return bool
     */
    public static function isEnabled(string $modelClass, string $feature): bool
    {
        // Normalize class name (handle leading backslash)
        $modelClass = ltrim($modelClass, '\\');

        $cacheKey = "{$modelClass}_{$feature}";

        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        $isEnabled = ModelSetting::where('model_class', $modelClass)
            ->where('feature_name', $feature)
            ->where('is_enabled', true)
            ->exists();

        self::$cache[$cacheKey] = $isEnabled;

        return $isEnabled;
    }
}
