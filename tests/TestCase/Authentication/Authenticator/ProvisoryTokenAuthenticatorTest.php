<?php

namespace ApiTokenAuthenticator\Test\Authentication\Authenticator;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\Http\ServerRequestFactory;
use Authentication\Authenticator\Result;
use Authentication\Identifier\IdentifierCollection;
use ApiTokenAuthenticator\Authentication\Authenticator\ProvisoryTokenAuthenticator;

class ProvisoryTokenAuthenticatorTest extends TestCase
{
    public $fixtures = ['plugin.ApiTokenAuthenticator.Users',];

    private $identifiers;
    private $request;
    private $tokenAuth;

    public function setUp(): void
    {
        parent::setUp();

        $this->identifiers = new IdentifierCollection([
            'Authentication.Token' => [
                'header' => 'Token',
            ],
        ]);
        $this->request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'rrd', 'password' => 'webmania']
        );

        $options = Configure::read('ApiTokenAuthenticator');
        $this->tokenAuth = new ProvisoryTokenAuthenticator($this->identifiers, $options);
    }

    public function testAuthenticateMissingHeaderToken()
    {
        $result = $this->tokenAuth->authenticate($this->request);
        $this->assertSame(Result::FAILURE_CREDENTIALS_MISSING, $result->getStatus());
    }

    public function testAuthenticateWithValidToken()
    {
        $requestWithHeader = $this->request->withAddedHeader('Token', 'token-1');
        $result = $this->tokenAuth->authenticate($requestWithHeader);
        $this->assertSame(Result::SUCCESS, $result->getStatus());
    }

    public function testAuthenticateWithInvalidToken()
    {
        $requestWithHeader = $this->request->withAddedHeader('Token', 'gauranga');
        $result = $this->tokenAuth->authenticate($requestWithHeader);
        $this->assertSame(Result::FAILURE_IDENTITY_NOT_FOUND, $result->getStatus());
    }
}