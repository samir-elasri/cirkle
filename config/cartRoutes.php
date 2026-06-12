<?php
/*
|--------------------------------------------------------------------------
| Routes Panier Front-end type BasicCart
|--------------------------------------------------------------------------
|
| Paramètres des routes pour les pages intégrées du front-end en rapport avec le BasicCart.
|
*/
return [
	'front-end' => [
		'cart'                  => [
			'uri'  => [
				'fr' => '/panier',
				'en' => '/cart',
			],
			'view' => 'core.pages.cart-list',
			'uses' => 'App\\Http\\Controllers\\Cart\\BasicCartController@cart',
			'page' => [
				'fr' => [
					'title' => 'Votre panier'
				],
				'en' => [
					'title' => 'Your cart'
				]
			]
		],
//		'checkout'              => [
//			'uri'   => [
//				'fr' => '/confirmation',
//				'en' => '/checkout',
//			],
//			'view'  => 'core.pages.checkout',
//			'uses'  => 'App\\Http\\Controllers\\Cart\\BasicCartController@checkout',
//			'admin' => [
//				'title'        => true,
//				'metas'        => true,
//				'customs'      => true,
//				'right_column' => true,
//				'blocs'        => true,
//				'sharing'      => true,
//			],
//			'page'  => [
//				'fr'         => [
//					'title' => 'Confirmation des éléments à acheter'
//				],
//				'en'         => [
//					'title' => 'Checkout'
//				],
//				'restricted' => true
//			],
//		],
		'purchase-confirmation' => [
			'uri'  => [
				'fr' => '/confirmation-d-achat',
				'en' => '/purchase-confirmation',
			],
			'view' => 'core.pages.payment-confirmed',
			'uses' => 'App\\Http\\Controllers\\Cart\\BasicCartController@purchaseConfirmation',
			'page' => [
				'fr' => [
					'title' => 'Confirmation d\'achat'
				],
				'en' => [
					'title' => 'Purchase confirmation'
				],
				'restricted' => true
			],
		],
		'order'                 => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/commande/{token}',
				'en' => '/order/{token}',
			],
			'uses'    => 'App\\Http\\Controllers\\Cart\\BasicCartController@order',
			'where'   => ['token' => '.+'],
			'view'    => 'core.pages.order-receipt',
			'page'    => [
				'fr'         => [
					'title' => 'Commande',
				],
				'en'         => [
					'title' => 'Order',
				],
				'restricted' => true
			]
		],
	]
];
