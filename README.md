# vperyod.session-handler
[Aura\Session] handler middleware

[![Latest version][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

## Installation
```
composer require vperyod/session-handler
```

## Usage
See [Aura\Session] documentation.

### Basic Session
```php
<?php
// Create handler, optionally passing Aura\SessionFactory instance
$handler = new Vperyod\SessionHandler\SessionHandler($sessionFactory);

// Optionally set the `SessionAttribute`, the name of the attribute on which to
// store the `Session` in the `Request`. Defaults to 'aura/session:session'
$handler->setSessionAttribute('session');

// Add to your middleware stack, radar, relay, etc.
$stack->middleware($handler);

// Subsequest dealings with `Request` will have the `Session` instance available at
// the previous specified atribute
$session = $request->getAttribute('session');


// The `SessionRequestAwareTrait` should make dealings easier.
//
// Have all your objects that deal with the session attribute on the request use
// the `SessionRequestAwareTrait` and have your DI container use the setter, so that
// they all know where the session object is stored.

class MyMiddleware
{
    use \Vperyod\SessionHandler\SessionRequestAwareTrait;

    public function __invoke($request, $response, $next)
    {
        $session = $this->getSession($request);
        // ... do stuff with session...
        return $next($request, $response);
    }
}

// Getting input for an action from a session
class MyInputExtractor
{

    use \Vperyod\SessionHandler\SessionRequestAwareTrait;

    public function __invoke($request)
    {
        return [
            'session' => $this->getSession($request),
            'data' => $request->getParsedBody()
        ];
    }
}

// Flash messaging in a responder
class MyAbstractResponder
{
    use \Vperyod\SessionHandler\SessionRequestAwareTrait;

    //...

    public function success()
    {
        $this->getSession($this->request)
            ->getSegment('My\\Messages')
            ->setFlash('message', 'You have Successfully Done Something!');
        return $this->redirect();
    }

    public function renderView()
    {
        $messages = $this->getSession($this->request)
            ->getSegment('My\\Messages')
            ->getFlash('message');
        $this->view->addData(['messages' => $messages]);
        // ...
    }
}
```

### CSRF Handler
```php
use \Vperyod\SessionHandler\SessionHandler;
use \Vperyod\SessionHandler\CsrfHandler;


$csrfFail = function ($request, $response, $next) {
    $response->getBody()->write('FAIL');
    return $response;
}

$session = new SessionHandler();
$csrf = new CsrfHandler($csrfFail); // optionally pass a fail responder callable

$stack->middleware($session); // make sure sesison handler is first
$stack->middleware($csrf);


// SessionRequestAwareTrait will provide methods for passing CSRF info to View

class MyAbstractResponder
{
    use SessionRequestAwareTrait;

    public function renderView()
    {
        // ...
        $this->view->addData(['csrf' => $this->getCsrfSpec()]);
        // [
        //      'type' => 'hidden',
        //      'name' => '__csrf_token',
        //      'value' => $csrfValue
        // ]
        // ...
    }
}

// in view using aura/html
$this->input($this->csrf);


```

[Aura\Session]: https://github.com/auraphp/Aura.Session

[ico-version]: https://img.shields.io/packagist/v/vperyod/session-handler.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/vperyod/vperyod.session-handler/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/vperyod/vperyod.session-handler.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/vperyod/vperyod.session-handler.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/vperyod/session-handler
[link-travis]: https://travis-ci.org/vperyod/vperyod.session-handler
[link-scrutinizer]: https://scrutinizer-ci.com/g/vperyod/vperyod.session-handler
[link-code-quality]: https://scrutinizer-ci.com/g/vperyod/vperyod.session-handler
