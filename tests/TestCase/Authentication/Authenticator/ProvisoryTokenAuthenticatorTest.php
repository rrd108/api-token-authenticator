<?php

declare(strict_types=1);

namespace ApiTokenAuthenticator\Test\Authentication\Authenticator;

use ApiTokenAuthenticator\Authentication\Authenticator\ProvisoryTokenAuthenticator;
use Authentication\Authenticator\Result;
use Authentication\Identifier\IdentifierCollection;
use Cake\Core\Configure;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;

class ProvisoryTokenAuthenticatorTest extends TestCase
{
    protected array $fixtures = ['plugin.ApiTokenAuthenticator.Users',];

    private $identifiers;
    private $request;
    private $tokenAuth;

    public function setUp(): void
    {
        parent::setUp();

        $this->identifiers = new IdentifierCollection(
            [
                'Authentication.Token' => [
                    'header' => 'Token',
                ],
            ]
        );
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

    public function testAuthenticateWithExpiredToken()
    {
        $options = Configure::read('ApiTokenAuthenticator');
        Configure::write('ApiTokenAuthenticator', $options + ['tokenExpiration' => 'token_expiration']);
        $tokenAuth = new ProvisoryTokenAuthenticator($this->identifiers, $options + ['tokenExpiration' => 'token_expiration']);

        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'rrdExpired', 'password' => 'webmania']
        );
        $requestWithHeader = $request->withAddedHeader('Token', 'token-2');
        $result = $tokenAuth->authenticate($requestWithHeader);
        $this->assertSame('TOKEN_EXPIRED', $result->getStatus());
    }

    public function testAuthenticateWithBearerToken()
    {
        $options = Configure::read('ApiTokenAuthenticator');
        $extraOptions = ['header' => 'Authorization', 'tokenPrefix' => 'Bearer'];
        Configure::write('ApiTokenAuthenticator', $extraOptions + $options);
        $tokenAuth = new ProvisoryTokenAuthenticator($this->identifiers, $extraOptions + $options);

        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => '/testpath'],
            [],
            ['username' => 'rrd', 'password' => 'webmania']
        );
        $requestWithHeader = $request->withAddedHeader('Authorization', 'Bearer token-1');
        $result = $tokenAuth->authenticate($requestWithHeader);
        $this->assertSame(Result::SUCCESS, $result->getStatus());
    }
}
