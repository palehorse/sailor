<?php
namespace Sailor\Utility\Curl;

class Response
{
    const OK = 200;

    const MOVED_PERMANENTLY = 301;
    const FOUND             = 302;
    const NOT_MODIFIED      = 304;
    
    const BAD_REQUEST        = 400;
    const UNAUTHORIZED       = 401;
    const FORBIDDEN          = 403;
    const NOT_FOUND          = 404;
    const METHOD_NOT_ALLOWED = 405;

    const INTERNAL_SERVER_ERR = 500;
    const BAD_GATEWAY         = 502;
    const GATEWAY_TIMEOUT     = 504;

    const HTTP_MSG = [
        '200' => 'OK',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '304' => 'Not Modified',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '403' => 'Forbidden',
        '405' => 'Method Not Allowed',
        '500' => 'Internal Server Error',
        '502' => 'Bad Gateway',
        '504' => 'Gateway Timeout',
    ];

    /** @var string */
    private $httpStatus;

    /** @var string */
    private $header;

    /** @var array */
    private $formattedHeader;

    /** @var string */
    private $body;

    /** @var array */
    private $formattedBody;

    /**
     * @param resource $ch
     */
    public function __construct($ch, $response)
    {
        $info = curl_getinfo($ch);
        
        $this->httpStatus = $info['http_code'];
        $this->info = $info;
        $this->response = $response;

        $this->header = substr($response, 0, $info['header_size']);
        $this->formattedHeader = $this->getFormattedHeader($this->header);

        $this->body = substr($response, $info['header_size']);
        $this->formattedBody = json_decode($this->body, true) ?: null;
    }

    /**
     * @return boolean | Is Successful or not
     */
    public function isSuccess()
    {
        return $this->httpStatus == self::OK;
    }

    /**
     * @return boolean
     */
    public function isMovedPermanently()
    {
        return $this->httpStatus == self::MOVED_PERMANENTLY;
    }

    /**
     * @return boolean
     */
    public function isRedirected()
    {
        return $this->httpStatus == self::FOUND;
    }

    /**
     * @return boolean
     */
    public function notModified()
    {
        return $this->httpStatus == self::NOT_MODIFIED;
    }

    /**
     * @return boolean
     */
    public function isBadRequest()
    {
        return $this->httpStatus == self::BAD_REQUEST;
    }

    /**
     * @return boolean
     */
    public function isUnauthorized()
    {
        return $this->httpStatus == self::UNAUTHORIZED;
    }

    /**
     * @return boolean
     */
    public function isForbidden()
    {
        return $this->httpStatus == self::FORBIDDEN;
    }

    /**
     * @return boolean
     */
    public function notFound()
    {
        return $this->httpStatus == self::NOT_FOUND;
    }

    /**
     * @return boolean
     */
    public function notAllowedMethod()
    {
        return $this->httpStatus == self::METHOD_NOT_ALLOWED;
    }

    /** @return string */
    public function getHttpMessage()
    {
        return self::HTTP_MSG[$this->httpStatus];
    }

    /**
     * @return string
     */
    public function getRawHeader()
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getRawBody()
    {
        return $this->body;
    }

    /**
     * @param string $name
     * @return string the value of response header with the given name
     */
    public function getHeader($name)
    {
        return isset($this->formattedHeader[$name]) ? $this->formattedHeader[$name] : null;
    }

    /**
     * @return boolean 
     */
    public function isJSON()
    {
        return !is_null($this->formattedBody);
    }

    public function __get($name)
    {
        return isset($this->formattedBody[$name]) ? $this->formattedBody[$name] : null;
    }

    private function getFormattedHeader($header)
    {
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
}