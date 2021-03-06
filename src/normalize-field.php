<?php

namespace Pave;

use Pave\EnsureObject\ensureObject;
use Pave\IsArray\isArray;
use Pave\IsObject\isObject;


class NormalizeField {
    public $alias;
    public $query;

    public function __construct($alias, $query) {
        $field = $query['_field'] ?? $alias;
        $args = ensureObject($query['_args']);
        if (!count(array_keys(get_object_vars($args)))) return $field;

        return $field + '('.json_encode(self::orderObject($args)).')';
    }

    public static function orderObject($obj) {
        if (!isObject($obj)) return $obj;
        if (isArray($obj)) return array_map(self::orderObject($obj), $obj);

        $val = new Object;
        $keys = array_keys(get_object_vars($obj)).sort();
        for ($i = 0, $l = count($keys); $i < $l; $i++) {
            $val[$keys[$i]] = self::orderObject($obj[$keys[$i]]);
        }
        return $val;
    }
}
