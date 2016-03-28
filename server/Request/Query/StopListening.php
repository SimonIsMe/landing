<?php namespace server\Request\Query;

use server\Connection;
use server\NotificationManger;
use server\Repository\RepositoryManager;
use server\Response\Response;

class StopListening extends AbstractQuery
{
    const LABEL_TYPE = 'stopListening';

/**
{
    type: 'stopListening',
    queryId: 'aaabbbccc123'
    model: 'Article',
    [ modelId: 'abc123' ]
}
 */
    public static function execute(array $query, Connection $connection)
    {

        if (isset($query['modelId'])) {
            NotificationManger::removeListener($connection, $query['model'], $query['modelId']);
        } else {
            NotificationManger::removeListener($connection, $query['model']);
        }

    }
}