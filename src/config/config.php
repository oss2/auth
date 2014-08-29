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
