<?php namespace server\Request\Query;

use server\Connection;
use server\NotificationManger;
use server\Repository\RepositoryManager;
use server\Response\Response;

class UpdateQuery extends AbstractQuery
{
    const LABEL_TYPE = 'updateQuery';

    /**
{
    type: 'updateQuery',
    id: 'aaabbbccc123'
    model: 'Article',
    modelId: 'abc123',
    params: {
        title: 'Tytuł',
        content: 'Lorem ispum dolorem'
    }
}
     */

    public static function execute(array $query, Connection $connection)
    {
        $modelRepo = RepositoryManager::getRepo($query['model']);
        $modelParams = $modelRepo->update($query['modelId'], $query['params']);

        NotificationManger::notify($connection, $modelParams, $query['model'], $query['modelId']);

        $response = new Response(Response::RESPONSE_CODE_OK);
        $response->setData([
            'queryId' => $query['queryId'],
            'data' => $modelParams
        ]);

        $connection->send($response);
    }
}