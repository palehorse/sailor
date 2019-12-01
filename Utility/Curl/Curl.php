<?php

namespace Sailor\Utility\Curl;

class Curl
{
    private $method;
    private $url;
    private $ch;
    private $headers = [];

    public function post($url, array $data=[])
    {
        $this->ch = curl_init();
        $this->url = $url;
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return $this;
    }

    public function get($url, array $data=[])
    {
        $this->ch = curl_init();
        $this->url = $url;

        if (!empty($data)) {
            $this->url .= '?' . http_build_query($data);
        }
        return $this;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getHeader($name)
    {
        if (!empty($this->headers[$name])) {
            return $this->headers[$name];
        }
        return null;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function call()
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->url);

        if (!empty($this->headers)) {
            $headers = [];
            foreach ($this->headers as $name => $value) {
                $headers[] = sprintf('%s: %s', $name, $value);
            }
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        curl_setopt($this->ch, CURLOPT_NOBODY, 0);
        $result = curl_exec($this->ch);
        $response = new Response($this->ch, $result);
        curl_close($this->ch);

        return $response;
    }
}