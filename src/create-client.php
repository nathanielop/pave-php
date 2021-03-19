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
      cacheExecute($this->cache, $key, injectType($query));
    },

    'cacheUpdate' => function ($data) {
      $prev = $this->cache;
      $this->cache = mergeCaches($this->cache, $data);
      if($this->cache === $prev) return $this;

      foreach ($watchers as $watcher) {
        list($data, $onChange, $query) = $watcher;
        if (!$query) return $onChange($this->cache);
        $newData = $this['cacheExecute']($query);
        if(!isEqual($data, $newData)) $onChange($watcher['data'] = $newData);
      }
      return $this;
    },

    // PHP doesn't allow for async functions, so will have to come up with a solution for this
    'execute' => function ($query, ...$args) {
      $query = injectType($query);
      $data = $this['execute']($query, ...$args);
      $this['update']($data, $query);
      return $data;
    },

    'update' => function ($data, $query) {
      $query = injectType($query);
      $data = normalize($data, $getKey, $query);
      $this['cacheUpdate']($data);
    },

    'watch' => function($onChange, $query) {
      $query = $query && injectType($query);
      $data = $query && $this['cacheExecute']($query);
      $watcher = (object)[$data, $onChange, $query];
      $watchers.add($watcher);
      return [ $data, 'unwatch' => function () { $watchers.delete($watcher); }];
    }
  ];
  return $client;
};
