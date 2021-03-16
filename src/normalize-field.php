<?php

require 'ensure-object.php';
require 'is-array.php';
require 'is-object.php';


function __construct($alias, $query) {
    $field = $query['_field'] ?? $alias;
    $args = ensureObject($query['_args']);
    if (!count(array_keys(get_object_vars($args)))) return $field;
    return $field + '('.json_encode(orderObject($args)).')';
}

function orderObject($obj) {
    if (!isObject($obj)) return $obj;
    if (isArray($obj)) return array_map(orderObject($obj), $obj);

    $val = new Object;
    $keys = array_keys(get_object_vars($obj)).sort();
    for ($i = 0, $l = count($keys); $i < $l; $i++) {
        $val[$keys[$i]] = orderObject($obj[$keys[$i]]);
    }
    return $val;
}
