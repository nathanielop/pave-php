<?php

require 'is-object.php';
require 'is-function.php';

function estimateCost($context, $path = [], $query, $schema, $type) {
  do {
    if($type == null) return 0;
    else if (!isObject($type)) $type = $schema[$type];
    else if ($type->optional) $type = $type->optional;
    else if ($type->nullable) $type = $type->nullable;
    else if ($type->arrayOf) $type = $type->arrayOf;
    else if ($type->oneOf) {
      $maxableArr = array();
      foreach($type->oneOf as list($name, $type)) {
        $onKey = '_on_'.$name;
        array_push($maxableArr, estimateCost($context, array_merge($path, $onKey), $query[$onKey] ?? (object)[], $schema, $type));
      } 
      return max($maxableArr);
    } else {
      list($_args, $_field, ...$_query) = $query;
      $cost = 0;
      if($type->Fields) {
        foreach($query as $alias) {
          $_query = $query[$alias];
          $_type = $type->fields[$_query.$_field ?? $alias];
          $cost += estimateCost(
            $context,
            array_merge($path, $alias),
            $_query,
            $schema,
            $_type
          );
        }
      } else {
        $cost = estimateCost(
          $context,
          $path,
          $_query,
          $schema,
          $type->type
        );
      }

      if(isFunction($type->cost)) {
        $cost = $type->cost($_args, $context, $cost, $path, $query, $schema, $type);
      } else if ($type->cost) {
        $cost += $type->cost;
      }

      return $cost;
    }
  } while (true);
}
