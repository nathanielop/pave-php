<?php

require 'is-object.php';

echo isObject(3);
echo isObject(3.14);
echo isObject(3/4);
echo isObject(-3);
echo isObject('foo');
echo isObject('bar');
echo isObject(['foo', 'bar', 'baz']);
echo isObject(['foo' => 'bar']);