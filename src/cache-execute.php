<?php

require 'ensure-object.php';
require 'is-array.php';
require 'is-object.php';
require 'normalize-field.php';

function walk($_, $cache, $query, $value) {
  while (true) {
    if(isArray($value)) {
      return array_map(walk($_, $cache, $query, $value), $array);
    } else if (!isObject($value) || gettype($value) === 'undefined') {
      return $value;
    } else if ($value->_type === '_ref') {
      $value = $cache[$value->key];   
    } else {
      list($_args, $_field, ...$_query) = ensureObject($query);
      $_query[`_on_${$value->_type}`];
      $data = new Object;
      foreach ($_query as $alias) {
        if (str_starts_with($alias, '_on_')) continue;
        $query = ensureObject($_query[$alias]);
        $field = normalizeField($alias, $query);
        if ($value->field) {
          $data[$alias] = walk($_, $cache, $query, $value[$field]);
        } else $_->isPartial = true;
      }
      return $data;
    }
  }
}

function cacheExecute($cache, $key, $query) {
    $_ = (object)['isPartial' => false];
    $value = $cache['key' ?? '_root'];
    $result = walk($_, $cache, $query, $value);
    if (!$_->isPartial) return $result;
}
