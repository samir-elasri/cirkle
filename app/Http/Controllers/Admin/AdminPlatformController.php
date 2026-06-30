<?php

namespace App\Http\Controllers\Admin;

use App\Models\Core\Setting;
use Illuminate\Http\Request;
use View;

/**
 * Page admin : activer/bloquer (« À VENIR ») CHACUNE des 4 plateformes individuellement
 * (Denis 30.06). Stocké dans settings.platforms_coming_soon (JSON de clés « locale-type »).
 */
class AdminPlatformController extends AdminBaseController
{
    /** Les 4 plateformes : clé « locale-type » => libellé. */
    public static function platforms(): array
    {
        return [
            'en-residential' => 'Residential English',
            'fr-residential' => 'Résidentiel Français',
            'en-business'    => 'B2B English',
            'fr-business'    => 'B2B Français',
        ];
    }

    public function edit()
    {
        $setting = Setting::first();
        $comingSoon = json_decode($setting->platforms_coming_soon ?? '[]', true) ?: [];
        $platforms = self::platforms();

        return View::make('platform.edit', compact('setting', 'comingSoon', 'platforms'));
    }

    public function update(Request $request)
    {
        $valid = array_keys(self::platforms());
        $selected = array_values(array_intersect($valid, (array) $request->input('coming_soon', [])));

        $setting = Setting::first();
        $setting->platforms_coming_soon = json_encode($selected);
        $setting->save();

        return redirect()->back()->with('success', 'Plateformes mises à jour.');
    }
}
