<?php

include 'is-array.php';
include 'is-object.php';

function isEqual($a, $b) {
    if ($a === $b) return true;
    if (!isObject($a) || !isObject($b)) return $a === $b;
    if (isArray($a)) {
        if (!isArray($b) || count($a) !== count($b)) return false;
        for ($i = 0; $i < count($a); $i++) if (!isEqual($a[$i], $b[$i])) return false;
        return true;
    }
    if (isArray($b)) return false;
    if (count(array_keys(get_object_vars($a))) !== count(array_keys(get_object_vars($b)))) return false;
    foreach ($a as $key) if (!isEqual($a[$key], $b[$key])) return false;
    return true;
}
