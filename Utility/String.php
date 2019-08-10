<?php

namespace Sailor\Utility;

use Sailor\Core\LoggerFactory as Logger;

class String
{
    const COMPARE_HEAD = 0;
    const COMPARE_TAIL = 1;

    public static function currency($amount)
    {
        $length = 3;
        $amount = (string)$amount;
        if (strlen($amount) <= $length) {
            return $amount;
        }

        $str = '';
        $start = strlen($amount) - 1 - $length;
        for ($i=$start; $i>=0; $i-=$length) {
            $str = ',' . substr($amount, $i, $length) . $str;
        }
        $str = substr($amount, 0, $i+$length+1) . $str;
        return $str;
    }

    public static function compare($rawStr, $comparisonStr, $length=0, $mode=self::COMPARE_HEAD)
    {
        if (empty($length)) {
            return $rawStr == $comparisonStr;
        }

        if ($length > strlen($rawStr) || $length > strlen($comparisonStr)) {
            Logger::error('The length can not be longer than string.');
            return false;
        }

        switch ($mode) {
            case self::COMPARE_HEAD:
                return substr($rawStr, 0, $length) == substr($comparisonStr, 0, $length);
            case self::COMPARE_TAIL:
                return substr($rawStr, strlen($rawStr) - $length, $length) == substr($comparisonStr, strlen($comparisonStr) - $length, $length);
            default:
                return false;
        }
    }
}