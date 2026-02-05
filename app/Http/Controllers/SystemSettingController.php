<?php

namespace App\Http\Controllers;

use App\Models\ModelSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemSettingController extends Controller
{
    public function index()
    {
        // Only root can access this
        if (! auth()->user()->hasRole('root')) {
            abort(403);
        }

        $models = [
            'App\Models\User' => 'Users',
            'App\Models\Matter' => 'Matters',
            'App\Models\Party' => 'Parties',
            'App\Models\Document' => 'Documents',
            'App\Models\CourtCase' => 'Court Cases',
            'App\Models\Hearing' => 'Hearings',
        ];

        $settings = ModelSetting::all();

        return Inertia::render('Settings/System', [
            'models' => $models,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        if (! auth()->user()->hasRole('root')) {
            abort(403);
        }

        $request->validate([
            'model_class' => 'required|string',
            'feature_name' => 'required|string',
            'is_enabled' => 'required|boolean',
        ]);

        ModelSetting::updateOrCreate(
            [
                'model_class' => $request->model_class,
                'feature_name' => $request->feature_name,
            ],
            [
                'is_enabled' => $request->is_enabled,
            ]
        );

        return redirect()->back()->with('success', 'Setting updated successfully.');
    }
}
