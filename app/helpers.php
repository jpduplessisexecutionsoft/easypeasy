<?php

if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob($class, $method, $params = []) {
        $paramString = implode(',', array_map('escapeshellarg', $params));
        $class = escapeshellarg($class);
        $method = escapeshellarg($method);

        exec("php scripts/run-job.php $class $method $paramString");
    }
}