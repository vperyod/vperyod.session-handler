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
use Aura\Session\SegmentInterface as Segment;

/**
 * Session request aware trait
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
trait SessionRequestAwareTrait
{
    /**
     * Attribute on which to strore session object in request
     *
     * @var string
     *
     * @access protected
     */
    protected $sessionAttribute = 'aura/session:session';

    /**
     * Name of posted CSRF token form field
     *
     * @var string
     *
     * @access protected
     */
    protected $csrfName = '__csrf_token';

    /**
     * Name of request header storing CSRF token
     *
     * @var string
     *
     * @access protected
     */
    protected $csrfHeader = 'X-Csrf-Token';

    /**
     * Namespace for default message storage
     *
     * @var string
     *
     * @access protected
     */
    protected $messageSegmentName = 'vperyod/session-handler:messages';

    /**
     * Factory to create a messenger
     *
     * @var callable
     *
     * @access protected
     */
    protected $messengerFactory;

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
     * Get name of attribute where session is stored
     *
     * @return string
     *
     * @access protected
     */
    protected function getSessionAttribute()
    {
        return $this->sessionAttribute;
    }

    /**
     * Set name of form field containing CSRF token
     *
     * @param string $name name of CSRF form field
     *
     * @return $this
     *
     * @access public
     */
    public function setCsrfName($name)
    {
        $this->csrfName = $name;
        return $this;
    }

    /**
     * Get CSRF form field name
     *
     * @return string
     *
     * @access protected
     */
    protected function getCsrfName()
    {
        return $this->csrfName;
    }

    /**
     * Set CSRF header name
     *
     * @param string $header name of header for CSRF token
     *
     * @return $this
     *
     * @access public
     */
    public function setCsrfHeader($header)
    {
        $this->csrfHeader = $header;
        return $this;
    }

    /**
     * Get name of header for CSRF token
     *
     * @return string
     *
     * @access protected
     */
    protected function getCsrfHeader()
    {
        return $this->csrfHeader;
    }

    /**
     * Set namespace for default messenger storage
     *
     * @param string $name name of session segment
     *
     * @return $this
     *
     * @access public
     */
    public function setMessageSegmentName($name)
    {
        $this->messageSegmentName = $name;
        return $this;
    }

    /**
     * Get namespace for message segment
     *
     * @return string
     *
     * @access protected
     */
    protected function getMessageSegmentName()
    {
        return $this->messageSegmentName;
    }

    /**
     * Set factory for default messenger
     *
     * @param callable $factory factory to create a messenger
     *
     * @return $this
     *
     * @access public
     */
    public function setMessengerFactory(callable $factory)
    {
        $this->messengerFactory = $factory;
        return $this;
    }

    /**
     * Get factory to create a messenger
     *
     * @return callable
     *
     * @access protected
     */
    protected function getMessengerFactory()
    {
        if (null === $this->messengerFactory) {
            $this->messengerFactory = [$this, 'messengerFactory'];
        }
        return $this->messengerFactory;
    }

    /**
     * Get session from request
     *
     * @param Request $request PSR7 Request
     *
     * @return Session
     * @throws Exception if session attribute is invalid
     *
     * @access protected
     */
    protected function getSession(Request $request)
    {
        $session = $request->getAttribute($this->getSessionAttribute());
        if (! $session instanceof Session) {
            throw new Exception(
                'Session not available in request at: '
                . $this->sessionAttribute
            );
        }
        return $session;
    }

    /**
     * Get spec for CSRF form field
     *
     * @param Request $request PSR7 Request
     *
     * @return array
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

    /**
     * Get session segment for default messenger
     *
     * @param Request $request PSR7 Request
     *
     * @return Aura\Session\SegmentInterface
     *
     * @access protected
     */
    protected function getMessageSegment(Request $request)
    {
        return $this->getSession($request)
            ->getSegment($this->getMessageSegmentName());
    }

    /**
     * Create a new default messenger from the message segment
     *
     * @param Request $request PSR7 Request
     *
     * @return Messenger
     *
     * @access protected
     */
    protected function newMessenger(Request $request)
    {
        $factory = $this->getMessengerFactory();
        $segment = $this->getMessageSegment($request);
        return $factory($segment);
    }

    /**
     * Create a messenger, default messenger factory
     *
     * @param Segment $segment session segment
     *
     * @return Messenger
     *
     * @access protected
     */
    protected function messengerFactory(Segment $segment)
    {
        return new Messenger($segment);
    }
}
