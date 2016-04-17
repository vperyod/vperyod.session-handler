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

use Aura\Session\SessionFactory;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * SessionHandler
 *
 * @category Middleware
 * @package  Vperyod\SessionHandler
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/2016 MIT License
 * @link     https://github.com/vperyod/vperyod.session-handler
 */
class SessionHandler
{
    use SessionRequestAwareTrait;

    /**
     * Session factory
     *
     * @var SessionFactory
     *
     * @access protected
     */
    protected $sessionFactory;

    /**
     * Create a session handler
     *
     * @param SessionFactory $sessionFactory Session factory
     *
     * @access public
     */
    public function __construct(SessionFactory $sessionFactory = null)
    {
        $this->sessionFactory = $sessionFactory ?: new SessionFactory();
    }

    /**
     * Create Session stores it on the specified request attribute
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
        $request = $request->withAttribute(
            $this->getSessionAttribute(),
            $this->sessionFactory->newInstance($request->getCookieParams())
        );
        return $next($request, $response);
    }
}
