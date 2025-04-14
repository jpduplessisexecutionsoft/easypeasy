# EasyPease Test

> This is my project for the easypeasy candicacy test.

---
## instructions on how to set up the project
- Make sure php has the pdo sqlite driver installed.
- install vendor
- install node modules

## How to use the runBackgroundJob function with examples:
> the run-job.php script can be found in the base path's /scripts directory.
-  php run-job.php Calculator addition "grrrowllllll this will fail!"
-  php run-job.php Calculator addition "50,50"

### Instructions on how to configure retry attempts and delays
>A configuration file exists in config/easypeasy.php to set the respective variables: delay and attempts. 

### Instructions on how to register allowed classes and methods
> A class has to be in App\CustomJobs\ location, using the 'App\CustomJobs' namespace.
> The file in config/background-jobs.php is used for specifying which classes and methods can be used by run-job.php. Here is an example:
- \Calculator::class => ['addition'],
