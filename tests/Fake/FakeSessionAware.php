<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler\Fake;

use Vperyod\SessionHandler\SessionAwareTrait;

class FakeSessionAware
{
    use SessionAwareTrait;

    public function __call($name, $args)
    {
        return call_user_func_array([$this, $name], $args);
    }
}
