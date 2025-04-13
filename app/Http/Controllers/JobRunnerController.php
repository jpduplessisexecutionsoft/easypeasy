<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\CustomJob;
use Illuminate\Support\Facades\Log;

class JobRunnerController extends Controller
{
    public function dispatch(Request $request)
    {
        $request->validate([
            'class' => 'required|string',
            'method' => 'required|string',
            'params' => 'required|array',
        ]);

        runBackgroundJob($request->class, $request->method, $request->params);
    
    }

    public function cancel(Request $request)
    {

        $job = CustomJob::Where('id', $request->job)->first();
        //kill the running task
        if ($job->pid) {
            exec("kill {$job->pid}");
            $job->update(['status' => 'canceled']);

            Log::channel('background_jobs')->info("Job Cancelled", [
                'class' => $job->class,
                'method' => $job->method,
                'status' => 'cancelled',
                'params' => $job->params,
                'timestamp' => now()->toDateTimeString(),
            ]);
        }
    }

}
