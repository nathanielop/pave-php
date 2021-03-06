<?php

namespace Pave;

class PaveError extends Error {
    public $code;
    public $info;

    public function __construct($code, $info) {
        $this.$code = $code;
        $info += $this;
    }
}