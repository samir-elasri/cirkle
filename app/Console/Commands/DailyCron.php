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
