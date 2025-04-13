<?php
namespace App\CustomJobs;
//a small calculator class for testing easypeasy test.
class Calculator {
    function addition($numbers)
    {
        $return = null;
        if (!is_array($numbers)) {
            $return = 'Input must be an array';
        } else {
            $return = array_sum($numbers);
        }
        //sleep(10000); //so that i can cancel it
        return $return;
    }

}
