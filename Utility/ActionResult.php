<?php

namespace Sailor\Utility;

class ActionResult
{
    public static function success($message = 'success', array $data = [])
    {
        return new ActionResult('success', $message, $data);
    }

    public static function error($message = 'error', array $data = [])
    {
        return new ActionResult('error', $message, $data);
    }

    public static function fail($message = 'success', array $data = [])
    {
        return new ActionResult('fail', $message, $data);
    }

    /** @var boolean */
    private $success;

    /** @var string */
    private $status;
    
    /** @var string */
    private $message;

    /** @var array */
    private $data = [];

    /**
     * @param string $status
     * @param string $message
     * @param array $data
     */
    public function __construct($status, $message, array $data = [])
    {
        switch ($status) {
            case 'success':
                $this->success = true;
                break;
            case 'error':
            case 'fail':
                $this->success = false;
                break;
        }

        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * @return boolean 
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}