<?php

namespace SlimPower\Config\FileParser;

/**
 * Config file parser interface
 *
 * @package    SlimPower
 * @subpackage Config
 * @author     Matias Nahuel Améndola <soporte.esolutions@gmail.com>
 * @link       https://github.com/matiasnamendola/slimpower-slim
 * @license    MIT
 */
interface FileParserInterface {

    /**
     * Parses a file from `$path` and gets its contents as an array
     *
     * @param  string $path
     *
     * @return array
     */
    public function parse($path);

    /**
     * Returns an array of allowed file extensions for this parser
     *
     * @return array
     */
    public function getSupportedExtensions();
}
