<?php

/**
 * ©[2017] SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.
 */

namespace Sugarcrm\REST\Tests\Client;

use Sugarcrm\REST\Client\SugarApi;
use Sugarcrm\REST\Endpoint\Metadata;
use Sugarcrm\REST\Tests\Stubs\Auth\SugarOAuthStub;


/**
 * Class SugarApiTest
 * @package Sugarcrm\REST\Tests\Client
 * @coversDefaultClass Sugarcrm\REST\Client\SugarApi
 * @group SugarApiTest
 */
class SugarApiTest extends \PHPUnit\Framework\TestCase {

    public static function setUpBeforeClass(): void {
        //Add Setup for static properties here
    }

    public static function tearDownAfterClass(): void {
        //Add Tear Down for static properties here
    }

    public function setUp(): void {
        parent::setUp();
    }

    public function tearDown(): void {
        parent::tearDown();
    }

    /**
     * @covers ::__construct
     * @covers ::init
     * @covers ::setAPIUrl
     */
    public function testConstructor() {
        $Client = new SugarApi();
        $this->assertNotEmpty($Client->getAuth());
        $this->assertNotEmpty($Client->getEndpointProvider());
        $this->assertEquals(10, $Client->getVersion());
        $this->assertEmpty($Client->getServer());
        $this->assertEmpty($Client->getAPIUrl());
        $Client = new SugarApi('localhost');
        $this->assertNotEmpty($Client->getAuth());
        $this->assertNotEmpty($Client->getEndpointProvider());
        $this->assertEquals(10, $Client->getVersion());
        $this->assertEquals('localhost', $Client->getServer());
        $this->assertEquals('http://localhost/rest/v10/', $Client->getAPIUrl());
        $Client = new SugarApi(
            'localhost',
            array(
                'username' => 'admin',
                'password' => 'asdf'
            )
        );
        $this->assertNotEmpty($Client->getAuth());
        $this->assertEquals(array(
            'username' => 'admin',
            'password' => 'asdf',
            'client_id' => 'sugar',
            'client_secret' => '',
            'platform' => 'base'
        ), $Client->getAuth()->getCredentials());
        $this->assertNotEmpty($Client->getEndpointProvider());
        $this->assertEquals(10, $Client->getVersion());
        $this->assertEquals('localhost', $Client->getServer());
        $this->assertEquals('http://localhost/rest/v10/', $Client->getAPIUrl());

        $Client->setVersion("11_4");
        $this->assertEquals("11_4", $Client->getVersion());
        $this->assertEquals('http://localhost/rest/v11_4/', $Client->getAPIUrl());
    }

    /**
     * @covers ::login
     */
    public function testLogin() {
        $Client = new SugarApi('localhost');
        $Auth = new SugarOAuthStub();
        $Client->setAuth($Auth);
        $this->assertEquals(true, $Client->login('admin', 'asdf'));
        $this->assertEquals(array(
            'username' => 'admin',
            'password' => 'asdf',
            'client_id' => 'sugar',
            'client_secret' => '',
            'platform' => 'base'
        ), $Client->getAuth()->getCredentials());
        $this->assertEquals(true, $Client->login('user1', 'asdf'));
        $this->assertEquals(array(
            'username' => 'user1',
            'password' => 'asdf',
            'client_id' => 'sugar',
            'client_secret' => '',
            'platform' => 'base'
        ), $Client->getAuth()->getCredentials());
        $this->assertEquals(true, $Client->login(NULL, 'abc123'));
        $this->assertEquals(array(
            'username' => 'user1',
            'password' => 'abc123',
            'client_id' => 'sugar',
            'client_secret' => '',
            'platform' => 'base'
        ), $Client->getAuth()->getCredentials());
        $this->assertEquals(true, $Client->login());
        $this->assertEquals(array(
            'username' => 'user1',
            'password' => 'abc123',
            'client_id' => 'sugar',
            'client_secret' => '',
            'platform' => 'base'
        ), $Client->getAuth()->getCredentials());
    }

    /**
     * @covers ::refreshToken
     */
    public function testRefreshToken() {
        $Client = new SugarApi('localhost');
        $Auth = new SugarOAuthStub();
        $Auth->setCredentials(array(
            'username' => '',
            'password' => '',
            'client_id' => 'sugar',
            'platform' => 'api'
        ));
        $Client->setAuth($Auth);
        $this->assertEquals(false, $Client->refreshToken());
        $Auth->setCredentials(array(
            'username' => '',
            'password' => '',
            'client_id' => 'sugar',
            'client_secret' => '',
            'platform' => 'api'
        ));
        $this->assertEquals(true, $Client->refreshToken());
    }

    /**
     * @covers ::logout
     */
    public function testLogout() {
        $Client = new SugarApi('localhost');
        $Auth = new SugarOAuthStub();
        $Client->setAuth($Auth);
        $this->assertEquals(true, $Client->logout());
    }

    // FIXME: mrussell to review
    // /**
    //  * @covers ::sudo
    //  */
    // public function testSudo() {
    //     $Client = new SugarApi('localhost');
    //     $Auth = new SugarOAuthStub();
    //     $Client->setAuth($Auth);
    //     $this->assertEquals(false, $Client->sudo('max'));
    // }

    // FIXME: mrussell to review
    // public function testEndpoints() {
    //     $Client = new SugarApi('localhost');
    //     $Auth = new SugarOAuthStub();
    //     $Client->setAuth($Auth);

    //     $Endpoint = $Client->bulk();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\Bulk', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest()->getBody()->getContents());

    //     $Endpoint = $Client->module();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\Module', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());

    //     $Endpoint = $Client->metadata();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\Metadata', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());

    //     $Endpoint = $Client->enum();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\Enum', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());

    //     $Endpoint = $Client->me();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\Me', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());

    //     $Endpoint = $Client->list();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\ModuleFilter', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());

    //     $Endpoint = $Client->oauth2Logout();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\OAuth2Logout', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());

    //     $Endpoint = $Client->oauth2Refresh();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\OAuth2Refresh', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());

    //     $Endpoint = $Client->oauth2Sudo();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\OAuth2Sudo', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());

    //     $Endpoint = $Client->oauth2Token();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\OAuth2Token', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());

    //     $Endpoint = $Client->ping();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\Ping', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());

    //     $Endpoint = $Client->search();
    //     $this->assertInstanceOf('\Sugarcrm\REST\Endpoint\Search', $Endpoint);
    //     $this->assertNotEmpty($Endpoint->getRequest());
    // }
}