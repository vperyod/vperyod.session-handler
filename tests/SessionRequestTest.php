<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class SessionRequestTest extends \PHPUnit_Framework_TestCase
{
    use SessionTestTrait;

    protected $request;

    protected $fake;

    public function setUp()
    {
        $this->fake = new Fake\FakeSessionRequestAware;
    }

    public function configProvider()
    {
        return [
            ['SessionAttribute', 'aura/session:session'],
            ['CsrfName', '__csrf_token'],
            ['CsrfHeader', 'X-Csrf-Token'],
            ['MessageSegmentName', 'vperyod/session-handler:messages'],
        ];
    }

    /**
     * @dataProvider configProvider
     */
    public function testConfig($name, $default)
    {
        $get = 'get' . $name;
        $this->assertEquals(
            $default,
            $this->fake->$get()
        );

        $set = 'set' . $name;
        $this->assertSame(
            $this->fake,
            call_user_func([$this->fake, $set], 'foo')
        );

        $this->assertEquals(
            'foo',
            $this->fake->$get()
        );
    }

    public function testGetSession()
    {
        $this->mockSession();
        $this->assertSame(
            $this->session,
            $this->fake->getSession($this->request)
        );
    }

    public function testCsrf()
    {
        $this->mockSession();

        $token = $this->getMockBuilder('Aura\Session\CsrfToken')
            ->disableOriginalConstructor()
            ->getMock();

        $this->session->expects($this->once())
            ->method('getCsrfToken')
            ->will($this->returnValue($token));

        $token->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('csrfValue'));

        $this->assertEquals(
            [
                'type' => 'hidden',
                'name' => '__csrf_token',
                'value' => 'csrfValue'
            ],
            $this->fake->getCsrfSpec($this->request)
        );
    }

    public function testError()
    {
        $this->setExpectedException('Vperyod\\SessionHandler\\Exception');
        $req = ServerRequestFactory::fromGlobals();
        $this->fake->getSession($req);
    }

    public function testMessageSegment()
    {
        $this->mockSession();
        $this->mockMessages();

        $this->assertSame(
            $this->messages,
            $this->fake->getMessageSegment($this->request)
        );
    }

    public function testDefaultFactory()
    {
        $this->mockSession();
        $this->mockMessages();

        $this->assertTrue(is_callable($this->fake->getMessengerFactory()));

        $this->assertInstanceOf(
            'Vperyod\SessionHandler\Messenger',
            $this->fake->newMessenger($this->request)
        );
    }

    public function testMessengerFactory()
    {
        $this->mockSession();
        $this->mockMessages();

        $factory = $this->getMock(\stdClass::class, ['__invoke']);
        $factory->expects($this->once())
            ->method('__invoke')
            ->with($this->messages)
            ->will($this->returnValue('foo'));

        $this->assertSame(
            $this->fake,
            $this->fake->setMessengerFactory($factory)
        );

        $this->assertEquals(
            'foo',
            $this->fake->newMessenger($this->request)
        );

    }

}
