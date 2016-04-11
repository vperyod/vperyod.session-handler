<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler\Fake;

use Vperyod\SessionHandler\SessionRequestAwareTrait;

class FakeSessionRequestAware
{
    use SessionRequestAwareTrait;

    public function proxyGetSession($request)
    {
        return $this->getSession($request);
    }

    public function proxyGetCsrfSpec($request)
    {
        return $this->getCsrfSpec($request);
    }
}
