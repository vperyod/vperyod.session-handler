<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler\Fake;

use Vperyod\SessionHandler\SessionRequestAwareTrait;

class FakeSessionRequestAware
{
    use SessionRequestAwareTrait;

    public function __call($name, $args)
    {
        return call_user_func_array([$this, $name], $args);
    }
}
