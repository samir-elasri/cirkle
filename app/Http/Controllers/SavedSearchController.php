<?php

namespace App\Http\Controllers;

use App\Models\SavedSearch;
use Illuminate\Http\Request;

class SavedSearchController extends Controller
{

    public function attachSavedSearch($id)
    {
        $subscriber = auth('subscribers')->user();
        if (!$subscriber) {
            abort(400);
        }

        if ($search = SavedSearch::find($id)) {
            $search->update([
                'subscriber_id' => $subscriber->id,
            ]);
        }
        return redirect()->to(urlRouteName('profile') . '?tab=supplier&subTab=saved-searches');
    }

    public function deleteSearch($id)
	{
		if ($search = SavedSearch::find($id)) {
			$search->update([
				'active' => false
			]);
		}
		return redirect()->back();
	}
}
