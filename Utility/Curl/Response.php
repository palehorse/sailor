<?php
namespace Sailor\Utility\Curl;

class Response
{
    /** @var string */
    private $response;

    /** @var boolean */
    private $isSuccess;

    /** @var array */
    private $header;

    /** @var string */
    private $body;

    /**
     * @param resource $ch 
     * @param resource $response
     */
    public function __construct($ch, $response)
    {
        $this->curl = $ch;
        $this->isSuccess = true;
        if (empty($response)) {
            $this->isSuccess = false;
            return;
        }
        $this->response = $response;
        $this->header = $this->getHeaderData();
        $this->body = $this->getBodyData();
    }

    /**
     * @return boolean | Is Successful or not
     */
    public function isSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * @return resource the raw response content
     */
    public function getRaw()
    {
        return ($this->isSuccess) ? $this->response : null;
    }

    /**
     * @param string $name
     * @return string the value of response header with the given name
     */
    public function getHeader($name)
    {
        if (!$this->isSuccess) {
            return null;
        }
        return isset($this->header[$name]) ? $this->header[$name] : null;
    }

    /**
     * @return array all of the header data
     */
    public function getHeaders()
    {
        return ($this->isSuccess) ? $this->header : null;
    }

    /**
     * @return array the body content of the response | string
     */
    public function getBody()
    {
        return ($this->isSuccess) ? $this->body : null;
    }

    /**
     * @return boolean 
     */
    public function isJSON()
    {
        if (!$this->isSuccess) {
            return null;
        }
        return $this->getHeader('Content-Type') == 'application/json' && is_array($this->body);
    }

    public function __get($name)
    {
        if (!$this->isSuccess) {
            return null;
        }

        if (isset($this->body[$name])) {
            return $this->body[$name];
        }
        return null;
    }

    private function getHeaderData()
    {
        if (!$this->isSuccess) {
            return null;
        }

        $header = substr($this->response, 0, curl_getinfo($this->curl, CURLINFO_HEADER_SIZE));
        preg_match_all('/([\w\-]+): ([\w\-\/]+)/', $header, $matches);
        
        $headers = [];
        if (!empty($matches)) {
            foreach ($matches[0] as $row) {
                list($name, $value) = explode(': ', $row);
                $headers[$name] = $value;
            }
        }

        return $headers;
    }

    private function getBodyData()
    {
        if (!$this->isSuccess) {
            return null;
        }
        
        $body = substr($this->response, curl_getinfo($this->curl, CURLINFO_HEADER_SIZE)) ?: null;
        if ($this->getHeader('Content-Type') == 'application/json') {
            return json_decode($body, true);
        }
        return $body;
    }
}