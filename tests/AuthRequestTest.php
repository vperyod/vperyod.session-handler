<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class SessionRequestTest extends \PHPUnit_Framework_TestCase
{
    protected $session;

    public function testTriat()
    {
        $session = $this->getMockBuilder('Aura\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $req = ServerRequestFactory::fromGlobals()
            ->withAttribute('session', $session);

        $fake = new Fake\FakeSessionRequestAware;

        $fake->setSessionAttribute('session');

        $this->assertSame($session, $fake->proxyGetSession($req));
    }

    public function testError()
    {
        $this->setExpectedException('InvalidArgumentException');

        $req = ServerRequestFactory::fromGlobals();

        $fake = new Fake\FakeSessionRequestAware;

        $fake->proxyGetSession($req);
    }
}
