<?php

namespace SlimPower\Config\FileParser;

/**
 * Abstract file parser
 *
 * @package    SlimPower
 * @subpackage Config
 * @author     Matias Nahuel Améndola <soporte.esolutions@gmail.com>
 * @link       https://github.com/matiasnamendola/slimpower-slim
 * @license    MIT
 */
abstract class AbstractFileParser implements FileParserInterface {

    /**
     * Path to the config file
     *
     * @var string
     */
    protected $path;

    /**
     * Sets the path to the config file
     *
     * @param string $path
     *
     * @codeCoverageIgnore
     */
    public function __construct($path) {
        $this->path = $path;
    }

}
