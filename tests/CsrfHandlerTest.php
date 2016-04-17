<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class CsrfHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $session;

    protected $handler;

    public function setUp()
    {
        parent::setUp();
        $this->handler = new CsrfHandler;

        $this->session = $this->getMockBuilder('Aura\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function providerHandler()
    {
        $request = ServerRequestFactory::fromGlobals();


        return [
            [$request->withMethod('GET'), 200],
            [$request->withMethod('PUT'), 400],
            [$request->withMethod('POST'), 400],
            [$request->withMethod('PATCH'), 400],
            [$request->withMethod('DELETE'), 400],
            [
                $request->withMethod('DELETE')->withAttribute('ignore-csrf', true),
                200
            ],
            [
                $request->withMethod('DELETE')
                    ->withParsedBody(['__csrf_token' => 'foo']),
                200, ['foo', true]
            ],
            [
                $request->withMethod('DELETE')
                    ->withHeader('X-Csrf-Token', 'foo'),
                200, ['foo', true]
            ],
            [
                $request->withMethod('DELETE')
                    ->withParsedBody(['__csrf_token' => 'foo']),
                400, ['foo', false]
            ],
        ];
    }

    /**
     * @dataProvider providerHandler
     */
    public function testHandler($request, $status, $token = null)
    {
        $request = $request->withAttribute('aura/session:session', $this->session);

        if ($token) {
            $this->expectToken($token);
        }

        $handler = $this->handler;
        $response = $handler(
            $request,
            new Response(),
            [$this, 'checkRequest']
        );

        $this->assertEquals(
            $status, $response->getStatusCode()
        );
    }

    public function expectToken($token)
    {
        $this->token = $this->getMockBuilder('Aura\Session\CsrfToken')
            ->disableOriginalConstructor()
            ->getMock();

        $this->session->expects($this->once())
            ->method('getCsrfToken')
            ->will($this->returnValue($this->token));

        $this->token->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo($token[0]))
            ->will($this->returnValue($token[1]));
    }

    public function checkRequest($request, $response)
    {
        $request;
        $response->getBody()->write('PASS');
        return $response;
    }
}
