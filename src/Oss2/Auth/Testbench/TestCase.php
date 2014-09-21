<?php namespace Oss2\Auth\Testbench;

/**
 * Oss2/Auth
 *
 * Testing class for Oss2 Auth - extends the Orchestra testbench.
 *
 * The only purpose of this class is for testing the authentication system. See the
 * tests directory in this package.
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
class TestCase extends \Orchestra\Testbench\TestCase
{

    /**
     * Set up our application:
     *
     * * set the authentication driver to 'oss2/auth'
     * * bind our test user provider
     * * disable logging
     *
     */
    public function createApplication()
    {
        $app = parent::createApplication();
        \Config::set( 'auth.driver', 'oss2/auth' );

        $app->bindShared('hash', function() { return new \Oss2\Auth\Hashing\PlaintextHasher; });

        $app->singleton( 'Oss2\Auth\UserProviderInterface', function(){
            return new \Oss2\Auth\Providers\FixedProvider( [], \Hash::getFacadeRoot() );
        });


        \Config::set( 'oss2/auth::log', false );

        return $app;
    }

    /**
     * Define what service providers this package provides
     */
    protected function getPackageProviders()
    {
        return array('Oss2\Auth\AuthServiceProvider');
    }

    /**
     * Refresh the HttpKernel client instance
     *
     * ### Origin
     *
     * When calling the same action more than once, a HTTP 404 error is generated.
     * For example, the following code results in the following exception:
     *
     * > Symfony\Component\HttpKernel\Exception\NotFoundHttpException: Controller method not found.
     *
     * ```php
     * $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
     * $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
     * ```
     *
     * Where as the following works:
     *
     * ```php
     * $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
     * $response = $this->call( 'POST', 'send-reset-token', [ 'username' => 'testusername' ] );
     * ```
     *
     * As I don't want to lose state (fake database backend), the following function resets the
     * client rather than the whole application allowing the following to succeed:
     *
     * ```php
     * $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
     * $this->resetClient();
     * $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
     * ```
     *
     */
    protected function refreshClient()
    {
        $this->client = $this->createClient();
    }


    /** @var \Oss2\Auth\User\FixedUser[] An array of test users */
    protected $users;

    /**
     * Set up our tests with:
     *
     * * an array of test users
     * * bind our test users to our provider
     */
    public function setUp()
    {
        parent::setUp();

        $this->users = [];
        $this->users[0] = new \Oss2\Auth\User\FixedUser;
        $this->users[0]->id        = '666';
        $this->users[0]->username  = 'testusername';
        $this->users[0]->password  = 'testpassword';
        $this->users[0]->email     = 'test@example.com';
        $this->users[0]->firstname = 'John';
        $this->users[0]->surname   = 'Doe';
        $this->users[0]->twoFA     = '\Oss2\Auth\TwoFactorAuthentication\Dummy';
        $this->users[0]->authSetAttempts( 0 );

        $this->users[1] = new \Oss2\Auth\User\FixedUser;
        $this->users[1]->id        = '667';
        $this->users[1]->username  = 'username667';
        $this->users[1]->email     = '667@example.com';

        $this->users[2] = new \Oss2\Auth\User\FixedUser;
        $this->users[2]->id        = '668';
        $this->users[2]->username  = 'username668';
        $this->users[2]->email     = '667@example.com';


        \App::make( 'Oss2\Auth\UserProviderInterface' )->setArray( $this->users );
    }

    /**
     * Get the array of test users (or a specific user)
     */
    public function getUsers( $i = null )
    {
        if( $i !== null ) {
            if( isset( $this->users[ $i ] ) )
                return $this->users[ $i ];

            return null;
        }

        return $this->users;
    }


    /**
     * Get application providers.
     *
     * **NB: We override this from Orchastra as we want to exlude the default Laravel AuthServiceProvider**
     *
     * @return array
     */
    protected function getApplicationProviders()
    {
        return [
            'Illuminate\Foundation\Providers\ArtisanServiceProvider',
            // 'Illuminate\Auth\AuthServiceProvider',
            'Illuminate\Cache\CacheServiceProvider',
            'Illuminate\Session\CommandsServiceProvider',
            'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
            'Illuminate\Routing\ControllerServiceProvider',
            'Illuminate\Cookie\CookieServiceProvider',
            'Illuminate\Database\DatabaseServiceProvider',
            'Illuminate\Encryption\EncryptionServiceProvider',
            'Illuminate\Filesystem\FilesystemServiceProvider',
            'Illuminate\Hashing\HashServiceProvider',
            'Illuminate\Html\HtmlServiceProvider',
            'Illuminate\Log\LogServiceProvider',
            'Illuminate\Mail\MailServiceProvider',
            'Illuminate\Database\MigrationServiceProvider',
            'Illuminate\Pagination\PaginationServiceProvider',
            'Illuminate\Queue\QueueServiceProvider',
            'Illuminate\Redis\RedisServiceProvider',
            'Illuminate\Remote\RemoteServiceProvider',
            'Illuminate\Auth\Reminders\ReminderServiceProvider',
            'Illuminate\Database\SeedServiceProvider',
            'Illuminate\Session\SessionServiceProvider',
            'Illuminate\Translation\TranslationServiceProvider',
            'Illuminate\Validation\ValidationServiceProvider',
            'Illuminate\View\ViewServiceProvider',
        ];
    }


    /**
     * Test a route and expect a HTTP exception.
     *
     * @see https://github.com/laravel/framework/issues/3979
     */
    public function assertHTTPExceptionStatus($expectedStatusCode, \Closure $codeThatShouldThrow, $data = null )
    {
        try
        {
            if( $data !== null )
                $codeThatShouldThrow($this,$data);
            else
                $codeThatShouldThrow($this);

            $this->assertFalse(true, "An HttpException should have been thrown by the provided Closure.");
        }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e)
        {
            // assertResponseStatus() won't work because the response object is null
            $this->assertEquals(
                $expectedStatusCode,
                $e->getStatusCode(),
                sprintf("Expected an HTTP status of %d but got %d.", $expectedStatusCode, $e->getStatusCode())
            );
        }
    }

}
