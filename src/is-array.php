<?php

namespace Pave;

class IsArray {
    public $array;

    public static function isArray() {
        return gettype(self::$array) === 'array';
    }
}