<?php

namespace App\Models\Core;

use Auth;

class SideMenu extends Salvaon
{

    protected $file = 'sidemenu.xml';

    protected $root = 'SideMenu';

    protected $child = 'SideMenuGroup';

    protected $primaryKey = 'Identifiant';

    public static function getAll()
    {
        $collections = SideMenu::all()->toArray();

        if (Auth::guard('users')->check()) {
            if (!Auth::guard('users')->user()->admin) {
                $i = 0;
                foreach ($collections as $collection) {
                    if ($collection->Autorisation == 'admin') {
                        unset($collections[$i]);
                    }
                    $i++;
                }
            }
        }

        return $collections;
    }
}
