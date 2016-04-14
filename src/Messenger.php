<?php
/**
 * Vperyod Session Handler
 *
 * PHP version 5
 *
 * Copyright (C) 2016 Jake Johns
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 *
 * @category  FlashMessenger
 * @package   Vperyod\SessionHandler
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2016 Jake Johns
 * @license   http://jnj.mit-license.org/2016 MIT License
 * @link      https://github.com/vperyod/vperyod.session-handler
 */

namespace Vperyod\SessionHandler;

use Aura\Session\SegmentInterface as Segment;

/**
 * Messenger
 *
 * @category FlashMessenger
 * @package  Vperyod\SessionHandler
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/ MIT License
 * @link     https://github.com/vperyod/vperyod.session-handler
 */
class Messenger
{
    /**
     * Default message levels
     */
    const LVL_SUCCESS = 'success';
    const LVL_INFO    = 'info';
    const LVL_WARNING = 'warning';

    /**
     * Session Segment
     *
     * @var Segment
     *
     * @access protected
     */
    protected $segment;

    /**
     * Create a flash messenger
     *
     * @param Segment $segment session storage
     *
     * @access public
     */
    public function __construct(Segment $segment)
    {
        $this->segment = $segment;
    }

    /**
     * Add a message
     *
     * @param string $message Message text
     * @param string $level   Message level
     *
     * @return $this
     *
     * @access public
     */
    public function add($message, $level = self::LVL_INFO)
    {
        $current = $this->segment->getFlash('messages', []);

        $current[] = [
            'message' => $message,
            'level'   => $level
        ];

        $this->segment->setFlash('messages', $current);

        return $this;
    }

    /**
     * Get current messages
     *
     * @return array
     *
     * @access public
     */
    public function getMessages()
    {
        return $this->segment->getFlash('messages');
    }

    /**
     * Set a success message
     *
     * @param string $message Message text
     *
     * @return $this
     *
     * @access public
     */
    public function success($message)
    {
        return $this->add($message, self::LVL_SUCCESS);
    }

    /**
     * Set an info message
     *
     * @param string $message Message text
     *
     * @return $this
     *
     * @access public
     */
    public function info($message)
    {
        return $this->add($message, self::LVL_INFO);
    }

    /**
     * Set a warning message
     *
     * @param string $message Message text
     *
     * @return $this
     *
     * @access public
     */
    public function warning($message)
    {
        return $this->add($message, self::LVL_WARNING);
    }
}
