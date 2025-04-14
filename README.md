# EasyPease Test

> This is my project for the easypeasy candicacy test.

---
## instructions on how to set up the project
- Make sure php has the pdo sqlite driver installed.
- install vendor with the command: composer install
- install node modules: with the command: npm install
- run the migrations with the command: php artisan migrate
- compile the assets with the command: npm run build or npm run dev

## How to use the runBackgroundJob function with examples:
> the run-job.php script can be found in the base path's /scripts directory.
-  php run-job.php Calculator addition "grrrowllllll this will fail!"
-  php run-job.php Calculator addition "50,50"
   - the logs of output can be found in /storage/logs as specified by the project requirements
   - the logs can be found in the /storage/logs directory as specified by the project requirements

### Instructions on how to configure retry attempts and delays
>A configuration file exists in config/easypeasy.php to set the respective variables: delay and attempts. 

### Instructions on how to register allowed classes and methods
> A class has to be in App\CustomJobs\ location, using the 'App\CustomJobs' namespace.
> The file in config/background-jobs.php is used for specifying which classes and methods can be used by run-job.php. Here is an example:
- \Calculator::class => ['addition'],

### instructions on how to use the dashboard
> A small front-end has been created which displays running tasks and the error log.
- a task can be cancelled by pressing the cancel butting, while it's executing.
- simply make sure the server has has rewrite enabled for the /public folder during installation.

## further details
- the code is commented clearly without being too verbose.
