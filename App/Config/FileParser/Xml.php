<?php

namespace SlimPower\Config\FileParser;

use SlimPower\Config\Exception\ParseException;

/**
 * XML file parser
 *
 * @package    SlimPower
 * @subpackage Config
 * @author     Matias Nahuel Améndola <soporte.esolutions@gmail.com>
 * @link       https://github.com/MatiasNAmendola/slimpower-slim
 * @license    MIT
 */
class Xml implements FileParserInterface {

    /**
     * {@inheritDoc}
     * Parses an XML file as an array
     *
     * @throws ParseException If there is an error parsing the XML file
     */
    public function parse($path) {
        libxml_use_internal_errors(true);

        $data = simplexml_load_file($path, null, LIBXML_NOERROR);

        if ($data === false) {
            $errors = libxml_get_errors();
            $latestError = array_pop($errors);
            $error = array(
                'message' => $latestError->message,
                'type' => $latestError->level,
                'code' => $latestError->code,
                'file' => $latestError->file,
                'line' => $latestError->line,
            );
            throw new ParseException($error);
        }

        $data = json_decode(json_encode($data), true);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getSupportedExtensions() {
        return array('xml');
    }

}
