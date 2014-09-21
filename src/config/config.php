<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Register the route to the authenication controller
	|--------------------------------------------------------------------------
	|
	| Set to false if you want to manually register routes.
	*/
	'registerControllerRoute' => true,

	/*
	|--------------------------------------------------------------------------
	| \Input parameter names
	|--------------------------------------------------------------------------
	|
	| Names of the parameters that will be POSTed to a login attempt (or other
	| API call)
	|
	| You could change the remember parameter by setting it, for example, as:
	|     'remember' => 'rememberMe'
	*/
	'inputParamNames' => [
		'username' => 'username',
		'password' => 'password',
		'remember' => 'remember'
	],


	/*
	|--------------------------------------------------------------------------
	| Credential parameter names
	|--------------------------------------------------------------------------
	|
	| Names of the parameters that will be queried during a login attempt (or other
	| API call) using, for example, Auth::attempt()
	|
	| **NB:**
	*/
	'credentialParamNames' => [
		'username' => 'username',
		'password' => 'password'
	],

	/*
	|--------------------------------------------------------------------------
	| Logging enabled
	|--------------------------------------------------------------------------
	|
	| We log events via \Log by default. You can disable this by setting the
	| following to false and logging yourself using Event listeners.
	|
	| NB: Logging enabled by default. This option must exist and be false to disable
	*/
	'log' => true,


	/*
	|--------------------------------------------------------------------------
	| Login
	|--------------------------------------------------------------------------
	|
	*/
	'login' => [
		'paramFilter' => [ 'username', 'password' ],
		'paramRules'  => [
			'username' => ['required', 'min:5'],
			'password' => ['required', 'min:8']
		],
		'validator'   => '\Oss2\Auth\Validation\DefaultValidator',
		'2fa_enabled' => false, // to allow / check for 2FA per user, set true
		'2faTokenLifetime'              => '+10 minutes', // valid strtotime() argument

	],

	/*
	|--------------------------------------------------------------------------
	| 2FA Login
	|--------------------------------------------------------------------------
	|
	*/
	'login-2fa' => [
		'paramFilter' => [ 'username', 'twofatoken', 'token' ],
		'paramRules'  => [
			'username'   => ['required', 'min:5'],
			'twofatoken' => ['required', 'min:10'],
			'token'      => ['required']
		],
		'paramsForLookup' => ['username'],
	],

	/*
  	|--------------------------------------------------------------------------
   	| Password reset token request
 	|--------------------------------------------------------------------------
	|
	*/
	'send-reset-token' => [
		'paramFilter' => [ 'username' ],
		'paramRules'  => [
			'username' => ['required', 'min:5']
		],
		// If we receive unknown credentials in a request for a password reset
		// token, we can return with:
		//   - 204 -> same response as a valid request, no leakage of user data
		//   - 404 -> this leaks the fact that an account does not exist (and,
		//            hence by the absense of this resposne, that an account exists)
		'invalidCredentialsResponse' => 204,
		'maxTokens'                  => 5,         // max reset tokens per user
		'tokenLifetime'              => '+2 days', // valid strtotime() argument
	],


	/*
	|--------------------------------------------------------------------------
	| Password reset
	|--------------------------------------------------------------------------
	|
	*/
	'reset' => [
		'paramFilter' => [ 'username', 'token', 'password', 'password_confirmation' ],
		'paramRules'  => [
			'username'              => ['required', 'min:5'],
			'token'                 => ['required', 'min:8'],
			'password'              => ['required', 'min:8', 'confirmed'],
			'password_confirmation' => ['required', 'min:8']
		],
		'paramsForLookup' => ['username'],

	],


	/*
	|--------------------------------------------------------------------------
	| Find usernames request
	|--------------------------------------------------------------------------
	|
	| Note that we tend to send the usernames to queried email address and we
	| do not provide them in the response. We tend to always respond with a
	| 204 also to not leak information on whether an account exists or not.
	*/
	'find-usernames' => [
		'paramFilter' => [ 'email' ],
		'paramRules'  => [
			'email' => ['required', 'email']
		],
		// If we receive unknown credentials in a request for usernames, we can
		// return with:
		//   - 204 -> same response as a valid request, no leakage of user data
		//   - 404 -> this leaks the fact that an account does not exist (and,
		//            hence by the absense of this resposne, that an account exists)
		'invalidCredentialsResponse' => 204,
	],




	/*
	|--------------------------------------------------------------------------
	|--------------------------------------------------------------------------
	|--------------------------------------------------------------------------
	| EXTENSIONS - BY WAY OF EVENT LISTENERS
	|--------------------------------------------------------------------------
	|--------------------------------------------------------------------------
	|--------------------------------------------------------------------------
	*/
	'extensions' => [

		/*
		|--------------------------------------------------------------------------
		| Max failed login attempts
		|--------------------------------------------------------------------------
		|
		| If you want to lock out accounts after a number of failed login attempts,
		| set the following to true and configure accordingly. To disable, set to
		| false.
		|
		*/
		'maxFailed' => [

			'enabled'             => false,
			'class'               => '\\Oss2\\Auth\\Extensions\\MaxFailedExtension',
			'handler'             => '\\Oss2\\Auth\\Extensions\\Handlers\\MaxFailedHandler',
			'retrieved_function'  => 'handleCredentialsInvalid',
			'valid_function'      => 'handleCredentialsValid',
			'reset_function'      => 'handlePasswordReset',
			'max'                 => 5
		]

		/*
		|--------------------------------------------------------------------------
		| END EXTENSIONS - BY WAY OF EVENT LISTENERS
		|--------------------------------------------------------------------------
		*/
	]

];
