<?php

namespace SlimPower\Config\FileParser;

use SlimPower\Config\Exception\ParseException;

/**
 * INI file parser
 *
 * @package    SlimPower
 * @subpackage Config
 * @author     Matias Nahuel Améndola <soporte.esolutions@gmail.com>
 * @link       https://github.com/matiasnamendola/slimpower-slim
 * @license    MIT
 */
class Ini implements FileParserInterface {

    /**
     * {@inheritDoc}
     * Parses an INI file as an array
     *
     * @throws ParseException If there is an error parsing the INI file
     */
    public function parse($path) {
        $data = @parse_ini_file($path, true);

        if (!$data) {
            $error = error_get_last();
            throw new ParseException($error);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getSupportedExtensions() {
        return array('ini');
    }

}
