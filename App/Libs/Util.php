<?php

namespace SlimPower\Libs;

/**
 * Utils
 *
 * @package    SlimPower
 * @subpackage Libs
 * @author     Matias Nahuel Améndola <soporte.esolutions@gmail.com>
 * @link       https://github.com/matiasnamendola/slimpower-slim
 * @license    MIT
 */
class Util {

    public static function implementsInterface($subjectClass, $interfaceName) {
        if (!is_string($subjectClass)) {
            $subjectClass = get_class($subjectClass);
        }
        $reflector = new \ReflectionClass($subjectClass);
        return $reflector->implementsInterface($interfaceName);
    }

    public static function dump($mxd) {
        echo "<pre>Debug:\n";
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        echo "- {$caller['file']}::{$caller['line']}\n";
        echo "------------------------\n";
        foreach (func_get_args() as $arg) {
            var_dump($arg);
        }
        echo "</pre>";
        die;
    }

}
