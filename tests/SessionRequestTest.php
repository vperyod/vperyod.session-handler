<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class SessionRequestTest extends \PHPUnit_Framework_TestCase
{
    protected $session;

    public function testGetSession()
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

    public function testCsrf()
    {
        $token = $this->getMockBuilder('Aura\Session\CsrfToken')
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this->getMockBuilder('Aura\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $session->expects($this->once())
            ->method('getCsrfToken')
            ->will($this->returnValue($token));

        $token->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('csrfValue'));

        $req = ServerRequestFactory::fromGlobals()
            ->withAttribute('session', $session);

        $fake = new Fake\FakeSessionRequestAware;

        $fake->setSessionAttribute('session');

        $this->assertSame('__csrf_token', $fake->getCsrfName());
        $this->assertSame('X-Csrf-Token', $fake->getCsrfHeader());
        $fake->setCsrfName('foo');
        $this->assertSame('foo', $fake->getCsrfName());
        $fake->setCsrfHeader('Foo');
        $this->assertSame('Foo', $fake->getCsrfHeader());

        $this->assertEquals(
            ['type' => 'hidden', 'name' => 'foo', 'value' => 'csrfValue'],
            $fake->proxyGetCsrfSpec($req)
        );

        $this->assertSame($session, $fake->proxyGetSession($req));
    }

    public function testError()
    {
        $this->setExpectedException('Vperyod\\SessionHandler\\Exception');

        $req = ServerRequestFactory::fromGlobals();

        $fake = new Fake\FakeSessionRequestAware;

        $fake->proxyGetSession($req);
    }
}
