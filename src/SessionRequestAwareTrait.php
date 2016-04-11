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
     * CsrfName
     *
     * @var string
     *
     * @access protected
     */
    protected $csrfName = '__csrf_token';

    /**
     * CsrfHeader
     *
     * @var string
     *
     * @access protected
     */
    protected $csrfHeader = 'X-Csrf-Token';

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
     * SetCsrfName
     *
     * @param mixed $name DESCRIPTION
     *
     * @return mixed
     *
     * @access public
     */
    public function setCsrfName($name)
    {
        $this->csrfName = $name;
        return $this;
    }

    /**
     * GetCsrfName
     *
     * @return mixed
     *
     * @access public
     */
    public function getCsrfName()
    {
        return $this->csrfName;
    }

    /**
     * SetCsrfHeader
     *
     * @param mixed $header DESCRIPTION
     *
     * @return mixed
     *
     * @access public
     */
    public function setCsrfHeader($header)
    {
        $this->csrfHeader = $header;
        return $this;
    }

    /**
     * GetCsrfHeader
     *
     * @return mixed
     *
     * @access public
     */
    public function getCsrfHeader()
    {
        return $this->csrfHeader;
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

    /**
     * GetCsrfSpec
     *
     * @param Request $request DESCRIPTION
     *
     * @return mixed
     *
     * @access protected
     */
    protected function getCsrfSpec(Request $request)
    {
        $value = $this->getSession($request)
            ->getCsrfToken()
            ->getValue();

        return [
            'type'  => 'hidden',
            'name'  => $this->getCsrfName(),
            'value' => $value,
        ];
    }
}
