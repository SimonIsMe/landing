<?php namespace server\Response;

class Response
{
    const RESPONSE_CODE_OK = 200;
    const RESPONSE_CODE_SERVER_ERROR = 500;
    const RESPONSE_CODE_PARSE_ERROR = 501;
    const RESPONSE_CODE_INCORRECT_REQUEST = 404;
    const RESPONSE_CODE_FORBIDDEN_REQUEST = 403;

    private $_code = self::RESPONSE_CODE_OK;
    private $_message;
    private $_data = [];

    public function __construct($code, $message = null, array $data = [])
    {
        $this->_code = $code;
        $this->_message = $message;
        $this->_data = $data;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->_code = $code;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @param null $message
     */
    public function setMessage($message)
    {
        $this->_message = $message;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    public function __toString() : string
    {
        $response = [];
        $response['code'] = $this->_code;

        if ($this->_message != null)
            $response['message'] = $this->_message;

        if (!empty($this->_data))
            $response['data'] = $this->_data;

        return json_encode($response);
    }

}