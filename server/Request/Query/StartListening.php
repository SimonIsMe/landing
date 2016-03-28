<?php namespace server\Request\Query;

use server\Connection;
use server\NotificationManger;
use server\Repository\RepositoryManager;
use server\Response\Response;

class StartListening extends AbstractQuery
{
    const LABEL_TYPE = 'startListening';

/**
{
    type: 'startListening',
    queryId: 'aaabbbccc123'
    model: 'Article',
    [ modelId: 'abc123' ]
}
 */
    public static function execute(array $query, Connection $connection)
    {

        if (isset($query['modelId'])) {
            NotificationManger::addListener($connection, $query['model'], $query['modelId']);
        } else {
            NotificationManger::addListener($connection, $query['model']);
        }

    }
}