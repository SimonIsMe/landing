<?php namespace server\Request\Query;

use server\Connection;
use server\NotificationManger;
use server\Repository\RepositoryManager;
use server\Response\Response;

class InsertQuery extends AbstractQuery
{
    const LABEL_TYPE = 'insertQuery';

    /**
{
    type: 'insertQuery',
    id: 'aaabbbccc123'
    model: 'Article',
    params: {
        title: 'Tytuł',
        content: 'Lorem ispum dolorem'
    }
}
     */

    public static function execute(array $query, Connection $connection)
    {
        $modelRepo = RepositoryManager::getRepo($query['model']);
        $modelParams = $modelRepo->insert($query['params']);

        NotificationManger::notify($connection, $modelParams, $query['model']);

        $response = new Response(Response::RESPONSE_CODE_OK);
        $response->setData([
            'queryId' => $query['queryId'],
            'data' => $modelParams
        ]);

        $connection->send($response);
    }
}