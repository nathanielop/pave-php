<?php

namespace Pave;

use isArray;
use isObject;

class EnsureObject {
    public $obj;

    public static function ensureObject() {
        return (isObject(self::$obj) && !isArray(self::$obj) ? self::$obj : new Object);
    } 
}
