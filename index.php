<?php
//entry point
require 'vendor/autoload.php';

use Ptgmp\App;

if (PHP_SAPI == 'cli') {
    echo "
run on cli-------------------------------------------------------------------
";
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['QUERY_STRING'] = 'synchronize';
}

$configPath = __DIR__.'/config/config.json';
(new App($configPath))->run();
