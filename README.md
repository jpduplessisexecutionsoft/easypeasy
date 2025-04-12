# EasyPease Test

> This is my project for the easypeasy candicacy test.

---
## instructions on how to set up the project
- Make sure php has the pdo sqlite driver installed.

## instructions on how to use runBackgroundJob().
- This is a helper function, and without namespace.  It can be called within any controller or other form of that executes functions within Laravel.  However, not within the run-job.php script as it will case an infinite loop.

## Examples of called background jobs:
> The following two examples are command line examples of calling run-job.php with a small Calculator script i put together for testing.  It will execute and store all required data in the designated logs, as required by the test.

- php run-job.php Calculator addition "1,2"
- php run-job.php Calculator subtraction "1,2"

### Instructions on how to configure retry attempts and delays
> in order to configure the retry attemps, a variable called "$retries" exists on line 47 of the run-job.php script and can be changed.
> in order to configure the waiting times (in seconds) for retry attemps, a variable called "$sleep" exists on line 48 of the run-job.php script and can be changed.

### Instructions on how to register allowed classes and methods
> The file in config/background-jobs.php is used for specifying which classes and methods can be used by run-job.php. Here is an example:
- \Calculator::class => ['addition', 'subtract'],