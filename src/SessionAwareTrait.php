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
 * @category  Trait
 * @package   Vperyod\SessionHandler
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2016 Jake Johns
 * @license   http://jnj.mit-license.org/2016 MIT License
 * @link      https://github.com/vperyod/vperyod.session-handler
 */

namespace Vperyod\SessionHandler;

use Aura\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Session aware trait
 *
 * Trait for objects which need to know where the session attribute is stored in
 * the request and some helpers to interact with it.
 *
 * @category Trait
 * @package  Vperyod\SessionHandler
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/2016 MIT License
 * @link     https://github.com/vperyod/vperyod.session-handler
 */
trait SessionAwareTrait
{
    /**
     * Get session from request
     *
     * @param Request $request PSR7 Request
     *
     * @return Session\Session
     * @throws Exception if session attribute is invalid
     *
     * @access protected
     */
    protected function getSession(Request $request) : Session\Session
    {
        $session = $request->getAttribute(SessionHandler::SESSION_ATTRIBUTE);

        if (! $session instanceof Session\Session) {
            throw new \Exception(
                'Session not available in request at: '
                . SessionHandler::SESSION_ATTRIBUTE
            );
        }
        return $session;
    }

    /**
     * GetSessionSegment
     *
     * @param Request $request incoming request
     * @param string  $name    segment name
     *
     * @return Session\SegmentInterface
     *
     * @access protected
     */
    protected function getSessionSegment(
        Request $request,
        string $name
    ) : Session\SegmentInterface {
        $session = $this->getSession($request);
        return $session->getSegment($name);
    }
}
