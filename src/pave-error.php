<?php

class PaveError extends Error {
    public $code;
    public $info;

    public function __construct($code, $info) {
        $this.$code = $code;
        $this.$info = $info;
    }
}