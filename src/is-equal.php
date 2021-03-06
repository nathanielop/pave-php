<?php

namespace Pave;

use Pave\IsArray\isArray;
use Pave\isObject\isObject;

class IsEqual {
    public $a;
    public $b;

    public static function isEqual() {
        if (self::$a === self::$b) return true;
        if (!isObject(self::$a) || !isObject(self::$b)) return self::$a === self::$b;
        if (isArray(self::$a)) {
            if (!isArray(self::$b) || count(self::$a) !== count(self::$b)) return false;
            for ($i = 0; $i < count(self::$a); $i++) if (!isEqual(self::$a[$i], self::$b[$i])) return false;
            return true;
        }
        if (isArray($b)) return false;
        if (count(array_keys(get_object_vars(self::$a))) !== count(array_keys(get_object_vars(self::$b)))) return false;
        foreach ($a as $key) if (!isEqual(self::$a[$key], self::$b[$key])) return false;
        return true;
    }
}
