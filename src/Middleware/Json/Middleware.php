<?php

namespace SlimPower\Slim\Middleware\Json;

/**
 * Middleware that sets a bunch of static routes for easy bootstrapping of json API's
 *
 * @package    SlimPower
 * @subpackage Slim
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
class Middleware extends \Slim\Middleware {

    /**
     * Constructor
     * @param \SlimPower\Slim\Slim $app SlimPower instance
     * @param array $config Config
     */
    function __construct(\SlimPower\Slim\Slim $app, array $config = null) {
        $this->app = $app;

        $defaultConfig = $this->getDefaultConfig();

        if (\is_array($config)) {
            $config = array_merge($defaultConfig, $config);
        } else {
            $config = $defaultConfig;
        }

        $app->config($config);

        $dataWrapper = $app->config(Config::DataWraper);
        $metaWrapper = $app->config(Config::MetaWrapper);

        $app->view(new View($app, $dataWrapper, $metaWrapper));

        $this->catchErrors();
        $this->catchNotFound();

        if ($app->config(Config::ClearData)) {
            $app->view->clear();
        }

        $app->hook('slim.after.router', function () use ($app) {
            if ($app->response()->header('Content-Type') === 'application/octet-stream') {
                return;
            }

            $cors = $app->config(Config::Cors);

            if ($cors) {
                if (\is_callable($cors)) {
                    $allowOrigin = \call_user_func($cors, $app->request()->headers->get('Origin'));
                } else {
                    if (!\is_string($cors)) {
                        $allowOrigin = '*';
                    } else {
                        $allowOrigin = $cors;
                    }
                }

                if ($allowOrigin) {
                    $app->response()->header('Access-Control-Allow-Origin', $allowOrigin);
                }
            }

            if ($app->config(Config::Protect)) {
                $app->response()->body('while(1);' . $app->response()->body());
            }
        });
    }

    private function getDefaultConfig() {
        $defaultConfig = array(
            'debug' => false, // Disable PrettyException middleware
            Config::Debug => false,
            Config::Status => false,
            Config::OverrideError => false,
            Config::OverrideNotFound => false,
            Config::Protect => false,
            Config::Cors => false,
            Config::ClearData => false,
            Config::JsonEncodeOptions => 0
        );

        return $defaultConfig;
    }

    private function catchErrors() {
        $app = $this->app;

        $overrideError = $app->config(Config::OverrideError);

        if (!$overrideError) {
            return;
        }

        $app->error(function (\Exception $e) use ($app, $overrideError) {
            if (\is_callable($overrideError)) {
                $func = $overrideError;
            }
            $return = array(
                'error' =>
                isset($func) ? \call_user_func($func, $e) : ($e->getCode() ? '(#' . $e->getCode() . ') ' : '') . $e->getMessage()
            );
            if ($app->config(Config::Debug)) {
                $return['_debug'] = array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace(),
                );
            }

            $app->render(500, $return);
        });
    }

    private function catchNotFound() {
        $app = $this->app;

        $overrideNotFound = $app->config(Config::OverrideNotFound);

        if (!$overrideNotFound) {
            return;
        }

        $app->notFound(function() use ($app, $overrideNotFound) {
            if (\is_callable($overrideNotFound)) {
                $func = $overrideNotFound;
            }

            $return = array(
                'error' =>
                isset($func) ? \call_user_func($func, $app->request()) : '\'' . $app->request()->getPath() . '\' is not found.'
            );

            $app->render(404, $return);
        });
    }

    public function call() {
        $this->next->call();
    }

    private function setConfigFunction($config, $func) {
        if (\is_callable($func) || \is_bool($func)) {
            $this->app->config($config, $func);
            return true;
        } else {
            return false;
        }
    }

    public function setErrorMessage($func) {
        return $this->setConfigFunction(Config::OverrideError, $func);
    }

    public function setNotFoundMessage($func) {
        return $this->setConfigFunction(Config::OverrideNotFound, $func);
    }

    static public function inject() {
        $args = \func_get_args();
        $app = \SlimPower\Slim\Slim::getInstance();
        $config = null;

        foreach ($args as $arg) {
            if ($arg instanceof \SlimPower\Slim\Slim) {
                $app = $arg;
            }

            if (\is_array($arg)) {
                $config = $arg;
            }
        }

        $app->add(new Middleware($config));
    }

}
