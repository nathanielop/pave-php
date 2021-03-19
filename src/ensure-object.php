<?php

require 'is-array.php';
require 'is-object.php';

function ensureObject($obj) {
    return (isObject($obj) && !isArray($obj) ? $obj : (object)[]);
}
