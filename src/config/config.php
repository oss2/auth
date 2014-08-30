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
			'username' => ['required','min:3']
		],
		'validator'   => '\Oss2\Auth\Validation\LoginValidator',
	],

	/*
  	|--------------------------------------------------------------------------
   	| Password reset
 	|--------------------------------------------------------------------------
	|
	*/
	'reset' => [
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
