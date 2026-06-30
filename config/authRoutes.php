<?php
/*
|--------------------------------------------------------------------------
| Routes Front-end type Auth
|--------------------------------------------------------------------------
|
| Paramètres des routes pour les pages intégrées du front-end en rapport avec l'authentification.
|
*/
return [
	'front-end' => [
		'profile'         => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/profil',
				'en' => '/profile',
			],
			'uses'    => 'App\\Http\\Controllers\\Auth\\AuthController@profile',
			'view'    => 'core.pages.my-space',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			],
			'page'    => [
				'fr'         => [
					'title' => 'Profil',
				],
				'en'         => [
					'title' => 'Profile',
				],
				'restricted' => true
			]
		],
		'register'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/sinscrire',
				'en' => '/register',
			],
			'view'    => 'pages.register.basic',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@createBasic',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			]
		],
		'edit-step-1'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/fournisseur/edit/1',
				'en' => '/supplier/edit/1',
			],
			'view'    => 'pages.register.step-1',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@editStep1',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			],
			'page' => [
				'restricted' => true
			]
		],
		'edit-step-2'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/fournisseur/edit/2',
				'en' => '/supplier/edit/2',
			],
			'view'    => 'pages.register.step-2',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@editStep2',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			],
			'page' => [
				'restricted' => true
			]
		],
		'edit-step-5'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/fournisseur/modifier/5',
				'en' => '/supplier/edit/5',
			],
			'view'    => 'pages.register.step-5',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@editStep5',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
				'translations' => [
					'fr' => [
						'title' => 'Modifier les options de profil'
					],
					'en' => [
						'title' => 'Edit profile options'
					]
				]
			],
			'page' => [
				'restricted' => true
			]
		],
		'add-options'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/fournisseur/options/ajouter',
				'en' => '/supplier/options/add',
			],
			'view'    => 'pages.register.add-options',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@createAddOptions',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
				'translations' => [
					'fr' => [
						'title' => 'Ajouter des options'
					],
					'en' => [
						'title' => 'Add options'
					]
				]
			],
			'page' => [
				'restricted' => true
			]
		],
		'register-supplier-step-1'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/sinscrire/fournisseur',
				'en' => '/register/supplier',
			],
			// Bouton principal « Inscription fournisseur » → inscription SUR UNE SEULE PAGE
			// (Denis 28.06). L'ancien assistant 6 étapes reste accessible à /sinscrire/fournisseur/2…6.
			'view'    => 'pages.register.supplier-full',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@createSupplierFull',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			]
		],
		'register-supplier-step-2'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/sinscrire/fournisseur/2',
				'en' => '/register/supplier/2',
			],
			'view'    => 'pages.register.step-2',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@createStep2',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			]
		],
		'register-supplier-step-3'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/sinscrire/fournisseur/3',
				'en' => '/register/supplier/3',
			],
			'view'    => 'pages.register.step-3',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@createStep3',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			]
		],
		'register-supplier-step-4'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/sinscrire/fournisseur/4',
				'en' => '/register/supplier/4',
			],
			'view'    => 'pages.register.step-4',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@createStep4',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			]
		],
		'register-supplier-step-5'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/sinscrire/fournisseur/5',
				'en' => '/register/supplier/5',
			],
			'view'    => 'pages.register.step-5',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@createStep5',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			]
		],
		'register-supplier-step-6'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/sinscrire/fournisseur/6',
				'en' => '/register/supplier/6',
			],
			'view'    => 'pages.register.step-6',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@createStep6',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			]
		],
		'register-supplier-full'        => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/sinscrire/fournisseur-1page',
				'en' => '/register/supplier-1page',
			],
			'view'    => 'pages.register.supplier-full',
			'uses'    => 'App\\Http\\Controllers\\SubscriberController@createSupplierFull',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			]
		],
		'resiliation' => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/resiliation',
				'en' => '/cancellation',
			],
			'view'    => 'pages.resiliation',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			]
		],
		'ideologie' => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/ideologie-engagement',
				'en' => '/ideology-commitment',
			],
			'view'    => 'pages.ideologie',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			]
		],
		'lost-password'   => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/mot-de-passe-oublie',
				'en' => '/password-lost',
			],
			'view'    => 'core.auth.lost-password',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			],
			'page'    => [
				'fr' => [
					'title' => 'Mot de passe oublié',
				],
				'en' => [
					'title' => 'Password lost',
				],
			]
		],
		'reset-password'  => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/changer-de-mot-de-passe',
				'en' => '/reset-password',
			],
			'view'    => 'core.auth.reset-password',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			],
			'page'    => [
				'fr' => [
					'title' => 'Changer de mot de passe',
				],
				'en' => [
					'title' => 'Reset password',
				],
			]
		],
		'email-validated' => [
			'methods' => 'get',
			'uri'     => [
				'fr' => '/courriel-valide',
				'en' => '/email-validated',
			],
			'view'    => 'core.auth.email-validated',
			'admin'   => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => false,
				'blocs'        => true,
				'sharing'      => true,
			],
			'page'    => [
				'fr' => [
					'title' => 'Courriel validé',
				],
				'en' => [
					'title' => 'Email validated',
				],
			]
		],
		'become-member'   => [
			'uri'   => [
				'fr' => '/devenir-membre',
				'en' => '/become-member',
			],
			'view'  => 'core.pages.become-member',
			'uses'  => 'App\\Http\\Controllers\\Auth\\AuthController@subscriptionsList',
			'admin' => [
				'title'        => true,
				'metas'        => true,
				'customs'      => true,
				'right_column' => true,
				'blocs'        => true,
				'sharing'      => true,
			],
			'page'  => [
				'fr' => [
					'title' => 'Devenir membre'
				],
				'en' => [
					'title' => 'Become a member'
				]
			]
		],
	],
];
