<?php

namespace App\Http\Controllers\Admin;

use App\Models\Core\Setting;
use Illuminate\Http\Request;
use View;

/**
 * Petite page admin : activer/désactiver les 2 plateformes FRANÇAISES (« À VENIR »).
 * Denis (30.06) : pouvoir bloquer/réactiver les plateformes françaises depuis le tableau
 * de bord, sans toucher au code. Stocké dans settings.french_platforms_coming_soon.
 */
class AdminPlatformController extends AdminBaseController
{
    public function edit()
    {
        $setting = Setting::first();
        return View::make('platform.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();
        $setting->french_platforms_coming_soon = $request->boolean('french_platforms_coming_soon');
        $setting->save();

        return redirect()->back()->with('success', 'Plateformes françaises mises à jour.');
    }
}
