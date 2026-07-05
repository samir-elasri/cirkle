<?php

use App\Models\ContactedProvider;
use App\Models\Core\Attendee;
use App\Models\Core\BasicEvent;
use App\Models\Core\Bloc;
use App\Models\Core\Blocs\BlocAudio;
use App\Models\Core\Blocs\BlocDocument;
use App\Models\Core\Blocs\BlocForm;
use App\Models\Core\Blocs\BlocGallery;
use App\Models\Core\Blocs\BlocGoogleMap;
use App\Models\Core\Blocs\BlocImage;
use App\Models\Core\Blocs\BlocMiniCard;
use App\Models\Core\Blocs\BlocPortfolio;
use App\Models\Core\Blocs\BlocTableOfContent;
use App\Models\Core\Blocs\BlocText;
use App\Models\Core\Blocs\BlocVideo;
use App\Models\Core\Category;
use App\Models\Core\CategoryGroup;
use App\Models\Core\Country;
use App\Models\Core\Document;
use App\Models\Core\Forms\Choice;
use App\Models\Core\Forms\ChoiceGroup;
use App\Models\Core\Forms\FormAnswer;
use App\Models\Core\Forms\FormField;
use App\Models\Core\Forms\FormGenerator;
use App\Models\Core\Gallery;
use App\Models\Core\GalleryElement;
use App\Models\Core\GoogleMap;
use App\Models\Core\GoogleMapGroup;
use App\Models\Core\InvoiceAddress;
use App\Models\Core\ListEmail;
use App\Models\Core\MenuTree;
use App\Models\Core\MiniCard;
use App\Models\Core\MiniCardGroup;
use App\Models\Core\News;
use App\Models\Core\Onglet;
use App\Models\Core\Order;
use App\Models\Core\Page;
use App\Models\Core\PriceCut;
use App\Models\Core\Product;
use App\Models\Core\ProductCat;
use App\Models\Core\Pub;
use App\Models\Core\PubGroup;
use App\Models\Core\Purchase;
use App\Models\Core\PurchasedSub;
use App\Models\Core\Reminder;
use App\Models\Core\SearchResult;
use App\Models\Core\SentReminder;
use App\Models\Core\Setting;
use App\Models\Core\Sharing;
use App\Models\Core\ShippingAddress;
use App\Models\Core\SideMenu;
use App\Models\Core\Slide;
use App\Models\Core\Slideshow;
use App\Models\Core\State;
use App\Models\Core\Subscriber;
use App\Models\Core\Subscription;
use App\Models\Core\Target;
use App\Models\Core\User;
use App\Models\Diploma;
use App\Models\Evaluation;
use App\Models\JobOffer;
use App\Models\License;
use App\Models\LikedSubscriber;
use App\Models\PostalCode;
use App\Models\ProfileOption;
use App\Models\Promotion;
use App\Models\PurchasedSubRecord;
use App\Models\SavedSearch;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubscriberImage;
use App\Models\SubscriberService;
use App\Models\SubscriberServiceCategory;
use App\Models\SubscriptionPrice;

return [
	'attendees'              => Attendee::class,
	'blocs'                  => Bloc::class,
	'bloc_audios'            => BlocAudio::class,
	'bloc_audio'             => BlocAudio::class,
	'bloc_documents'         => BlocDocument::class,
	'bloc_forms'             => BlocForm::class,
	'bloc_galleries'         => BlocGallery::class,
	'bloc_google_maps'       => BlocGoogleMap::class,
	'bloc_images'            => BlocImage::class,
	'bloc_mini_cards'        => BlocMiniCard::class,
	'bloc_portfolios'        => BlocPortfolio::class,
	'bloc_table_of_contents' => BlocTableOfContent::class,
	'bloc_texts'             => BlocText::class,
	'bloc_videos'            => BlocVideo::class,
	'categories'             => Category::class,
	'category_groups'        => CategoryGroup::class,
	'galleries'              => Gallery::class,
	'gallery_elements'       => GalleryElement::class,
	'elements'               => GalleryElement::class,
	'basic_events'           => BasicEvent::class,
	'google_maps'            => GoogleMap::class,
	'google_map_groups'      => GoogleMapGroup::class,
	'menu_trees'             => MenuTree::class,
	'mini_cards'             => MiniCard::class,
	'mini_card_groups'       => MiniCardGroup::class,
	'news'                   => News::class,
	'onglets'                => Onglet::class,
	'pages'                  => Page::class,
	'pubs'                   => Pub::class,
	'pub_groups'             => PubGroup::class,
	'search_results'         => SearchResult::class,
	'settings'               => Setting::class,
	'sharings'               => Sharing::class,
	'shipping_addresses'     => ShippingAddress::class,
	'invoice_addresses'      => InvoiceAddress::class,
	'side_menus'             => SideMenu::class,
	'slides'                 => Slide::class,
	'slideshows'             => Slideshow::class,
	'subscribers'            => Subscriber::class,
	'users'                  => User::class,
	'choices'                => Choice::class,
	'choice_groups'          => ChoiceGroup::class,
	'form_fields'            => FormField::class,
	'form_generators'        => FormGenerator::class,
	'form_answers'           => FormAnswer::class,
	'documents'              => Document::class,
	'subscriptions'          => Subscription::class,
	'reminders'              => Reminder::class,
	'product_cats'           => ProductCat::class,
	'purchased_subs'         => PurchasedSub::class,
	'sent_reminders'         => SentReminder::class,
	'price_cuts'             => PriceCut::class,
	'products'               => Product::class,
	'orders'                 => Order::class,
	'purchases'              => Purchase::class,
	'states'                 => State::class,
	'countries'              => Country::class,
	'list_emails'            => ListEmail::class,
	'targets'                => Target::class,
	'service_categories'     => ServiceCategory::class,
	'services'               => Service::class,
	'subscription_prices'    => SubscriptionPrice::class,
	'saved_searches'         => SavedSearch::class,
	'contacted_providers'    => ContactedProvider::class,
	'profile_options'        => ProfileOption::class,
	'purchased_sub_records'  => PurchasedSubRecord::class,
	'job_offers'             => JobOffer::class,
	'subscriber_images'      => SubscriberImage::class,
	'promotions'             => Promotion::class,
	'licenses'               => License::class,
	'diplomas'               => Diploma::class,
	'liked_subscribers'      => LikedSubscriber::class,
	'postal_codes'           => PostalCode::class,
	'subscriber_services'    => SubscriberService::class,
	'subscriber_service_categories'    => SubscriberServiceCategory::class,
	'evaluations'    => Evaluation::class,

];
