<?php

function createRoutes($locale)
{

	Route::get('search-component', [
		'as' => 'search-component',
		'uses' => 'SearchController@search'
	]);

	// Page « Master » : aperçu de tous les formulaires en onglets, sans inscription (Denis 21.06).
	Route::get('master', fn () => view('pages.master-forms'))->name('master-forms');

	// Page CONCLUSION : ouverte (obligatoirement) depuis le bas du 2350 (Denis 24.06).
	Route::get('conclusion', fn () => view('pages.conclusion'))->name('conclusion');

	Route::post('contactForm', [
		'as' => 'contactForm.post',
		'uses' => 'PageController@contactUs'
	])->where(['id' => '[0-9]+', 'slug' => '.*'])
		->middleware('recaptcha');

    Route::post('providers-contact', [
        'as' => 'providers.contact',
        'uses' => 'ProviderController@contact'
    ])->middleware('recaptcha');

    Route::post('providers-update', [
        'as' => 'providers.update',
        'uses' => 'ProviderController@update'
    ])->middleware('recaptcha');


    Route::get('delete-search/{id?}',[
		'as' => 'delete-search',
		'uses' => 'SavedSearchController@deleteSearch'
	]);

	Route::get('pause-subscription',[
		'as' => 'pause-subscription',
		'uses' => 'SubscriptionController@pause'
	]);

	Route::post('profile-option.add/{type}', [
		'as' => 'profile-option.add',
		'uses' => 'ProfileOptionController@add'
	]);

	// Édition d'un item d'option (form classique en modale ; pas de recaptcha — action authentifiée)
	Route::post('profile-option.update/{type}/{id}', [
		'as' => 'profile-option.update',
		'uses' => 'ProfileOptionController@update'
	]);


	Route::get('unpause-subscription',[
		'as' => 'unpause-subscription',
		'uses' => 'SubscriptionController@unpause'
	]);

	Route::get('cancel-subscription',[
		'as' => 'cancel-subscription',
		'uses' => 'SubscriptionController@cancel'
	]);

    Route::get('like-provider',[
        'as' => 'like-provider',
        'uses' => 'ProviderController@like'
    ]);

    Route::get('like-profession',[
        'as' => 'like-profession',
        'uses' => 'ProviderController@likeProfession'
    ]);

    Route::get('invoice/{token}',[
        'as' => 'invoice',
        'uses' => 'InvoiceController@download'
    ]);

    Route::get('license-list',[
        'as' => 'license-list',
        'uses' => 'ProfileOptionController@getLicenseList'
    ]);

    Route::get('diploma-list',[
        'as' => 'diploma-list',
        'uses' => 'ProfileOptionController@getDiplomaList'
    ]);

    Route::get('promotion-list',[
        'as' => 'promotion-list',
        'uses' => 'ProfileOptionController@getPromotionList'
    ]);

    Route::get('photo-list',[
        'as' => 'photo-list',
        'uses' => 'ProfileOptionController@getPhotoList'
    ]);

    Route::get('job-offer-list',[
        'as' => 'job-offer-list',
        'uses' => 'ProfileOptionController@getJobOfferList'
    ]);

    Route::get('promotion-toggle/{id?}',[
        'as' => 'promotion-toggle',
        'uses' => 'ProfileOptionController@promotionInProgressToggle'
    ]);

    Route::get('job-offer-toggle/{id?}',[
        'as' => 'job-offer-toggle',
        'uses' => 'ProfileOptionController@jobOfferActiveToggle'
    ]);

    Route::get('option-move/{type?}/{id?}/{dir?}',[
        'as' => 'option-move',
        'uses' => 'ProfileOptionController@moveOption'
    ]);

    Route::get('option-delete/{type?}/{id?}',[
        'as' => 'option-delete',
        'uses' => 'ProfileOptionController@deleteOption'
    ]);

    Route::post('edit-legend/{id?}',[
        'as' => 'edit-legend',
        'uses' => 'ProfileOptionController@editLegend'
    ]);

	Route::post('estimations.update', [
		'as' => 'estimations.update',
		'uses' => 'ProfileOptionController@updateEstimations'
	])->middleware('recaptcha');

	Route::post('url.update', [
		'as' => 'url.update',
		'uses' => 'ProfileOptionController@updateUrl'
	])->middleware('recaptcha');

    Route::get('attach-saved-search/{id}',[
        'as' => 'attach-saved-search',
        'uses' => 'SavedSearchController@attachSavedSearch'
    ]);

	Route::namespace('Auth')->prefix('subscriber')->group(static function () {
		Route::post('login', [
			'as'   => 'subscriber.login',
			'uses' => 'AuthController@postLogin'
		])->middleware('recaptcha');

		Route::any('logout', [
			'as'   => 'subscriber.logout',
			'uses' => 'AuthController@logout'
		]);
	});

	Route::post('evaluation-store', [
		'as' => 'evaluation.store',
		'uses' => 'EvaluationController@store'
	])->middleware('recaptcha');

	Route::post('evaluation-reply', [
		'as' => 'evaluation.reply',
		'uses' => 'EvaluationController@reply'
	])->middleware('recaptcha');

	if (config('app.allow_user_registration', false)) {
		/**
		 * Subscribers routes > Add a subscriber prefix to all call
		 */
		Route::namespace('Auth')->prefix('subscriber')->group(static function () {

			Route::post('lost', [
				'as' => 'subscriber.lost',
				'uses' => 'AuthController@lost'
			])->middleware('recaptcha');

			Route::any('reset', [
				'as'   => 'subscriber.reset',
				'uses' => 'AuthController@reset'
			]);

			Route::any('validate/{token?}', [
				'as'   => 'subscriber.validate',
				'uses' => 'AuthController@validateSubscriber'
			]);


			Route::post('update', [
				'as'   => 'subscriber.update',
				'uses' => 'AuthController@update'
			])->middleware('recaptcha');

			Route::post('update-password', [
				'as'   => 'subscriber.update-password',
				'uses' => 'AuthController@updatePassword'
			])->middleware('recaptcha');
		});

		// Lien de validation du courriel « lisible en français » (sans les mots
		// anglais subscriber/validate dans l'URL) — même contrôleur que
		// subscriber.validate. Utilisé pour les membres francophones.
		Route::any('valider-courriel/{token?}', [
			'as'   => 'subscriber.validate-fr',
			'uses' => 'Auth\\AuthController@validateSubscriber'
		]);

		Route::post('register-basic', [
			'as'   => 'subscriber.register.storeBasic',
			'uses' => 'SubscriberController@storeBasic'
		])->middleware('recaptcha');

		Route::post('update-step1', [
			'as'   => 'subscriber.profile.updateStep1',
			'uses' => 'SubscriberController@updateStep1'
		])->middleware('recaptcha');

		Route::post('update-step2', [
			'as'   => 'subscriber.profile.updateStep2',
			'uses' => 'SubscriberController@updateStep2'
		])->middleware('recaptcha');

		Route::post('update-step5', [
			'as'   => 'subscriber.profile.updateStep5',
			'uses' => 'SubscriberController@updateStep5'
		])->middleware('recaptcha');

		// Achat d'options APRÈS l'inscription (ajout au panier; paiement = activation).
		// recaptcha exigé par le FormBuilder maison (comme tous les forms register).
		Route::post('add-options', [
			'as'   => 'subscriber.profile.add-options.store',
			'uses' => 'SubscriberController@addOptions'
		])->middleware('recaptcha');

		Route::post('register-step1', [
			'as'   => 'subscriber.register.storeStep1',
			'uses' => 'SubscriberController@storeStep1'
		])->middleware('recaptcha');

		Route::post('register-step2', [
			'as'   => 'subscriber.register.storeStep2',
			'uses' => 'SubscriberController@storeStep2'
		])->middleware('recaptcha');

		Route::post('register-step3', [
			'as'   => 'subscriber.register.storeStep3',
			'uses' => 'SubscriberController@storeStep3'
		])->middleware('recaptcha');

		Route::post('register-step4', [
			'as'   => 'subscriber.register.storeStep4',
			'uses' => 'SubscriberController@storeStep4'
		])->middleware('recaptcha');

		Route::post('register-step5', [
			'as'   => 'subscriber.register.storeStep5',
			'uses' => 'SubscriberController@storeStep5'
		])->middleware('recaptcha');

		Route::post('register-step6', [
			'as'   => 'subscriber.register.storeStep6',
			'uses' => 'SubscriberController@storeStep6'
		])->middleware('recaptcha');

		Route::get('register-step2-service-form', [
			'as'   => 'subscriber.register.step2-service-form',
			'uses' => 'SubscriberController@step2ServiceForm'
		]);

		// Porte d'acceptation des frais de la fiche (feature #6) — AJAX, sans recaptcha
		// (appel programmatique; protégé par le jeton CSRF de la page)
		Route::post('register-accept-fiche-fee', [
			'as'   => 'subscriber.register.accept-fee',
			'uses' => 'SubscriberController@acceptFee'
		]);
	}

	if (config('cart.cart_type', null) === 'basic') {
		//Routes pour le cart
		Route::namespace('Cart')->prefix('cart')->group(static function () {

			Route::prefix('coupon')->group(static function () {

				Route::post('remove', [
					'as' => 'cart.coupon.remove',
					'uses' => 'BasicCartController@removeCoupon'
				])->middleware('recaptcha');

				Route::post('add', [
					'as' => 'cart.coupon.add',
					'uses' => 'BasicCartController@addCoupon'
				])->middleware('recaptcha');
			});

			Route::post('buy', [
				'as' => 'cart.buy',
				'uses' => 'BasicCartController@buy'
			])->middleware('recaptcha');

			Route::post('buy-without-paying', [
				'as' => 'cart.buy_without_paying',
				'uses' => 'BasicCartController@buyWithoutPaying'
			])->middleware('recaptcha');

			Route::post('empty', [
				'as' => 'cart.empty',
				'uses' => 'BasicCartController@empty'
			])->middleware('recaptcha');

			Route::delete('destroy', [
				'as' => 'cart.destroy',
				'uses' => 'BasicCartController@destroy'
			]);

			Route::post('store', [
				'as' => 'cart.store',
				'uses' => 'BasicCartController@store'
			])->middleware('recaptcha');

			Route::prefix('stripe')->group(static function () {

				Route::get('validate-checkout-session/{token}/{session_id?}', [
					'as' => 'cart.stripe.validate-checkout-session',
					'uses' => 'BasicCartController@validateStripeCheckoutSession'
				]);
			});
		});
	}
}
