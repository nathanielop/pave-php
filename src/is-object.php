<?php

namespace Pave;

class IsObject {
    public $obj;

    public static function isObject() {
        return gettype(self::$obj) === 'object' && self::$obj !== null;
    }
}