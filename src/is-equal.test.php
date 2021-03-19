<?php

require 'is-equal.php';

// function undefined() {
//     echo isEqual(undefined, undefined);
//     echo isEqual(undefined, null);
//     echo isEqual(null, undefined);
//     echo isEqual(undefined, (object)[]);
// }

function null() {
    echo isEqual(null, null);
    echo isEqual(null, 0);
    echo isEqual(null, '');
    // echo isEqual(undefined, (object)[]);
}

function boolean() {
    echo isEqual(true, true);
    echo isEqual(false, false);
    echo isEqual(true, false);
    echo isEqual(true, (object)[]);
    echo isEqual(false, null);
}

function string() {
    echo isEqual('a', 'a');
    echo isEqual('1', 1);
    echo isEqual(1, '1');
    echo isEqual('[object Object]', (object)[]);
}

function number() {
    echo isEqual(1, 1);
    echo isEqual(1, 2);
    echo isEqual(1, '1');
}

function arrays() {
    echo isEqual([], []);
    echo isEqual([[]], [[]]);
    echo isEqual([1, (object)['two' => 2, 'dos' => 2], ['three']], [1, (object)['two' => 2, 'dos' => 2], ['three']]);
    echo isEqual([], [1]);
    echo isEqual([1], [1, 2]);
    echo isEqual([], (object)[]);
}

function object() {
    echo isEqual((object)[], (object)[]);
    echo isEqual((object)['a' => 1], (object)['a' => 1]);
    echo isEqual((object)['a' => 1, 'b' => 2], (object)['b' => 2, 'a' => 1]);
    echo isEqual((object)['a' => 1], (object)['b' => 1]);
    echo isEqual((object)[], []);
}
