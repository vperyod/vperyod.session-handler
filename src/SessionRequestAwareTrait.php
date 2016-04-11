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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Aura\Session\Session;

/**
 * Session request aware trait
 *
 * Trait for objects which need to know where the session attribute is stored in
 * the request.
 *
 * @category Trait
 * @package  Vperyod\SessionHandler
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/2016 MIT License
 * @link     https://github.com/vperyod/vperyod.session-handler
 */
trait SessionRequestAwareTrait
{
    /**
     * Session attribute
     *
     * @var string
     *
     * @access protected
     */
    protected $sessionAttribute = 'aura/session:session';

    /**
     * Set session attribute
     *
     * @param string $attr name of attribute for session
     *
     * @return $this
     *
     * @access public
     */
    public function setSessionAttribute($attr)
    {
        $this->sessionAttribute = $attr;
        return $this;
    }

    /**
     * Get session from request
     *
     * @param Request $request PSR7 Request
     *
     * @return Session
     * @throws InvalidArgumentException if session attribute is invalid
     *
     * @access protected
     */
    protected function getSession(Request $request)
    {
        $session = $request->getAttribute($this->sessionAttribute);
        if (! $session instanceof Session) {
            throw new Exception(
                'Session not available in request at: '
                . $this->sessionAttribute
            );
        }
        return $session;
    }
}
