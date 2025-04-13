<?php
namespace App\Console\Commands;

use App\Models\CustomJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessCustomJobs extends Command
{
    protected $signature = 'custom-jobs:process';
    protected $description = 'Process custom jobs from database chronolgically';

    public function handle()
    {
        $jobs = CustomJob::orderBy('created_at')->get();
        foreach ($jobs as &$job) {
            while ($job->attempts < config('easypeasy.attempts')) {

                if ($job->status == 'success') {
                    break 1;
                }

                $job->update([
                    'status' => 'running',
                    'attempts' => $job->attempts + 1,
                    'last_attempt_at' => now(),
                ]);

                Log::channel('background_jobs')->info("Job Running", [
                    'class' => $job->class,
                    'method' => $job->method,
                    'status' => 'running',
                    'params' => $job->params,
                    'timestamp' => now()->toDateTimeString(),
                ]);

                sleep(config('easypeasy.delay'));

                try {
                    Log::channel('background_jobs')->info("Job Started", [
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
                        'status' => 'success',
                        'output' => json_encode($result),
                    ]);

                    Log::channel('background_jobs')->info("Job Completed", [
                        'class' => $job->class,
                        'method' => $job->method,
                        'status' => 'success',
                        'result' => $result,
                        'params' => $job->params,
                        'timestamp' => now()->toDateTimeString(),
                    ]);

                    break 1;

                } catch (\Throwable $e) {

                    Log::channel('background_jobs_errors')->error("Job failed: ", [
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
                    sleep(config('easypeasy.delay'));
                }

                if ($job->status == 'success') {
                    break 1;
                }

                
            }           
        }
    }
}
