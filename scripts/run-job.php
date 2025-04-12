#!/usr/bin/env php
<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

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

//Only allow execution of pre-approved classes and methods:
$allowedJobs = config('background-jobs.allowed_jobs');

if (!array_key_exists($className, $allowedJobs)) {
    Log::channel('background_jobs_errors')->debug("Error: Class '$className' is not allowed.");
    exit(1);
}

if (!in_array($methodName, $allowedJobs[$className])) {
    Log::channel('background_jobs_errors')->debug("Error: Method '$methodName' is not allowed for class '$className'");
    exit(1);
}

//assign amount of retries
$retries = 3;
$sleep = 1;

function run($className, $methodName, $parameters)
{
    //log running of job
    Log::channel('background_jobs')->debug(
        'Status: running'
    );

    try {
        //load the specified class
        require_once($className.".php");

        //instantiate the loaded class
        $loadedClass = new $className;


        //execute the method specified as argument
        $result = $loadedClass->$methodName($parameters[0],$parameters[1]);

        //Check result value
        $status = ($result === false || $result === null) ? 'Failure' : 'Success';

        //log the job execution
        Log::channel('background_jobs')->debug(
            'Loaded class: "' . $className . '" Method: "' . $methodName . '" Status: "' . $status . '"'
        );

        //exit and log on success
        if ($status === 'Success')
        {
            //log job completion
            Log::channel('background_jobs')->debug(
                'Status: complete'
            );
            exit(1);
        }

    } catch (\Exception $e)
    {
        //log any error
        Log::channel('background_jobs_errors')->debug($e->getMessage());
        //log job execution error
        Log::channel('background_jobs')->error(
            'Job Failed - Class: "' . $className . '" Method: "' . $methodName . '" Status: Failure'
        );
        //log job failure
        Log::channel('background_jobs')->debug(
            'Status: failed'
        );
        
    }
}

//retry x amount of attempts
while($retries)
{
    //attempt to run the job
    run($className, $methodName, $parameters);
    
    $retries--;

    //retry delay between attemps
    sleep($sleep);

}