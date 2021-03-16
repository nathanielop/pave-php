<?php

require 'normalize.php';
require 'merge-caches.php';
require 'is-equal.php';
require 'inject-type.php';
require 'cache-execute.php';


function ($cache, $execute, $getKey) {
  $watchers = new Set();
  $client = (object)[
    'cache' => $cache ?? (object)[],

    'cacheExecute' =>  function ($key, $query) {
      cacheExecute($client->cache, $key, injectType($query));
    },

    'cacheUpdate' => function ($data) {
      $prev = $client->cache;
      $client->cache = mergeCaches($client->cache, $data);
      if($client->cache === $prev) return $client;

      foreach ($watchers as $watcher) {
        list($data, $onChange, $query) = $watcher;
        if (!$query) return $onChange($client->cache);
        $newData = $client['cacheExecute']($query);
        if(!isEqual($data, $newData)) $onChange($watcher['data'] = $newData);
      }
      return $client;
    },

    // PHP doesn't allow for async functions, so will have to come up with a solution for this
    'execute' => function ($query, ...$args) {
      $query = injectType($query);
      $data = $client['execute']($query, ...$args);
      $client['update']($data, $query);
      return $data;
    },

    'update' => function ($data, $query) {
      $query = injectType($query);
      $data = normalize($data, $getKey, $query);
      $client['cacheUpdate']($data);
    },

    'watch' => function($onChange, $query) {
      $query = $query && injectType($query);
      $data = $query && $client['cacheExecute']($query);
      $watcher = (object)[$data, $onChange, $query];
      $watchers.add($watcher);
      return [ $data, 'unwatch' => function () { $watchers.delete($watcher); }];
    }
  ];
  return $client;
};
