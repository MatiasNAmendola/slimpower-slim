#SlimPower - Slim

[![Latest version][ico-version]][link-packagist]
[comment]: # ([![Total Downloads][ico-downloads]][link-downloads])

[![Latest Stable Version](https://poser.pugx.org/matiasnamendola/slimpower-slim/version?format=flat-square)](https://packagist.org/packages/matiasnamendola/slimpower-slim) 
[![Latest Unstable Version](https://poser.pugx.org/matiasnamendola/slimpower-slim/v/unstable?format=flat-square)](//packagist.org/packages/matiasnamendola/slimpower-slim) 
[![Total Downloads](https://poser.pugx.org/matiasnamendola/slimpower-slim/downloads?format=flat-square)](https://packagist.org/packages/matiasnamendola/slimpower-slim) 
[![Monthly Downloads](https://poser.pugx.org/matiasnamendola/slimpower-slim/d/monthly?format=flat-square)](https://packagist.org/packages/matiasnamendola/slimpower-slim)
[![Daily Downloads](https://poser.pugx.org/matiasnamendola/slimpower-slim/d/daily?format=flat-square)](https://packagist.org/packages/matiasnamendola/slimpower-slim)
[![composer.lock available](https://poser.pugx.org/matiasnamendola/slimpower-slim/composerlock?format=flat-square)](https://packagist.org/packages/matiasnamendola/slimpower-slim)

An extension to [Slim Framework][1] that allows you use to dynamically
instantiated controllers with action methods wherever you would use a
closure or callback when routing.

The controller can optionally be loaded from Slim's DI container,
allowing you to inject dependencies as required.

Additionally, this extension implements Json Middleware & View with great ease.

[1]: http://www.slimframework.com/

##Installation

Look at [Installation File](INSTALLATION.md)

##Usage - Dynamic controller instantiation

Use the string format `{controller class name}:{action method name}`
wherever you would usually use a closure:

e.g.

```php
require 'vendor/autoload.php';

$app = new \SlimPower\Slim\Slim();

$app->get('/hello:name', 'App\IndexController:home');
```

You can also register the controller with Slim's DI container:

```php
<?php

require 'vendor/autoload.php';

$app = new \SlimPower\Slim\Slim();

$app->container->singleton('App\IndexController', function ($container) {
    // Retrieve any required dependencies from the container and
    // inject into the constructor of the controller

    return new \App\IndexController();
});

$app->get('/', 'App\IndexController:index');
```

###example controller

*SlimPower - Slim Controller* will call the controller's `setApp()`, `setRequest()`
and `setResponse()` methods if they exist and populate appropriately. It will
then call the controller's `init()`` method.

Hence, a typical controller may look like:

```php
<?php

namespace App;

class IndexController {
    // Optional properties
    protected $app;
    protected $request;
    protected $response;

    public function index() {
        echo "This is the home page";
    }

    public function hello($name) {
        echo "Hello, $name";
    }

    // Optional setters
    public function setApp($app) {
        $this->app = $app;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

    public function setResponse($response) {
        $this->response = $response;
    }

    // Init
    public function init() {
        // do things now that app, request and response are set.
    }
}
```

##Usage - Json Middleware

To include the middleware and view you just have to load them using the default _Slim_ way.
Read more about Slim Here (https://github.com/codeguy/Slim#getting-started)

```php
require 'vendor/autoload.php';

$app = new \SlimPower\Slim\Slim();

$app->view(new \SlimPower\Slim\Middleware\Json\JsonView());
$app->add(new \SlimPower\Slim\Middleware\Json\JsonMiddleware());
```

###example method

all your requests will be returning a JSON output.
the usage will be `$app->render( (int)$HTTP_CODE, (array)$DATA);`

####example code
 
```php

    $app->get('/', function() use ($app) {
        $app->render(200, array(
            'msg' => 'welcome to my API!',
        ));
    });

```

####example output

```json
{
    "msg":"welcome to my API!",
    "error":false,
    "status":200
}
```

###Errors

To display an error just set the `error => true` in your data array.
All requests will have an `error` param that defaults to false.

```php
$app->get('/user/:id', function($id) use ($app) {

    //your code here

    $app->render(404, array(
        'error' => TRUE,
        'msg'   => 'user not found',
    ));
});
```
```json
{
    "msg":"user not found",
    "error":true,
    "status":404
}
```

You can optionally throw exceptions, the middleware will catch all exceptions and display error messages.

```php
$app->get('/user/:id', function($id) use ($app) {

    //your code here

    if(...){
        throw new \Exception("Something wrong with your request!");
    }
});
```

```json
{
    "error": true,
    "msg": "ERROR: Something wrong with your request!",
    "status": 500
}
```

###Embedding response data and metadata in separate containers

It is possible to separate response metadata and business information in separate containers.

####To make it possible just init JsonView with containers names

```php
require 'vendor/autoload.php';

$app = new \SlimPower\Slim\Slim();

$app->view(new \SlimPower\Slim\Middleware\Json\JsonView("resource", "meta"));
$app->add(new \SlimPower\Slim\Middleware\Json\JsonMiddleware());
```

####Response
```json
{
    "resource":{
        "msg":"welcome to my API!"
    },
    "meta":{
        "error":false,
        "status":200
    }
}
```

###routing specific requests to the API

If your site is using regular HTML responses and you just want to expose an API point on a specific route,
you can use Slim router middlewares to define this.

```php
function jsonResponse(){
    $app = \SlimPower\Slim\Slim::getInstance();
    $app->view(new \SlimPower\Slim\Middleware\Json\JsonView());
    $app->add(new \SlimPower\Slim\Middleware\Json\JsonMiddleware());
}

$app->get('/home',function() use($app){
    //regular html response
    $app->render("template.tpl");
});

$app->get('/api','jsonResponse',function() use($app){
    //this request will have full json responses

    $app->render(200, array(
        'msg' => 'welcome to my API!',
    ));
});
```


###middleware

The middleware will set some static routes for default requests.
**if you dont want to use it**, you can copy its content code into your bootstrap file.

***IMPORTANT: remember to use `$app->config('debug', false);` or errors will still be printed in HTML***

##Security

If you discover any security related issues, please email [soporte.esolutions@gmail.com](mailto:soporte.esolutions@gmail.com?subject=[SECURITY] Config Security Issue) instead of using the issue tracker.

##Credits

- [Matías Nahuel Améndola](https://github.com/matiasnamendola)
- [Franco Soto](https://github.com/francosoto)

##License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/MatiasNAmendola/slimpower-slim.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/MatiasNAmendola/slimpower-slim.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/matiasnamendola/slimpower-slim
[link-downloads]: https://packagist.org/packages/matiasnamendola/slimpower-slim

##Example project

Look at [slimpower-slim-example](https://github.com/matiasnamendola/slimpower-slim-example).
