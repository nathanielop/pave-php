<?php

function injectType($query) {
  $query = (object)['_type' => (object)[], ...$query ];
  foreach (array_keys(get_object_vars($query)) as $key) {
    if ($key !== '_args' && $key !== '_field' && $key !== '_type') {
      $query[$key] = injectType($query[$key]);
    }
  }
  return $query;
};
