<?php

namespace App\Console\Commands;

use App\Models\ContactedProvider;
use App\Models\Core\Subscriber;
use App\Models\SavedSearch;
use Illuminate\Console\Command;

class DailyCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0);
        $this->sendSurveys();
        $this->searchNotifications();
        $this->subscriptionLifecycle();
    }

    /**
     * Cycle de vie des abonnements (feature #12) :
     *  - 7 jours avant expiration : courriel de rappel (payer ou annuler dans le profil)
     *  - délai de grâce de 7 jours après l'expiration (la fiche reste visible)
     *  - après la grâce, ou si annulé en fin de terme : désactivation + fiche masquée
     *
     * Note : la charge récurrente AUTOMATIQUE (abonnement Stripe + webhooks) dépend du
     * compte Stripe du client; ici on gère les rappels, la grâce et la fin de terme.
     */
    private function subscriptionLifecycle(): void
    {
        $now = now();

        // Délai de rappel/grâce : 10 jours (cahier de charges), configurable via réglage.
        $window = (int) (setting('renewal_reminder_days') ?: 10);

        // 1) Rappel 10 jours avant expiration (une seule fois)
        $expiringSoon = \App\Models\Core\PurchasedSub::query()
            ->where('active', true)
            ->where('cancel_at_period_end', false)
            ->whereNull('renewal_reminder_sent_at')
            ->whereDate('end_date', '>=', $now->toDateString())
            ->whereDate('end_date', '<=', $now->copy()->addDays($window)->toDateString())
            ->with('subscriber')
            ->get();

        $this->info($expiringSoon->count() . ' subscription(s) expiring within 7 days');

        foreach ($expiringSoon as $sub) {
            try {
                if ($sub->subscriber?->email) {
                    \Mail::to($sub->subscriber->email)->send(new \App\Mail\AdminMail(
                        __('subscription.renewal_email_body', [
                            'date' => $sub->end_date,
                            'url'  => urlRouteName('profile', [], true),
                        ]),
                        __('subscription.renewal_email_title')
                    ));
                }
                $sub->update(['renewal_reminder_sent_at' => $now]);
            } catch (\Throwable $e) {
                $this->error('renewal reminder failed for sub ' . $sub->id . ': ' . $e->getMessage());
            }
        }

        // 2) Fin de terme : expiré au-delà de la grâce de 10 jours, OU annulé en fin de terme
        $graceCutoff = $now->copy()->subDays($window)->toDateString();

        $toDeactivate = \App\Models\Core\PurchasedSub::query()
            ->where('active', true)
            ->where(function ($q) use ($now, $graceCutoff) {
                $q->whereDate('end_date', '<', $graceCutoff) // grâce écoulée
                  ->orWhere(function ($q2) use ($now) {
                      $q2->where('cancel_at_period_end', true)
                         ->whereDate('end_date', '<', $now->toDateString());
                  });
            })
            ->with('subscriber')
            ->get();

        $this->info($toDeactivate->count() . ' subscription(s) to deactivate (term ended)');

        foreach ($toDeactivate as $sub) {
            $sub->update(['active' => false]);
            $sub->subscriber?->update(['is_public' => false]);
        }
    }

    private function sendSurveys(): void
    {
        $contactedProviders = ContactedProvider
            ::whereRaw('HOUR(TIMEDIFF(NOW(), contacted_providers.created_at)) >= 720')
            ->where('evaluation_mail_sent', '=', false)
            ->whereNotNull('client_id')
            ->whereNotNull('provider_id')
            ->with('client')
            ->get();

        $this->info($contactedProviders->count() . ' contacted providers found');

        foreach ($contactedProviders as $contactedProvider) {
            $success = $contactedProvider->client?->sendMail('customer_evaluation', [
                'url' => urlRouteName('evaluation', [
                    'client_id' => $contactedProvider->client_id,
                    'provider_id' => $contactedProvider->provider_id,
                ], true)
            ]);
            if ($success) {
                $contactedProvider->client->evaluation_mail_sent = true;
                $contactedProvider->client->save();
            }
            else {
                $this->error('contacted provider client ' . $contactedProvider->client_id . ' email failed');
            }
        }
    }

    private function searchNotifications(): void
    {
        $subscribers = [];
        $savedSearches = SavedSearch::where('active', '=', true)
            ->whereRaw('HOUR(TIMEDIFF(NOW(), created_at)) <= 24')
            ->whereNotNull('subscriber_id')
            ->with('subscriber')
            ->with('services')
            ->with('serviceCategories')
            ->get();

        $this->info($savedSearches->count() . ' saved searches found');

        foreach($savedSearches as $savedSearch) {

            try {
                $hasResults = (bool) $savedSearch->doSearch()->count();
            }
            catch (\ErrorException $e) {
                $this->error($e->getMessage() . ' for Subscriber::ProviderSearch with ' . $savedSearch->id);
                continue;
            }

            if ($hasResults) {
                $this->info('SavedSearch(' . $savedSearch->id . ') has results');

                if (!array_key_exists($savedSearch->subscriber_id, $subscribers)) {
                    $subscribers[$savedSearch->subscriber_id] = [
                        'subscriber' => $savedSearch->subscriber,
                        'searches' => []
                    ];
                }
                $subscribers[$savedSearch->subscriber_id]['searches'][] = $savedSearch;
            }
        }

        foreach ($subscribers as $subscriberArray) {
            $searchesWithResults = $subscriberArray['searches'];
            $subscriber = $subscriberArray['subscriber'];
            $list = [];

            foreach($searchesWithResults as $search) {
                $label = trans_choice('mail.saved-search-list-item',
                    $search->services->count(),
                    [
                        'postal-code' => $search->postal_code,
                        'service' => $search->services->implode('title', ', ')
                    ]
                );

                $list[] = '<a href="'. $search->url . '">'
                    . $label
                    . '</a>';
            }

            $subscriber->sendMail('search_notification', [
                'list' => implode(PHP_EOL, $list),
            ]);
        }
    }
}
