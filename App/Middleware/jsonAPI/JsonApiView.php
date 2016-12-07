<?php

namespace SlimPower\Middleware\jsonApi;

/**
 * jsonAPI - Slim extension to implement fast JSON API's
 *
 * @package    SlimPower
 * @subpackage View
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
 * JsonApiView - view wrapper for json responses (with error code).
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
class JsonApiView extends \Slim\View {

    /**
     * Bitmask consisting of <b>JSON_HEX_QUOT</b>,
     * <b>JSON_HEX_TAG</b>,
     * <b>JSON_HEX_AMP</b>,
     * <b>JSON_HEX_APOS</b>,
     * <b>JSON_NUMERIC_CHECK</b>,
     * <b>JSON_PRETTY_PRINT</b>,
     * <b>JSON_UNESCAPED_SLASHES</b>,
     * <b>JSON_FORCE_OBJECT</b>,
     * <b>JSON_UNESCAPED_UNICODE</b>.
     * The behaviour of these constants is described on
     * the JSON constants page.
     * @var int
     */
    public $encodingOptions = 0;

    /**
     * Content-Type sent through the HTTP header.
     * Default is set to "application/json",
     * append ";charset=UTF-8" to force the charset
     * @var string
     */
    public $contentType = 'application/json';

    /**
     *
     * @var string
     */
    private $dataWraper;

    /**
     *
     * @var string
     */
    private $metaWrapper;

    /**
     * 
     * @var bool
     */
    private $dataOnly = false;

    /**
     * Construct JsonApiView instance
     * @param type $dataWrapper (optional) Wrapper for data in response
     * @param type $metaWrapper (optional) Wrapper for metadata in response
     */
    public function __construct($dataWrapper = NULL, $metaWrapper = NULL) {
        parent::__construct();
        $this->dataWraper = $dataWrapper;
        $this->metaWrapper = $metaWrapper;
    }

    public function render($status = 200, $data = NULL) {
        $app = \Slim\Slim::getInstance();

        $status = intval($status);

        if ($this->dataWraper) {
            $response[$this->dataWraper] = $this->all();
        } else {
            $response = $this->all();
        }

        if (!$this->dataOnly) {
            //append error bool
            if ($status < 400) {
                if ($this->metaWrapper) {
                    $response[$this->metaWrapper]['error'] = false;
                } else {
                    $response['error'] = false;
                }
            } else {
                if ($this->metaWrapper) {
                    $response[$this->metaWrapper]['error'] = true;
                } else {
                    $response['error'] = true;
                }
            }

            //append status code
            if ($this->metaWrapper) {
                $response[$this->metaWrapper]['status'] = $status;
            } else {
                $response['status'] = $status;
            }

            //add flash messages
            if (isset($this->data->flash) && is_object($this->data->flash)) {
                $flash = $this->data->flash->getMessages();
                if ($this->dataWraper) {
                    unset($response[$this->dataWraper]['flash']);
                } else {
                    unset($response['flash']);
                }
                if (count($flash)) {
                    if ($this->metaWrapper) {
                        $response[$this->metaWrapper]['flash'] = $flash;
                    } else {
                        $response['flash'] = $flash;
                    }
                }
            }
        } else {
            unset($response['flash'], $response['status'], $response['error']);
        }

        $app->response()->status($status);
        $app->response()->header('Content-Type', $this->contentType);

        $jsonp_callback = $app->request->get('callback', null);

        if ($jsonp_callback !== null) {
            $app->response()->body($jsonp_callback . '(' . json_encode($response, $this->encodingOptions) . ')');
        } else {
            $app->response()->body(json_encode($response, $this->encodingOptions));
        }

        $app->stop();
    }

    /**
     * set whether to return only the data
     * @param bool $dataOnly  
     */
    public function dataOnly($dataOnly = true) {
        $this->dataOnly = $dataOnly;
    }

}
