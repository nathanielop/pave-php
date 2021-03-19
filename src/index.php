<?php

require 'create-client.php';
require 'estimate-cost.php';
require 'execute.php';
require 'validate-args.php';
require 'validate-query.php';

use Pave\PaveError;

return (object)[createClient(), estimateCost(), execute(), new PaveError, validateArgs(), validateQuery()];
