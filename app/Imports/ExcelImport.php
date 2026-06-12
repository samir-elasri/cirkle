<?php
namespace App\Imports;

use App\Models\ServiceCategory;
use App\Models\SubscriptionPrice;
use App\Models\Core\Subscription;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ExcelImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $servicesIntroText = [];
        $services = [];
        $consumersText = [];
        $capabilitiesText = [];
        $capabilities = [];
        $oneMonthPrice = $threeMonthPrice = $sixMonthPrice = $twelveMonthPrice = 0;
        $keywords = [];

        $sectionID = 0;
        foreach($rows as $row) {
            if ($sectionID === 0) {
                $sectionID++;
                $langue = strtolower(substr($row->get(1), 0, 2));
                continue;
            }
            if ($sectionID === 1) {
                $sectionID++;
                $titreInterne = $row->get(1);
                continue;
            }
            if ($sectionID === 2) {
                $sectionID++;
                $categorie = $row->get(1);
                continue;
            }
            if ($sectionID === 3) {
                $sectionID++;
                $profession = $row->get(1);
                continue;
            }
            // NEW SECTION 4: Services intro text
            if ($sectionID === 4) {
                if (empty($servicesIntroText) || empty($row->get(0))) {
                    $servicesIntroText[] = trim($row->get(1));
                }
                else {
                    $sectionID++;
                }
                // no continue;
            }
            // Services list (was section 4, now section 5)
            if ($sectionID === 5) {
                if (empty($services) || empty($row->get(0))) {
                    $services[] = [$row->get(1), $row->get(2)];
                }
                else {
                    $sectionID++;
                }
                // no continue;
            }
            // Consumers text (was section 5, now section 6)
            if ($sectionID === 6) {
                if (empty($consumersText) || empty($row->get(0))) {
                    $consumersText[] = trim($row->get(1));
                }
                else {
                    $sectionID++;
                }
                // no continue;
            }
            // Capabilities text (was section 6, now section 7)
            if ($sectionID === 7) {
                if (empty($capabilitiesText) || empty($row->get(0))) {
                    $capabilitiesText[] = trim($row->get(1));
                }
                else {
                    $sectionID++;
                }
                // no continue;
            }
            // Capabilities list (was section 7, now section 8)
            if ($sectionID === 8) {
                if (empty($capabilities) || empty($row->get(0))) {
                    $capabilities[] = [$row->get(1), $row->get(2)];
                }
                else {
                    $sectionID++;
                }
                // no continue;
            }
            // 1-month price (was section 8, now section 9)
            if ($sectionID === 9) {
                $sectionID++;
                $oneMonthPrice = preg_replace('/[^0-9]/', '', $row->get(1));
                continue;
            }
            // NEW SECTION 10: 3-month price
            if ($sectionID === 10) {
                $sectionID++;
                $threeMonthPrice = preg_replace('/[^0-9]/', '', $row->get(1));
                continue;
            }
            // 6-month price (was section 9, now section 11)
            if ($sectionID === 11) {
                $sectionID++;
                $sixMonthPrice = preg_replace('/[^0-9]/', '', $row->get(1));
                continue;
            }
            // 12-month price (was section 10, now section 12)
            if ($sectionID === 12) {
                $sectionID++;
                $twelveMonthPrice = preg_replace('/[^0-9]/', '', $row->get(1));
                continue;
            }
            // Keywords (was section 11, now section 13)
            if ($sectionID === 13) {
                if (empty($row->get(1))) {
                    break;
                }
                $keywords[] = $row->get(1);
            }
        }

        app()->setLocale($langue);

        $parentModel = ServiceCategory::firstOrCreate([
            'label' => $categorie
        ]);

        if (empty($parentModel->title)) {
            $parentModel->title = $categorie;
            $parentModel->save();
        }

        $categoryModel = ServiceCategory::firstOrCreate([
            'label' => $titreInterne
        ]);

        $categoryModel->service_category_id = $parentModel->id;
        $categoryModel->title = $profession;

        // Process services intro text
        $categoryModel->services_intro_text = '';
        foreach ($servicesIntroText as $text) {
            if (empty($text)) {
                $categoryModel->services_intro_text .= '<br>';
            }
            else {
                $categoryModel->services_intro_text .= ' ' . $text . ' ';
            }
        }

        $categoryModel->customers_text = '';
        foreach ($consumersText as $text) {
            if (empty($text)) {
                $categoryModel->customers_text .= '<br>';
            }
            else {
                $categoryModel->customers_text .= ' ' . $text . ' ';
            }
        }

        $categoryModel->capabilities_text = '';
        foreach ($capabilitiesText as $text) {
            if (empty($text)) {
                $categoryModel->capabilities_text .= '<br>';
            }
            else {
                $categoryModel->capabilities_text .=  ' ' . $text . ' ';
            }
        }

        $categoryModel->keywords_json = json_encode($keywords);

        $categoryModel->save();

        foreach($services as $service) {
            \App\Models\Service::create([
                'service_category_id' => $categoryModel->id,
                'type' => 'service',
                'title' => $service[0],
                'input_label' => $service[1],
            ]);
        }

        foreach($capabilities as $service) {
            \App\Models\Service::create([
                'service_category_id' => $categoryModel->id,
                'type' => 'capability',
                'title' => $service[0],
                'input_label' => $service[1],
            ]);
        }

        $oneMonthSubscription = Subscription::where('duration', '=', 1)->where('active', '=', true)->first();
        $threeMonthSubscription = Subscription::where('duration', '=', 3)->where('active', '=', true)->first();
        $sixMonthSubscription = Subscription::where('duration', '=', 6)->where('active', '=', true)->first();
        $twelveMonthSubscription = Subscription::where('duration', '=', 12)->where('active', '=', true)->first();

        $sub = SubscriptionPrice::firstOrCreate([
            'service_category_id' => $categoryModel->id,
            'subscription_id' => $oneMonthSubscription->id,
        ]);
        $sub->cost = $oneMonthPrice;
        $sub->save();

        $sub = SubscriptionPrice::firstOrCreate([
            'service_category_id' => $categoryModel->id,
            'subscription_id' => $threeMonthSubscription->id,
        ]);
        $sub->cost = $threeMonthPrice;
        $sub->save();

        $sub = SubscriptionPrice::firstOrCreate([
            'service_category_id' => $categoryModel->id,
            'subscription_id' => $sixMonthSubscription->id,
        ]);
        $sub->cost = $sixMonthPrice;
        $sub->save();

        $sub = SubscriptionPrice::firstOrCreate([
            'service_category_id' => $categoryModel->id,
            'subscription_id' => $twelveMonthSubscription->id,
        ]);
        $sub->cost = $twelveMonthPrice;
        $sub->save();
    }
}
