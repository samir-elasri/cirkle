<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServiceCategory;
use App\Models\Service;
use Arr;
use View;
use Illuminate\Http\Request;

class AdminFicheController extends AdminBaseController
{
    public function create() {
        // Liste des fiches déjà importées (chaque profession = un TITRE INTERNE),
        // pour voir en un coup d'œil ce qui a été téléversé (ou rien).
        $fiches = ServiceCategory::whereNotNull('service_category_id')
            ->orderByDesc('id')->get();
        $parents = ServiceCategory::whereNull('service_category_id')->get()->keyBy('id');
        $serviceCounts = Service::selectRaw('service_category_id, COUNT(*) as c')
            ->groupBy('service_category_id')->pluck('c', 'service_category_id');

        return View::make('fiche.create', compact('fiches', 'parents', 'serviceCounts'));
    }

    public function store(Request $request) {
        $data = $request->all([
            'category_label',
            'category',
            'profession',
            'services',
        ]);

        $category = new ServiceCategory(['label' => $data['category_label']]);
        $category->saveElement($data['category']);

        $profession = new ServiceCategory([
            'service_category_id'=> $category->id,
        ]);
        $profession->saveElement($data['profession']);

        $services = [];
        foreach ($data['services'] as $service) {
            if (!Arr::get($service, 'fr.title') && !Arr::get($service, 'en.title')) {
                continue;
            }
            $s = new Service([
                'service_category_id'=> $profession->id,
            ]);
            $s->saveElement($service);
            $services[] = $s;
        }

        return redirect()->back()->with('success', 'Fiche sauvegardée');
    }
}
