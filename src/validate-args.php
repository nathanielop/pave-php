<?php

require 'is-array.php';
require 'is-function.php';
require 'is-object.php';

use Pave\PaveError;

function validateValue($context, $path = [], $query, $schema, $type, $typeArgs, $value) {
  $fail = function ($code, $extra) use ($context, $path, $query, $schema, $type, $typeArgs, $value) {
    throw new PaveError($code, (object)[$context, $path, $query, $schema, $type, $typeArgs, $value, ...$extra]);
  };

  $isNullable = false;
  $isOptional = false;

  do {
    if($type == null) {
      if($value != null) return $value;
      if(!$isOptional && $isNullable) return null;
      if(!isset($value) && !$isOptional) $fail('expectedRequired', null);
      if($value === null && !$isNullable) $fail('expectedNonNull', null);

      return $value;
    } else if (!isObject($type)) {
      if($schema[$type]) $type = $schema[$type];
      else $fail('unknownType', null);
    } else if (!isset($value) && isset($type->defaultValue)) {
      $value = $type->defaultValue;
    } else if ($type->optional) {
      $type = $type->optional;
      $isOptional = true;
    } else if ($type->nullable) {
      $type = $type->nullable;
      $isNullable = true;
    } else if ($value == null) $type = null;
    else if ($type->arrayOf) {
      if (!isArray($value)) $fail('expectedArray', null);

      list($minLength, $maxLength) = $type;
      if($minLength != null && count($value) < $minLength) {
        $fail('expectedArrayMinLength', null);
      }

      if($maxLength != null && count($value) > $maxLength) {
        $fail('expectedArrayMaxLength', null);
      }

      $returnMapping = array();

      foreach($value as $i => $val) {
        array_push($returnMapping, validateValue($context, array_merge($path, $i), $query, $schema, $type->arrayOf, $typeArgs, $value));
      }

      return $returnMapping;
    } else if ($type->oneOf) $type = $type->oneOf[$type->resolveType($value)];
    else if ($type->fields) {
      $check = (object)[];
      foreach($type->fields as $field => $val) unset($check[$field]);

      $check = (object)[...$check, ...$value];
      $_value = (object)[];
      foreach($check as $field => $val) {
        $value = $check[$field];
        $_type = $type->fields[$field];
        if (!$_type) $fail('unknownField', (object)[$field]);

        $value = validateValue(
          $context,
          array_merge($path, $field),
          $query,
          $schema,
          $_type,
          $typeArgs,
          $value
        );
        if(isset($value)) $_value[$field] = $value;
      }
      return $_value;
    } else {
      //$_value = 'resolve' in $type ? $type->resolve : $value;
      if(isFunction($_value)) {
        $_value = $_value(
          validateArgs($typeArgs, $context, array_merge($path, ['_args']), $query, $schema, $type),
          $context,
          $path,
          $query,
          $schema,
          $type,
          $value
        );
      }
      $typeArgs = $type->typeArgs;
      $type = $type->type;
      $value = $_value;
    }
  } while (true);
}

function validateArgs($args, $context, $path, $query, $schema, $type) {
  $args = validateValue(null, $context, $path, $query, $schema, (object)['defaultValue' => (object)[], 'fields' => $type->args ?? (object)[] ], $args);

  if($type->validate) return $args;

  return $type->validate($args, $context, $path, (object)[...$query, $args], $schema, $type);
}
