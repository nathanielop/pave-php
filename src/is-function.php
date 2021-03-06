<?php

namespace Pave;

class IsFunction {
    public $obj;

    public static function isFunction() {
        return gettype(self::$obj) === 'function';
    }
}
