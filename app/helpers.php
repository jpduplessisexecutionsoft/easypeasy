<?php

use App\Models\CustomJob;

if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob(string $class, string $method, array $params = [])
    {
        //Only allow execution of pre-approved classes and methods:
        $allowedJobs = config('background-jobs.allowed_jobs');

        if (!array_key_exists($class, $allowedJobs)) {
            Log::channel('background_jobs_errors')->debug("Error: Class '$class' is not allowed.");
            return;
        }

        if (!in_array($method, $allowedJobs[$class])) {
            Log::channel('background_jobs_errors')->debug("Error: Method '$method' is not allowed for class '$class'");
            return;
        }

        // Create the job in the DB
        $job = CustomJob::create([
            'class' => $class,
            'method' => $method,
            'params' => $params,
        ]);

        Log::channel('background_jobs')->info("Job Created", [
            'class' => $job->class,
            'method' => $job->method,
            'status' => 'pending',
            'params' => $job->params,
            'timestamp' => now()->toDateTimeString(),
        ]);


        $descriptorspec = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open('php ' . base_path('artisan') . ' custom-jobs:process', $descriptorspec, $pipes);
        

        if (is_resource($process)) {
            echo 'The process is running';

            $pid = proc_get_status($process)['pid'];
            // Save the pid to your job model
            $job->update(['pid' => $pid]);
            

            while (proc_get_status($process)['running']) {
                usleep(10);
            }    

        }

    }

}

