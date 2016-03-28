<?php namespace server;

use server\Request\Request;
use server\Response\Response;

class Connection
{

    /**
     * @var \Ratchet\WebSocket\Version\RFC6455\Connection
     */
    private $_connection;

    /**
     * @var string
     */
    private $_demoId;

    /**
     * @param \Ratchet\WebSocket\Version\RFC6455\Connection $conn
     */
    public function __construct(\Ratchet\WebSocket\Version\RFC6455\Connection $conn)
    {
        $this->_connection = $conn;
    }

    /**
     * @return string
     */
    public function getDemoId()
    {
        return $this->_demoId;
    }

    /**
     * @param string $demoId
     */
    public function setDemoId($demoId)
    {
        $this->_demoId = $demoId;
    }

    public function compareConnections(\Ratchet\WebSocket\Version\RFC6455\Connection $conn)
    {
        return $this->_connection == $conn;
    }

    public function parseRequest($message)
    {
//        {
//            demoId: 'aaa',
//            userId: 'aaabbbccc123',
//            queries: [
//                {
//                    ...
//                },
//                {
//                    ...
//                },
//            ]
//        }

        $request = $this->_parse($message);
        if ($request === false)
            return;

        $request->executeQueries();
    }

    private function _parse(string $message)
    {
        $json = json_decode($message, 1);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:

                if (isset($json[Request::REQUEST_SESSION_ID_LABEL])
                    && isset($json[Request::REQUEST_QUERIES_LABEL]))
                {
                    $sessionId = $json[Request::REQUEST_SESSION_ID_LABEL];
                    $queries = $json[Request::REQUEST_QUERIES_LABEL];

                    return new Request($sessionId, $queries, $this);
                }

                $errorMessage = 'There should be "' . Request::REQUEST_SESSION_ID_LABEL . '" and "' . Request::REQUEST_QUERIES_LABEL . '" keys.';
                break;
            case JSON_ERROR_DEPTH:
                $errorMessage = ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $errorMessage = ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $errorMessage = ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $errorMessage = ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $errorMessage = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $errorMessage = ' - Unknown error';
                break;
        }

        $this->_connection->send(
            new Response(Response::RESPONSE_CODE_PARSE_ERROR, $errorMessage)
        );
        return false;
    }

    public function send(Response $response)
    {
        $this->_connection->send($response->__toString());
    }
}