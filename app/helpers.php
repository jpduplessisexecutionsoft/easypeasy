<?php

use App\Models\CustomJob;

if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob(string $class, string $method, array $params = [])
    {
        //Only allow execution of pre-approved classes and methods:
        $allowedJobs = config('background-jobs.allowed_jobs');

        if (!array_key_exists($class, $allowedJobs)) {
            Log::channel('background_jobs_errors')->error("Error: Class '$class' is not allowed.");
            return;
        }

        if (!in_array($method, $allowedJobs[$class])) {
            Log::channel('background_jobs_errors')->error("Error: Method '$method' is not allowed for class '$class'");
            return;
        }

        // Create the job in the DB
        $job = CustomJob::create([
            'class' => $class,
            'method' => $method,
            'params' => $params,
            'execute' => true
        ]);

        while (CustomJob::where('id', $job->id)->where('execute',true)->first()) {

            $job->update([
                'status' => 'running',
                'attempts' => $job->attempts + 1,
                'last_attempt_at' => now(),
            ]);

            try {
                Log::channel('background_jobs')->info("Job Running", [
                    'class' => $job->class,
                    'method' => $job->method,
                    'status' => 'running',
                    'params' => $job->params,
                    'timestamp' => now()->toDateTimeString(),
                ]);

                $class = "App\\CustomJobs\\{$job->class}";
                $instance = new $class;

                $result = call_user_func([$instance, $job->method], $job->params);

                $job->update([
                    'status' => 'completed',
                    'output' => json_encode($result),
                    'execute' => false,
                ]);

                Log::channel('background_jobs')->info("Job Completed", [
                    'class' => $job->class,
                    'method' => $job->method,
                    'status' => 'completed',
                    'result' => $result,
                    'params' => $job->params,
                    'timestamp' => now()->toDateTimeString(),
                ]);

            } catch (\Throwable $e) {

                Log::channel('background_jobs_errors')->error("Job Failed: ", [
                    'class' => $job->class,
                    'method' => $job->method,
                    'params' => $job->params
                ]);

                Log::channel('background_jobs')->info("Job Failed", [
                    'class' => $job->class,
                    'method' => $job->method,
                    'status' => 'failed',
                    'params' => $job->params,
                    'timestamp' => now()->toDateTimeString(),
                ]);

                $job->update([
                    'status' => 'failed',
                    'output' => $e->getMessage(),
                ]);

            }

            sleep(config('easypeasy.delay'));

            if ($job->attempts >= config('easypeasy.attempts')) {
                $job->update([
                    'status' => 'failed',
                    'execute' => false,
                ]);
            }
  
        }

    }

}

