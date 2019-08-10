<?php

namespace Sailor\Utility;

class Message
{
    const SUCCESS   = 'Success';
    const FAILED    = 'Failed';
    const ERROR     = 'Error';
    const NOT_FOUND = 'Not found';

    public static function success($message, array $data=[], $status=self::SUCCESS)
    {
        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ];
    }

    public static function fail($message, $status=self::FAILED)
    {
        return [
            'status'  => $status,
            'message' => $message,
        ];
    }

    public static function error($message, $status=self::ERROR)
    {
        return [
            'status'  => $status,
            'message' => $message,
        ];
    }

    public static function notFound($message, $status=self::NOT_FOUND)
    {
        return [
            'status'  => $status,
            'message' => $message,
        ];
    }
}