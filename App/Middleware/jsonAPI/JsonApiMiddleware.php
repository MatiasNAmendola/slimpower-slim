<?php

namespace SlimPower\Middleware\jsonApi;

/**
 * jsonAPI - Slim extension to implement fast JSON API's
 *
 * @package    SlimPower
 * @subpackage Middleware
 * @author     Matias Nahuel Améndola <soporte.esolutions@gmail.com>
 * @link       https://github.com/matiasnamendola/slimpower-slim
 * @license    https://github.com/MatiasNAmendola/slimpower-slim/blob/master/LICENSE.md
 * @since      0.0.1
 * 
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * JsonApiMiddleware - Middleware that sets a bunch of static routes for easy bootstrapping of json API's
 *
 * @package    SlimPower
 * @subpackage View
 * @author     Matias Nahuel Améndola <soporte.esolutions@gmail.com>
 * @link       https://github.com/matiasnamendola/slimpower-slim
 * @license    https://github.com/MatiasNAmendola/slimpower-slim/blob/master/LICENSE.md
 * 
 * @since   0.0.1
 * 
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class JsonApiMiddleware extends \Slim\Middleware {

    /**
     * Sets a buch of static API calls
     * @param string $name The name of the Slim application
     */
    function __construct($name = 'default') {
        $app = \Slim\Slim::getInstance($name);
        $app->config('debug', false);

        // Mirrors the API request
        $app->get('/return', function() use ($app) {

            $app->render(200, array(
                'method' => $app->request()->getMethod(),
                'name' => $app->request()->get('name'),
                'headers' => $app->request()->headers(),
                'params' => $app->request()->params(),
            ));
        });

        // Generic error handler
        $app->error(function (Exception $e) use ($app) {

            if ($e->getCode()) {
                $errorCode = $e->getCode();
            } else {
                $errorCode = 500;
            }

            // Log error with the same message
            $message = \JsonApiMiddleware::_errorType($e->getCode()) . ": " . $e->getMessage();
            $app->getLog()->error($message . ' in ' . $e->getFile() . ' at line ' . $e->getLine());

            $app->render($errorCode, array(
                'msg' => $message,
            ));
        });

        // Not found handler (invalid routes, invalid method types)
        $app->notFound(function() use ($app) {
            $app->render(404, array(
                'msg' => 'Invalid route',
            ));
        });

        // Handle Empty response body
        $app->hook('slim.after.router', function () use ($app) {
            // Will allow download request to flow
            if ($app->response()->header('Content-Type') === 'application/octet-stream') {
                return;
            }

            if (strlen($app->response()->body()) == 0) {
                $app->render(500, array(
                    'msg' => 'Empty response',
                ));
            }
        });
    }

    /**
     * Call next
     */
    function call() {
        return $this->next->call();
    }

    static function _errorType($type = 1) {
        switch ($type) {
            default:
            case E_ERROR: // 1 //
                return 'ERROR';
            case E_WARNING: // 2 //
                return 'WARNING';
            case E_PARSE: // 4 //
                return 'PARSE';
            case E_NOTICE: // 8 //
                return 'NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'CORE_WARNING';
            case E_CORE_ERROR: // 64 //
                return 'COMPILE_ERROR';
            case E_CORE_WARNING: // 128 //
                return 'COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'USER_DEPRECATED';
        }
    }

}
