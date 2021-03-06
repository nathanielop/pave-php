<?php
namespace Pave;

use function Pave\EnsureObject\ensureObject;
use function Pave\IsArray\isArray;
use function Pave\IsObject\isObject;
use function Pave\NormalizeField\normalizeField;

class CacheExecute {
  public $_;
  public $cache;
  public $query;
  public $value;

  public static function walk() {
    while (true) {
      if(isArray(self::$value)) {
        return array_map(self::walk(self::$_, self::$cache, self::$query, self::$value), self::$array);
      } else if (!isObject(self::$value) || gettype(self::$value) === 'undefined') {
        return self::$value;
      } else if (self::$value._type === '_ref') {
        self::$value = self::$cache[self::$value.key];   
      } else {
        { $_args, $_field, ...$_query } = ensureObject(self::$query);
        $_query[`_on_${$value._type}`];
        $data = new Object;
        foreach (self::$_query as $alias) {
          if (str_starts_with($alias, '_on_')) continue;
          self::$query = ensureObject($_query[$alias]);
          $field = normalizeField($alias, self::$query);
          if (self::$value.field) {
            $data[$alias] = self::walk(self::$_, self::$cache, self::$query, self::$value[$field]);
          } else $_.isPartial = true;
        }
        return $data;
      }
    };
  };

  return {
    $_ = { isPartial: false };
    $value = self::$cache[key ?? '_root'];
    $result = walk(self::$_, self::$cache, self::$query, self::$value);
    if (!$_.isPartial) return $result;
  };
}

?>
