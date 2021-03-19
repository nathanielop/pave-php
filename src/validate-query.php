<?php

require 'is-object.php';
require 'validate-args.php';

use Pave\PaveError;

function validateQuery($context, $path = [], $query, $schema, $type) {
    $SKIP_ARGS = (object)[];
    $fail = function ($code, $extra) use ($context, $path, $query, $schema, $type) {
        throw new PaveError($code, (object)[$context, $path, $query, $schema, $type, ...$extra]);
    };
    do {
        if(!isObject($query)) $fail('invalidQuery', []);
        if($type == null) {
            foreach($query as $alias => $val) {
                if($alias !== '_args' && $alias !== '_field' && $alias !== '_type' && !str_starts_with($alias, '_on_')) {
                    $fail('unknownField', (object)[$alias, $query[$alias]->_field || $alias]);
                }
            }
            return (object)[];
        } else if (!isObject($type)) {
            if($schema[$type]) $type = $schema[$type];
            else $fail('unknownType', []);
        } else if ($type->optional) $type = $type->optional;
        else if ($type->nullable) $type = $type->nullable;
        else if ($type->arrayOf) $type = $type->arrayOf;
        else if ($type->oneOf) {
            $query = list($query);
            foreach ($query as $alias => $val) {
                if($alias === '_field' || $query[$alias] === $SKIP_ARGS) continue;

                $subQuery = list($query[$alias]);
                if(!isObject($subQuery)) $fail('invalidQuery', (object)[$alias, $alias]);

                $field = $subQuery->_field ?? $alias;
                if($field == '_type') {
                    unset($query[$alias]);
                    continue;
                }

                if($alias === $field) unset($subQuery['_field']);

                if(!str_starts_with($field, '_on_')) {
                    $fail('ambiguousField', (object)[$alias, $alias]);
                }

                $name = substr($field, strlen('_on_'));
                $query[$alias] = validateQuery($context, array_merge($path, $alias), $subQuery, $schema, $type->oneOf[$name]);
            }

            return $query;
        } else if ($type->fields) {
            $query = list($query);
            foreach($query as $alias => $val) {
                if($alias === '_field' || $query[$alias] === $SKIP_ARGS) continue;

                if(!isObject($query[$alias])) {
                    $fail('invalidQuery', (object)[$alias, $alias]);
                }

                $subQuery = list($query[$alias]);
                $field = $subQuery->_field ?? $alias;

                if($alias === $field) unset($subQuery->_field);

                if($field === '_type') {
                    $query[$alias] = (object)[];
                    continue;
                }

                $_type = $type->fields[$field];
                if(!$_type) $fail('unknownField', (object)[$alias, $field]);

                $query[$alias] = validateQuery($context, array_merge($path, $alias), $subQuery, $schema, $_type);
            }

            return $query;
        } else {
            list($_args, $_field, ...$_query) = $query;
            $_query = validateQuery($context, $path, (object)['_args' => $SKIP_ARGS, ...$_query], $schema, $type->type);
            if($_field) $_query->_field = $_field;

            if($_args !== $SKIP_ARGS) {
                $_query->_args = validateQuery($_args, $context, array_merge($path, ['_args']), (object)[...$_query, $_args], $schema, $type);
            }

            if(!$type->args) unset($_query->_args);
            return $_query;
        }
    } while (true);
}
