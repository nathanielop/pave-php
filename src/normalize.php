<?php

require 'ensure-object.php';
require 'is-array.php';
require 'is-object.php';
require 'normalize-field.php';

function walk($normalized = new stdClass, $data, $getKey, $query) {
    if (isArray($data)) {
        $results = array();
        foreach($data as $dataChild) {
            array_push($results, walk($normalized, $dataChild, $getKey, $query));
        }
        return $results;
    }

    if(!isObject($data) || $data->_type === 'undefined') return $data;

    $key = $getKey && getKey($data);
    $obj = $key ? $normalized[$key] ?? ($normalized[$key] === (object)[]) : (object)[];

    // list($_args, $_field, ...$_query) = ensureObject($query); TODO: Translate this in all use instances
    $_query[`_on_`.$data->type] = $_query;
    foreach($_query as $alias) {
        // if(!($alias in $data)) continue; TODO: translate this garbage
        $query = ensureObject($_query[$alias]);
        $field = normalizeField($alias, $query);
        $value = walk($normalized, $data[$alias], $getKey, $query);
        $obj[$field] = isObject($value) && !isArray($value) && $value->_type !== 'ref' ? (object)[...$obj[$field], ...$value] : $value;
    }

    return $key ? (object)['_type' => '_ref', $key] : $obj;
}

function normalize($data, $getKey, $query) {
    $normalized = (object)[];
    $normalized->_root = walk($data, $getKey, $normalized, $query);
    return $normalized;
}
