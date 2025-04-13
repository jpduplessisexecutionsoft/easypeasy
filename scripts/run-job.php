#!/usr/bin/env php
<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Models\CustomJob;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();


// Get command-line arguments
$args = $argv ?? [];

// $argv[0] is the script name, not used here
$script = array_shift($args);

// make sure that all arguments required were given
if (count($args) < 3) {
    echo "Usage: php run-job.php <ClassName> <methodName> <\"param1,param2\">\n";
    exit(1);
}

// Basic sanitization and assignment
$className = trim($args[0]);
$methodName = trim($args[1]);
$parameters = array_map('trim', explode(',', $args[2]));


//run the job
runBackgroundJob($className, $methodName, $parameters);
