<?php
namespace Sailor\Utility;

class JSend
{
    /**
     * @var mix $data
     * @return array
     */
    public static function success($data = null)
    {
        $returnInfo = ['status' => 'success'];
        if (!is_null($data)) {
            $returnInfo['data'] = $data;
        }

        return $returnInfo;
    }

    /**
     * @var mix $data
     * @var array
     */
    public static function fail($data = null)
    {
        $returnInfo = ['status' => 'fail'];
        if (!is_null($data)) {
            $returnInfo['data'] = $data;
        }

        return $returnInfo;
    }

    /**
     * @var string $message
     * @var array
     */
    public static function error($message, $data = null)
    {
        $returnInfo = [
            'status' => 'error',
            'message' => $message,
        ];

        if (!is_null($data)) {
            $returnInfo['data'] = $data;
        }

        return $returnInfo;
    }
}