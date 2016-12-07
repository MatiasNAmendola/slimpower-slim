<?php

namespace SlimPower\Slim;

/**
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
class DownloadView extends \Slim\View {

    /**
     * Render -> Download file.
     * @param string $filepath Filepath
     * @param array|null $data
     * @return void
     * @throws \RuntimeException
     */
    protected function render($filepath, $data = NULL) {
        if (!file_exists($filepath)) {
            throw new \RuntimeException("View cannot render `$filepath` because the template does not exist");
        }

        $data = $this->getData();

        $contentType = 'application/octet-stream';

        if (isset($data["CONTENT_TYPE"])) {
            $cType = $data["CONTENT_TYPE"];

            if (!empty($cType)) {
                $contentType = $cType;
            }
        }

        $filename = basename($filepath);

        if (isset($data["FILENAME"])) {
            $fname = $data["FILENAME"];

            if (!empty($fname)) {
                $filename = $fname;
            }
        }

        header("HTTP/1.1 200 OK");
        header("Content-type: " . $contentType);
        header('Content-Transfer-Encoding: binary');
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Content-Length: " . filesize($filepath));
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        ob_clean();
        ob_start();
        readfile($filepath);
        return ob_get_clean();
    }

}
