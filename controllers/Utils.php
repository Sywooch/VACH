<?php

namespace app\controllers;

class Utils {

    // Function to calculate square of value - mean
    public static function square($x, $mean) {
        return pow($x - $mean, 2);
    }

// Function to calculate standard deviation (uses square)
    public static function standard_deviation($array) {

// square root of sum of squares devided by N-1
        return sqrt(array_sum(array_map("self::square", $array, array_fill(0, count($array), (array_sum($array) / count($array))))) / (count($array) - 1));
    }

}

?>
