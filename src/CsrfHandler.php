<?php
/**
 * Session Handler
 *
 * PHP version 5
 *
 * Copyright (C) 2016 Jake Johns
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 *
 * @category  Middleware
 * @package   Vperyod\SessionHandler
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2016 Jake Johns
 * @license   http://jnj.mit-license.org/2016 MIT License
 * @link      https://github.com/vperyod/vperyod.session-handler
 */

namespace Vperyod\SessionHandler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * CsrfHandler
 *
 * @category Middleware
 * @package  Vperyod\SessionHandler
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/2016 MIT License
 * @link     https://github.com/vperyod/vperyod.sesion-handler
 */
class CsrfHandler
{
    use SessionRequestAwareTrait;

    /**
     * FailResponder
     *
     * @var callable
     *
     * @access protected
     */
    protected $failResponder;

    /**
     * Create a CSRF Handler
     *
     * @param callabel $failResponder respond to failed CSRF check
     *
     * @access public
     */
    public function __construct(callable $failResponder = null)
    {
        $this->failResponder = $failResponder ?: [$this, 'fail'];
    }

    /**
     * Check non-idempotent and non-ignored requests and respond or continue
     *
     * @param Request  $request  PSR7 Request
     * @param Response $response PSR7 Response
     * @param callable $next     Next callable middleware
     *
     * @return Response
     *
     * @access public
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if ('GET' === $request->getMethod()) {
            return $next($request, $response);
        }

        $request = $this->withCsrfHeader($request);

        if ($this->ignore($request) || $this->isValid($request)) {
            return $next($request, $response);
        }

        $responder = $this->failResponder;
        return $responder($request, $response, $next);
    }

    /**
     * Check body for posted value, and move to request header
     *
     * @param Request $request PSR7 Request
     *
     * @return Request
     *
     * @access protected
     */
    protected function withCsrfHeader(Request $request)
    {
        $key  = $this->getCsrfName();
        $body = $request->getParsedBody();

        if (isset($body[$key])) {
            $value = $body[$key];
            unset($body[$key]);

            $request = $request
                ->withParsedBody($body)
                ->withHeader($this->getCsrfHeader(), $value);
        }

        return $request;
    }

    /**
     * Ignore this request?
     *
     * @param Request $request PSR7 Request
     *
     * @return bool
     *
     * @access protected
     */
    protected function ignore(Request $request)
    {
        return $request->getAttribute('ignore-csrf', false);
    }

    /**
     * Is CSRF Header Valid?
     *
     * @param Request $request PSR7 Request
     *
     * @return bool
     *
     * @access protected
     */
    protected function isValid(Request $request)
    {
        $key = $this->getCsrfHeader();
        $value = $request->getHeaderLine($key);
        return $value && $this->getSession($request)
            ->getCsrfToken()
            ->isValid($value);
    }

    /**
     * Respond to failed CSRF Check
     *
     * @param Request  $request  PSR7 Request
     * @param Response $response PSR7 Response
     *
     * @return Response
     *
     * @access protected
     */
    protected function fail(Request $request, Response $response)
    {
        $request;
        $response = $response
            ->withStatus(400)
            ->withHeader('Content-type', 'text/plain');

        $response->getBody()->write('CSRF Detected');
        return $response;
    }
}
