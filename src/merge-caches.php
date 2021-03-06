<?php

namespace Pave;

use Pave\IsArray\isArray;
use Pave\IsEqual\isEqual;
use Pave\IsObject\isObject;

class MergeCaches {
    public $a;
    public $b;
    public $isCacheRoot;

    public function merge($a, $b, $isCacheRoot = true) {
        if (isEqual($a, $b)) return $a;
        if (
            !isObject($a) ||
            isArray($a) ||
            !isObject($b) ||
            isArray($b) ||
            (gettype($b) === 'undefined' && !$isCacheRoot) ||
            $b['_type'] === '_ref'
        ) {
            return $b;
        }

        $c = $a;
        foreach ($b as $k) {
            $v = self::merge($a[$k], $b[$k]);
            if ($v !== $a[$k]) {
                if ($c === $a) $c = $a;
                $c[$k] = $v;
            }
        }
        return $c;
    }
}
