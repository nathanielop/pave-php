<?php

include 'is-object.php';
include 'is-equal.php';
include 'is-array.php';

function mergeCaches($a, $b, $isCacheRoot = true) {
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
        $v = mergeCaches($a[$k], $b[$k]);
        if ($v !== $a[$k]) {
            if ($c === $a) $c = $a;
            $c[$k] = $v;
        }
    }
    return $c;
}
