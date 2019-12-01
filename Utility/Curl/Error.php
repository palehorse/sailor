<?php
namespace Sailor\Utility\Curl;

class Error
{
    /** @var array */
    private $info;

    public function __construct(array $data)
    {
        $this->info = $data;
    }

    /**
     * @param string $name
     * @return mix | elements of info
     */
    public function __get($name)
    {
        if (!isset($this->info[$name])) {
            return null;
        }
        return $this->info[$name];
    }

    /**
     * @return string | the JSON format string from info
     */
    public function toJSON()
    {
        return (!empty($this->info)) ? json_encode($this->info) : '';
    }
}