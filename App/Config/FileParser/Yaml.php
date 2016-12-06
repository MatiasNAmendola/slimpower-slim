<?php

namespace SlimPower\Config\FileParser;

use Exception;
use Symfony\Component\Yaml\Yaml as YamlParser;
use SlimPower\Config\Exception\ParseException;

/**
 * YAML file parser
 *
 * @package    SlimPower
 * @subpackage Config
 * @author     Matias Nahuel Améndola <soporte.esolutions@gmail.com>
 * @link       https://github.com/matiasnamendola/slimpower-slim
 * @license    MIT
 */
class Yaml implements FileParserInterface {

    /**
     * {@inheritDoc}
     * Loads a YAML/YML file as an array
     *
     * @throws ParseException If If there is an error parsing the YAML file
     */
    public function parse($path) {
        try {
            $data = YamlParser::parse(file_get_contents($path));
        } catch (Exception $exception) {
            throw new ParseException(
            array(
        'message' => 'Error parsing YAML file',
        'exception' => $exception,
            )
            );
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getSupportedExtensions() {
        return array('yaml', 'yml');
    }

}
