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

		if ($subscription = $sub->getLatestSubscription()) {
			$sub->update([
				'is_public' => false,
			]);
			$subscription->update([
				'active' => false,
			]);
//			TODO STRIPE
			return back()->with('success', trans('auth.subscription.canceled'));
		}
	}
}
