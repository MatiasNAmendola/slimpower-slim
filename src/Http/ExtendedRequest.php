<?php

namespace SlimPower\Slim\Http;

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
class ExtendedRequest extends \Slim\Http\Request {

    /**
     * List of uploaded files
     *
     * @var UploadedFileInterface[]
     */
    protected $uploadedFiles;
    protected $error = '';

    /**
     * Constructor
     * @param \Slim\Environment $env
     */
    public function __construct(\Slim\Environment $env) {
        parent::__construct($env);
        $this->uploadedFiles = UploadedFile::createFromEnvironment($env);
    }

    /**
     * File Params
     */

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     */
    public function getUploadedFiles() {
        return $this->uploadedFiles;
    }

    public function qtyUploadedFiles() {
        return count($this->uploadedFiles);
    }

    public function qtyUploadedFilesByKey($key) {
        if (isset($this->uploadedFiles[$key])) {
            return count($this->uploadedFiles[$key]);
        }

        return 0;
    }

    public function isArray($key) {
        if (isset($this->uploadedFiles[$key])) {
            try {
                return is_array(@array_values($this->uploadedFiles[$key]));
            } catch (Exception $e) {
                
            }
        }

        return false;
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     * @return self
     * @throws \InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles) {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;
        return $clone;
    }

    /**
     * Get error
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    private function getErrorMessage($newfile) {
        //http://php.net/manual/es/features.file-upload.errors.php
        $upload_errors = array(
            0 => "There is no error, the file uploaded with success"
            , UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini"
            , UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"
            , UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded"
            , UPLOAD_ERR_NO_FILE => "No file was uploaded"
        );

        if (version_compare(PHP_VERSION, '5.0.3') >= 0) {
            $upload_errors[UPLOAD_ERR_NO_TMP_DIR] = "Missing a temporary folder";
        }


        if (version_compare(PHP_VERSION, '5.1.0') >= 0) {
            $upload_errors[UPLOAD_ERR_CANT_WRITE] = "Failed to write to disk";
        }


        if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
            $upload_errors[UPLOAD_ERR_EXTENSION] = "File upload stopped by extension";
        }

        $error = $newfile->getError();

        if (array_key_exists($error, $upload_errors)) {
            return $upload_errors[$error];
        }

        return "Unknown error";
    }

    public function validUploadedFile($field = 'fileupload', $contentType = null) { // 'application/pdf'
        if ($this->qtyUploadedFilesByKey($field) == 0) {
            $this->error = "No files uploaded.";
            return false;
        }

        if ($this->qtyUploadedFilesByKey($field) > 1) {
            $this->error = "Cannot upload more than 1 file.";
            return false;
        }

        $files = $this->getUploadedFiles();
        $newfile = $this->isArray($field) ? $files[$field][0] : $files[$field];

        if ($newfile->getError() !== UPLOAD_ERR_OK) {
            $this->error = $this->getErrorMessage($newfile);
            return false;
        }

        if ($newfile->getSize() == 0) {
            $this->error = "Uploaded file is empty.";
            return false;
        }

        if (is_null($contentType) || !is_string($contentType) || empty($contentType)) {
            return $newfile;
        }

        if ($newfile->getClientMediaType() != $contentType) {
            $this->error = "Uploaded file is incorrect.";
            return false;
        }

        return $newfile;
    }

}
