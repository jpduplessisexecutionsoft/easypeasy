# EasyPease Test

> This is my project for the easypeasy candicacy test.

---
## instructions on how to set up the project
- Make sure php has the pdo sqlite driver installed.
- install vendor
- install node modules

## instructions on how to use runBackgroundJob().
- With the following artisan command: php artisan custom-jobs:process

## Examples of called background jobs:


### Instructions on how to configure retry attempts and delays
>A configuration file exists in config/easypeasy.php to set the respective variables: delay and attempts. 

### Instructions on how to register allowed classes and methods
> A class has to be in App\CustomJobs\ location, using the 'App\CustomJobs' namespace.
> The file in config/background-jobs.php is used for specifying which classes and methods can be used by run-job.php. Here is an example:
- \Calculator::class => ['addition', 'subtract'],
