<?php

namespace SlimPower\Libs;

/**
 * Utils
 *
 * @package    SlimPower
 * @subpackage Libs
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
