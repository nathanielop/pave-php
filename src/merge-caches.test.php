<?php

include 'merge-caches.php';

function testMergeCaches() {
    $data = (object)[
    '_root' => (object)[ '_type' => null, 'foo' => 1, 'bar' => (object)[ 'a' => 1 ], 'baz' => (object)[ '_type' => null, 'a' => 1 ] ]
    ];

    echo(mergeCaches($data, (object)['_root' => (object)['_type' => null]]) === $data);
    echo(mergeCaches($data, (object)['_root' => (object)['_type' => null, 'foo' => 1]]) === $data);
    echo(mergeCaches($data, (object)['_root' => (object)['_type' => null, 'foo' => 1, 'bar' => (object)['a' => 1]]]) === $data);
    echo(mergeCaches($data, (object)['_root' => (object)['_type' => null, 'foo' => 1, 'bar' => (object)['a' => 1]]]) === $data);

    $updated = mergeCaches($data, (object)['_root' => (object)['_type' => null, 'foo' => 2]]);

    echo($updated !== $data);
    echo($updated->_root->foo === 2);
    echo($updated->_root->bar === $data->_root->bar);

    $updated = mergeCaches($data, (object)['_root' => (object)['_type' => null, 'bar' => (object)['b' => 1]]]);

    echo($updated !== $data);
    echo($updated->_root->bar === (object)['b' => 1]);
    echo($updated->_root->foo === $data->_root->foo);

    $updated = mergeCaches($data, (object)['_root' => (object)['_type' => null, 'baz' => 'new']]);

    echo($updated !== $data);
    echo($updated->_root->baz === 'new');
    echo($updated->_root->foo === $data->_root->foo);
    echo($updated->_root->bar === $data->_root->bar);

    $updated = mergeCaches($data, (object)['_root' => (object)['_type' => null, 'baz' => (object)['_type' => null, 'b' => 2]]]);

    echo($updated !== $data);
    echo($updated->_root->baz === (object)['_type' => null, 'a' => 1, 'b' => 2]);
    echo($updated->_root->foo === $data->_root->foo);
    echo($updated->_root->bar === $data->_root->bar);
}
