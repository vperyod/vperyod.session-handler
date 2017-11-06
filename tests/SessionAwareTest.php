<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class SessionAwareTest extends \PHPUnit_Framework_TestCase
{

    public function testHasSession()
    {
        $fake = new Fake\FakeSessionAware;

        $session = $this->getMockBuilder('Aura\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $request = ServerRequestFactory::fromGlobals()
            ->withAttribute(SessionHandler::SESSION_ATTRIBUTE, $session);

        $this->assertSame($session, $fake->getSession($request));

    }

    public function testHasSegment()
    {
        $fake = new Fake\FakeSessionAware;

        $session = $this->getMockBuilder('Aura\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $segment = $this->getMockBuilder('Aura\Session\SegmentInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $session->expects($this->once())
            ->method('getSegment')
            ->with('seg')
            ->will($this->returnValue($segment));

        $request = ServerRequestFactory::fromGlobals()
            ->withAttribute(SessionHandler::SESSION_ATTRIBUTE, $session);

        $this->assertSame($segment, $fake->getSessionSegment($request, 'seg'));

    }


    public function testHasNoSession()
    {
        $this->setExpectedException('Exception');
        $fake = new Fake\FakeSessionAware;
        $request = ServerRequestFactory::fromGlobals();
        $fake->getSession($request);
    }

}
