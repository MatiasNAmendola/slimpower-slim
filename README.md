#SlimPower - Slim Controller

[![Latest version][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

An extension to [Slim Framework][1] that allows you use to dynamically
instantiated controllers with action methods wherever you would use a
closure or callback when routing.

The controller can optionally be loaded from Slim's DI container,
allowing you to inject dependencies as required.

Additionally, this extension implements json API's with great ease.

[1]: http://www.slimframework.com/

##Installation

In terminal:

```sh
    composer require matiasnamendola/slimpower-slim
```

Or you can add use this as your composer.json:

```json
    {
        "require": {
            "slim/slim": "2.*",
            "matiasnamendola/slimpower-slim": "dev-master"
        }
    }

```

###.htaccess sample
Here's an .htaccess sample for simple RESTful API's
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

##Usage - Dynamic controller instantiation

Use the string format `{controller class name}:{action method name}`
wherever you would usually use a closure:

e.g.

```php
    require 'vendor/autoload.php';
    
    $app = new \SlimPower\Slim();
    
    $app->get('/hello:name', 'App\IndexController:home');
```

You can also register the controller with Slim's DI container:

```php
    require 'vendor/autoload.php';
    
    $app = new \SlimPower\Slim();

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

    namespace ...;

    class IndexController
    {
        // Optional properties
        protected $app;
        protected $request;
        protected $response;

        public function index()
        {
            echo "This is the home page";
        }

        public function hello($name)
        {
            echo "Hello, $name";
        }

        // Optional setters
        public function setApp($app)
        {
            $this->app = $app;
        }

        public function setRequest($request)
        {
            $this->request = $request;
        }

        public function setResponse($response)
        {
            $this->response = $response;
        }

        // Init
        public function init()
        {
            // do things now that app, request and response are set.
        }
    }
```

##Usage - jsonAPI Middleware

To include the middleware and view you just have to load them using the default _Slim_ way.
Read more about Slim Here (https://github.com/codeguy/Slim#getting-started)

```php
    require 'vendor/autoload.php';

    $app = new \Slim\Slim();

    $app->view(new \SlimPower\Middleware\jsonApi\JsonApiView());
    $app->add(new \SlimPower\Middleware\jsonApi\JsonApiMiddleware());
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

####To make it possible just init JsonApiView with containers names
```php
    require 'vendor/autoload.php';

    $app = new \Slim\Slim();

    $app->view(new \SlimPower\Middleware\jsonApi\JsonApiView("resource", "meta"));
    $app->add(new \SlimPower\Middleware\jsonApi\JsonApiMiddleware());
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
        $app = \Slim\Slim::getInstance();
        $app->view(new \SlimPower\Middleware\jsonApi\JsonApiView());
        $app->add(new \SlimPower\Middleware\jsonApi\JsonApiMiddleware());
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

##Usage - Config

Config is a file configuration loader that supports PHP, INI, XML, JSON,
and YML files.

###Requirements

Config requires PHP 5.3+, and suggests using the [Symfony Yaml component](https://github.com/symfony/Yaml).

Config is designed to be very simple and straightforward to use. All you can do with
it is load, get, and set.

###Loading files

The `Config` object can be created via the factory method `load()`, or
by direct instantiation:

```php

<?php

use SlimPower\Config\Config;

// Load a single file
$conf = Config::load('config.json');
$conf = new Config('config.json');

// Load values from multiple files
$conf = new Config(array('config.json', 'config.xml'));

// Load all supported files in a directory
$conf = new Config(__DIR__ . '/config');

// Load values from optional files
$conf = new Config(array('config.dist.json', '?config.json'));

```

Files are parsed and loaded depending on the file extension. Note that when
loading multiple files, entries with **duplicate keys will take on the value
from the last loaded file**.

When loading a directory, the path is `glob`ed and files are loaded in by
name alphabetically.

###Getting values

Getting values can be done in three ways. One, by using the `get()` method:

```php

// Get value using key
$debug = $conf->get('debug');

// Get value using nested key
$secret = $conf->get('security.secret');

// Get a value with a fallback
$ttl = $conf->get('app.timeout', 3000);

```

The second method, is by using it like an array:

```php

// Get value using a simple key
$debug = $conf['debug'];

// Get value using a nested key
$secret = $conf['security.secret'];

// Get nested value like you would from a nested array
$secret = $conf['security']['secret'];

```

The third method, is by using the `all()` method:

```php

// Get all values
$data = $conf->all();

```

###Setting values

Although Config supports setting values via `set()` or, via the
array syntax, **any changes made this way are NOT reflected back to the
source files**. By design, if you need to make changes to your
configuration files, you have to do it manually.

```php

$conf = Config::load('config.json');

// Sample value from our config file
assert($conf['secret'] == '123');

// Update config value to something else
$conf['secret'] = '456';

// Reload the file
$conf = Config::load('config.json');

// Same value as before
assert($conf['secret'] == '123');

// This will fail
assert($conf['secret'] == '456');

```

###Using with default values

Sometimes in your own projects you may want to use Config for storing
application settings, without needing file I/O. You can do this by extending
the `AbstractConfig` class and populating the `getDefaults()` method:

```php

<?php

namespace ...;

use SlimPower\Config\AbstractConfig;

class MyConfig extends AbstractConfig
{
    protected function getDefaults()
    {
        return array(
            'host' => 'localhost',
            'port'    => 80,
            'servers' => array(
                'host1',
                'host2',
                'host3'
            ),
            'application' => array(
                'name'   => 'configuration',
                'secret' => 's3cr3t'
            )
        );
    }
}

```

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
