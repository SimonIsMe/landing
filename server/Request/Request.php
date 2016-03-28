<?php namespace server\Request;

use server\Connection;
use server\Request\Query\GetIdQuery;
use server\Request\Query\InsertQuery;
use server\Request\Query\StopListening;
use server\Request\Query\UpdateQuery;

class Request
{
    const REQUEST_SESSION_ID_LABEL = 'sessionId';
    const REQUEST_QUERIES_LABEL = 'queries';

    /**
     * @var string
     */
    private $_sessionId;

    /**
     * @var array
     */
    private $_queries;

    /**
     * @var Connection
     */
    private $_connection;

    public function __construct(string $sessionId, array $queries, Connection $connection)
    {
        $this->_sessionId = $sessionId;
        $this->_queries = $queries;
        $this->_connection = $connection;
    }

    public function executeQueries()
    {
        foreach ($this->_queries as $query) {

            if (!isset($query['type'])) {
                continue;
            }

            switch ($query['type']) {
                case GetIdQuery::LABEL_TYPE:
                    GetIdQuery::execute($query, $this->_connection);
                    break;
                case UpdateQuery::LABEL_TYPE:
                    UpdateQuery::execute($query, $this->_connection);
                    break;
                case InsertQuery::LABEL_TYPE:
                    InsertQuery::execute($query, $this->_connection);
                    break;
                case StopListening::LABEL_TYPE:
                    StopListening::execute($query, $this->_connection);
                    break;
                default:
                    break;
            }
        }
    }

}