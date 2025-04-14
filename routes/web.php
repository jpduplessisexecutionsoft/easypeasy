<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\JobRunnerController;
use App\Models\CustomJob;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/background_jobs_errors', function () {
    $log = file_get_contents(storage_path('logs/background_jobs_errors.log'));
    return response(['log' => $log]);
});

Route::get('/background_jobs', function () {
    $log = file_get_contents(storage_path('logs/background_jobs.log'));
    return response(['log' => $log]);
});

Route::post('/cancel/{job}', function (int $id) {
    $job = CustomJob::where('id',$id)->first();
    $job->update([
        'execute' => false
    ]);
});

Route::get('/jobs', function () {
    return CustomJob::orderBy('created_at', 'desc')->get();
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
