<?php namespace Oss2\Auth;

use Illuminate\Support\ServiceProvider;

/**
 * Oss2/Auth
 *
 * Laravel authentication with frontend API / pages and enhanced with extensions
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.md.
 *
 * @category   Authentication
 * @package    Oss2\Auth
 * @copyright  Copyright (c) 2014, Open Source Solutions Limited, Dublin, Ireland
 */
class AuthServiceProvider extends ServiceProvider {

	/** @var bool Indicates if loading of the provider is deferred */
	protected $defer = false;

	/** @var array Configuration from file */
    protected $config = null;


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// copied from \Illuminate\Auth\AuthServiceProvider
		$this->app->bindShared('auth', function($app)
		{
			// Once the authentication service has actually been requested by the developer
			// we will set a variable in the application indicating such. This helps us
			// know that we need to set any queued cookies in the after event later.
			$app['auth.loaded'] = true;

			return new \Illuminate\Auth\AuthManager($app);
		});

	}

	public function boot()
	{
		$this->package( 'oss2/auth', 'oss2/auth' );

		// Register our authentication guard
        \Auth::extend( 'oss2/auth', function() {
            return new \Oss2\Auth\Guard( \App::make('Oss2\Auth\Provider'), \App::make('session.store') );
        });

		if( \Config::get( 'oss2/auth::registerControllerRoute', false ) )
			\Route::controller( 'auth', 'Oss2\\Auth\\Controller\\Auth' );

		$this->registerExtensions();
	}

	public function registerExtensions()
	{
		foreach( \Config::get( 'oss2/auth::extensions', [] ) as $name => $extension )
			\Auth::addExtension( $name, $extension );
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		// copied from \Illuminate\Auth\AuthServiceProvider
		return array('auth');
	}

}
