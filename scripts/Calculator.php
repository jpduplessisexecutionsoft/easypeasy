<?php
//a small calculator class for testing easypeasy test.
class Calculator {
    //add param1 and param2
    function addition($num1, $num2)
    {
        return (float)$num1 + (float)$num2;
    }

    //subtract param2 from param1
    function subtract($num1, $num2)
    {
        return (float)$num1 - (float)$num2;
    }
}