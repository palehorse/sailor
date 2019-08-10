<?php

namespace Sailor\Utility;

class Arr
{
    public static function isSingleDimension($array)
    {
        if (!is_array($array)) {
            return false;
        }

        return sizeof(array_filter($array, 'is_array')) == 0;
    }

    public static function slice($array, $position, $length)
    {
        $returnArr = [];
        if ($position + $length > count($array)) {
            return $returnArr;
        }

        for ($i=$position; $i<$position+$length; $i++) {
            $returnArr[] = $array[$i];
        }

        return $returnArr;
    }
}