<?php

/** @noinspection SuspiciousBinaryOperationInspection */
$cartRoutes = config('cart.cart_type') === 'basic' ? config('cartRoutes') : [];
$authRoutes = config('app.allow_user_registration') ? config('authRoutes') : [];

return array_replace_recursive(
	$authRoutes, //Login, register, profil, etc..
	$cartRoutes, //Cart, checkout, order, etc..
	[
	// Backend routes are now in crud.php

	/*
	|--------------------------------------------------------------------------
	| Routes Front-End
	|--------------------------------------------------------------------------
	|
	| Paramètres des routes pour les pages intégrées du front-end.
	|
	*/

		'front-end' => [
			'home'           => [
				'uri'   => [
					'fr' => '/',
					'en' => '/',
				],
				'view'  => 'pages.home',
				'uses'  => 'App\\Http\\Controllers\\SearchController@home',
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
						'title' => 'Accueil'
					],
					'en' => [
						'title' => 'Home'
					]
				]
			],
			'subscriptions'           => [
				'uri'   => [
					'fr' => '/nos-forfaits',
					'en' => '/our-offers',
				],
				'view'  => 'pages.subscriptions',
				'uses'  => 'App\\Http\\Controllers\\PageController@subscriptions',
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
						'title' => 'Nos forfaits'
					],
					'en' => [
						'title' => 'Our offers'
					]
				]
			],
			'public-subscriptions'           => [
				'uri'   => [
					'fr' => '/forfaits',
					'en' => '/offers',
				],
				'view'  => 'pages.subscriptions',
				'uses'  => 'App\\Http\\Controllers\\PageController@publicSubscriptions',
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
						'title' => 'Nos forfaits'
					],
					'en' => [
						'title' => 'Our offers'
					]
				]
			],
			/*'news-list'      => [
				'uri'   => [
					'fr' => '/nouvelles',
					'en' => '/news',
				],
				'view'  => 'core.pages.news-list',
				'uses'  => 'App\\Http\\Controllers\\PageController@newsList',
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
						'title' => 'Liste des nouvelles'
					],
					'en' => [
						'title' => 'News list'
					]
				]
			],
			'news'           => [
				'uri'           => [
					'fr' => '/nouvelle/{id}/{slug?}',
					'en' => '/news/{id}/{slug?}',
				],
				'translate_uri' => 'App\\Models\\Core\\News@translateRouteUri',
				'where'         => [
					'id'   => '[0-9]+',
					'slug' => '.*'
				],
				'view'          => 'core.pages.news',
				'uses'          => 'App\\Http\\Controllers\\PageController@news',
				'collection'    => 'news',
			],
			'basic-event'    => [
				'uri'           => [
					'fr' => '/evenement/{id}/{slug?}',
					'en' => '/event/{id}/{slug?}',
				],
				'translate_uri' => 'App\\Models\\Core\\BasicEvent@translateRouteUri',
				'where'         => [
					'id'   => '[0-9]+',
					'slug' => '.*'
				],
				'view'          => 'core.pages.basic-event',
				'uses'          => 'App\\Http\\Controllers\\PageController@basicEvent',
				'collection'    => 'basic_events',
			],
			'basic-events'   => [
				'uri'   => [
					'fr' => '/evenements',
					'en' => '/events',
				],
				'view'  => 'core.pages.basic-events',
				'uses'  => 'App\\Http\\Controllers\\PageController@basicEvents',
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
						'title' => 'Liste des évènements'
					],
					'en' => [
						'title' => 'Events list'
					]
				]
			],*/
			'term-of-use'    => [
				'uri'   => [
					'fr' => '/conditions-d-utilisation',
					'en' => '/term-of-use',
				],
				'view'  => 'pages.term-of-use',
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
						'title' => 'Conditions d\'utilisation'
					],
					'en' => [
						'title' => 'Term of use'
					]
				]
			],
			'search-results' => [
				'uri'   => [
					'fr' => '/recherche',
					'en' => '/search',
				],
				'view'  => 'core.pages.search-results',
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
						'title' => 'Recherche'
					],
					'en' => [
						'title' => 'Search'
					]
				]
			],
			'not-found'      => [
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
						'title' => 'Page introuvable'
					],
					'en' => [
						'title' => 'Page not found'
					]
				]
			],
			'installation' => [
				'uri'   => [
					'fr' => '/installation',
					'en' => '/installation',
				],
				'view' => 'core.pages.installation',
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
						'title' => 'Installation'
					],
					'en' => [
						'title' => 'Installation'
					]
				]
			],
            'provider-edit' => [
                'uri' => [
                    'fr' => 'formulaire-fournisseur',
                    'en' => 'provider-form',
                ],
                'view' => 'pages.providers.edit',
                'uses' => 'App\\Http\\Controllers\\ProviderController@edit',
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
                        'title' => 'Fiche fournisseur'
                    ],
                    'en' => [
                        'title' => 'Provider file'
                    ],
                    'restricted' => true
                ]
            ],
            'provider' => [
                'uri' => [
                    'fr' => 'fournisseur/{id}',
                    'en' => 'provider/{id}',
                ],
                'view' => 'pages.providers.show',
                'uses' => 'App\\Http\\Controllers\\ProviderController@show',
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
                        'title' => 'Fiche fournisseur'
                    ],
                    'en' => [
                        'title' => 'Provider file'
                    ]
                ]
            ],
            'providers-search' => [
                'uri' => [
                    'fr' => 'recherche-service',
                    'en' => 'service-search',
                ],
                'view' => 'pages.providers.search',
                'uses' => 'App\\Http\\Controllers\\ProviderController@search',
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
                        'title' => 'Trouver un service - Résultat de recherche'
                    ],
                    'en' => [
                        'title' => 'Find a service - Search results'
                    ]
                ]
            ],
            'edit-profile-options-licenses' => [
                'uri' => [
                    'fr' => 'modifier-option-profil/permis',
                    'en' => 'edit-profile-options/licenses',
                ],
                'view' => 'pages.profile-options.licenses',
                'uses' => 'App\\Http\\Controllers\\ProfileOptionController@editLicenses',
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
                        'title' => 'Modification des options de profils'
                    ],
                    'en' => [
                        'title' => 'Edit profile options'
                    ]
                ]
            ],
            'edit-profile-options-diplomas' => [
                'uri' => [
                    'fr' => 'modifier-option-profil/diplomes',
                    'en' => 'edit-profile-options/diplomas',
                ],
                'view' => 'pages.profile-options.diplomas',
                'uses' => 'App\\Http\\Controllers\\ProfileOptionController@editDiplomas',
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
                        'title' => 'Modification des options de profils'
                    ],
                    'en' => [
                        'title' => 'Edit profile options'
                    ]
                ]
            ],
            'edit-profile-options-estimations' => [
                'uri' => [
                    'fr' => 'modifier-option-profil/estimations',
                    'en' => 'edit-profile-options/estimations',
                ],
                'view' => 'pages.profile-options.estimations',
                'uses' => 'App\\Http\\Controllers\\ProfileOptionController@editEstimations',
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
                        'title' => 'Modification des options de profils'
                    ],
                    'en' => [
                        'title' => 'Edit profile options'
                    ]
                ]
            ],
            'edit-profile-options-job_offers' => [
                'uri' => [
                    'fr' => 'modifier-option-profil/offres-demploi',
                    'en' => 'edit-profile-options/job-offers',
                ],
                'view' => 'pages.profile-options.joboffers',
                'uses' => 'App\\Http\\Controllers\\ProfileOptionController@editJoboffers',
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
                        'title' => 'Modification des options de profils'
                    ],
                    'en' => [
                        'title' => 'Edit profile options'
                    ]
                ]
            ],
            'edit-profile-options-urls' => [
                'uri' => [
                    'fr' => 'modifier-option-profil/site-web',
                    'en' => 'edit-profile-options/url',
                ],
                'view' => 'pages.profile-options.url',
                'uses' => 'App\\Http\\Controllers\\ProfileOptionController@editUrl',
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
                        'title' => 'Modification des options de profils'
                    ],
                    'en' => [
                        'title' => 'Edit profile options'
                    ]
                ]
            ],
            'edit-profile-options-promotions' => [
                'uri' => [
                    'fr' => 'modifier-option-profil/promotions',
                    'en' => 'edit-profile-options/promotions',
                ],
                'view' => 'pages.profile-options.promotions',
                'uses' => 'App\\Http\\Controllers\\ProfileOptionController@editPromotions',
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
                        'title' => 'Modification des options de profils'
                    ],
                    'en' => [
                        'title' => 'Edit profile options'
                    ]
                ]
            ],
            'edit-profile-options-images' => [
                'uri' => [
                    'fr' => 'modifier-option-profil/photos',
                    'en' => 'edit-profile-options/photos',
                ],
                'view' => 'pages.profile-options.photos',
                'uses' => 'App\\Http\\Controllers\\ProfileOptionController@editPhotos',
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
                        'title' => 'Modification des options de profils'
                    ],
                    'en' => [
                        'title' => 'Edit profile options'
                    ]
                ]
            ],
			'options-list' => [
                'uri' => [
                    'fr' => 'options',
                    'en' => 'options',
                ],
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
                        'title' => 'Liste des options'
                    ],
                    'en' => [
                        'title' => 'List of options'
                    ]
                ]
            ],

			'profession' => [
                'uri' => [
                    'fr' => 'profession/{id}',
                    'en' => 'profession/{id}',
                ],
                'admin' => [
                    'title'        => true,
                    'metas'        => true,
                    'customs'      => true,
                    'right_column' => true,
                    'blocs'        => true,
                    'sharing'      => true,
                ],
				'view' => 'pages.profession',
                'uses' => 'App\\Http\\Controllers\\SearchController@profession',
                'page'  => [
                    'fr' => [
                        'title' => 'Profession'
                    ],
                    'en' => [
                        'title' => 'Profession'
                    ]
                ]
            ],
			'category' => [
                'uri' => [
                    'fr' => 'categorie/{id}',
                    'en' => 'category/{id}',
                ],
                'admin' => [
                    'title'        => true,
                    'metas'        => true,
                    'customs'      => true,
                    'right_column' => true,
                    'blocs'        => true,
                    'sharing'      => true,
                ],
				'view' => 'pages.category',
                'uses' => 'App\\Http\\Controllers\\SearchController@category',
                'page'  => [
                    'fr' => [
                        'title' => 'Catégorie'
                    ],
                    'en' => [
                        'title' => 'Category'
                    ]
                ]
            ],
		],
	]
);
