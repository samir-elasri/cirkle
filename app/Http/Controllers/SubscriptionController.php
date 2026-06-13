<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function pause() {
		$sub = Auth::guard('subscribers')->user();

		if ($subscription = $sub->getActiveSubscription()) {
			$sub->update([
				'is_public' => false,
			]);
			$subscription->update([
				'on_pause' => true,
				'pause_start_date' => now()->toDateString(),
			]);
//			TODO STRIPE

			return back()->with('success', trans('auth.subscription.paused'));
		}
	}
    public function unpause() {
		$sub = Auth::guard('subscribers')->user();
		if ($subscription = $sub->getPausedSubscription()) {
			echo $subscription;
			$sub->update([
				'is_public' => true,
			]);
			$subscription->update([
				'on_pause' => false,
				'pause_end_date' => now()->toDateString(),
			]);

			$duration = Carbon::parse($subscription->pause_start_date)->diffInDays($subscription->pause_end_date);

			$subscription->update([
				'end_date' => Carbon::parse($subscription->end_date)->addDays($duration)->toDateString(),
			]);


			return back()->with('success', trans('auth.subscription.unpaused'));
		}
	}

	public function cancel() {
		$sub = Auth::guard('subscribers')->user();

		// Annulation effective à la FIN DU TERME, sans remboursement (spec #12).
		// L'abonnement reste actif et la fiche visible jusqu'à end_date; le cron
		// (DailyCron) le désactive ensuite et ne le renouvelle pas.
		if ($subscription = $sub->getLatestSubscription()) {
			$subscription->update([
				'cancel_at_period_end' => true,
			]);
			return back()->with('success', trans('auth.subscription.canceled'));
		}

		return back();
	}
}
