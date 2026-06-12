<?php
return [
	'pages'             => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Blocs',
			'relation' => 'blocs',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Partage',
			'relation' => 'sharing',
			'create'   => false,
			'active'   => false,
		],
	],
	'news'              => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Blocs',
			'relation' => 'blocs',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Partage',
			'relation' => 'sharing',
			'create'   => false,
			'active'   => false,
		],
	],
	'basic_events'      => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Participants',
			'relation' => 'attendees',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Blocs',
			'relation' => 'blocs',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Partage',
			'relation' => 'sharing',
			'create'   => false,
			'active'   => false,
		],
	],
	'pub_groups'        => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Éléments',
			'relation' => 'pubs',
			'create'   => false,
			'active'   => false,
		],
	],
	'list_emails'       => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Cibles',
			'relation' => 'targets',
			'create'   => false,
			'active'   => false,
		],
	],
	'subscribers'       => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		// [
		// 	'nom'      => 'Historique d\'abonnement',
		// 	'relation' => 'purchased_sub_records',
		// 	'create'   => false,
		// 	'active'   => false,
		// ],
		[
			'nom'      => 'Historique d\'abonnement',
			'relation' => 'purchased_subs',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Offres d\'emploi',
			'relation' => 'job_offers',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Photos',
			'relation' => 'subscriber_images',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Promotions',
			'relation' => 'promotions',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Permis',
			'relation' => 'licenses',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Fournisseurs aimés',
			'relation' => 'liked_subscribers',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Services',
			'relation' => 'subscriber_services',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Codes postaux',
			'relation' => 'postal_codes',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Sous-catégories',
			'relation' => 'subscriber_service_categories',
			'create'   => false,
			'active'   => false,
		],
		//		[
		//			'nom'      => 'Adresse de facturation',
		//			'relation' => 'invoice_address',
		//			'create'   => false,
		//			'active'   => false,
		//		],
		//		[
		//			'nom'      => 'Adresse de livraison',
		//			'relation' => 'shipping_address',
		//			'create'   => false,
		//			'active'   => false,
		//		],
		//		[
		//			'nom'      => 'Historique de membership pris',
		//			'relation' => 'purchased_subs',
		//			'create'   => false,
		//			'active'   => false,
		//		],
	],
	'subscriptions'     => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Prix',
			'relation' => 'subscription_prices',
			'create'   => false,
			'active'   => false,
		],
	],
	'purchased_subs'    => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Rappels envoyés',
			'relation' => 'sent_reminders',
			'create'   => false,
			'active'   => false,
		],
	],
	'slideshows'        => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Diapositives',
			'relation' => 'slides',
			'create'   => false,
			'active'   => false,
		],
	],
	'form_generators'   => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Champs',
			'relation' => 'form_fields',
			'create'   => false,
			'active'   => false,
		],
		[
			'nom'      => 'Réponses',
			'relation' => 'form_answers',
			'create'   => false,
			'active'   => false,
		],
	],
	'choice_groups'     => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Choix',
			'relation' => 'choices',
			'create'   => false,
			'active'   => false,
		],
	],
	'google_map_groups' => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Liste de points',
			'relation' => 'google_maps',
			'create'   => false,
			'active'   => false,
		],
	],
	'mini_card_groups'  => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Fiches',
			'relation' => 'mini_cards',
			'create'   => false,
			'active'   => false,
		],
	],
	'category_groups'   => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Éléments',
			'relation' => 'categories',
			'create'   => false,
			'active'   => false,
		],
	],
	'galleries'         => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
		[
			'nom'      => 'Éléments',
			'relation' => 'elements',
			'create'   => false,
			'active'   => false,
		],
	],
	'orders'            => [
		[
			'nom'         => 'Informations générales',
			'identifiant' => 'general',
			'create'      => true,
			'active'      => false,
		],
        [
            'nom'      => 'Options achetées',
            'relation' => 'purchases',
            'create'   => false,
            'active'   => false,
        ],
        [
            'nom'      => 'Forfait acheté',
            'relation' => 'purchasedSubs',
            'create'   => false,
            'active'   => false,
        ],
	],
];
