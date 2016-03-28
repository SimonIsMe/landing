<?php namespace server\Request\Query;

use server\Connection;
use server\NotificationManger;
use server\Repository\RepositoryManager;
use server\Response\Response;

class GetIdQuery extends AbstractQuery
{
    const LABEL_TYPE = 'getIdQuery';

/**
{
    type: 'getIdQuery',
    queryId: 'aaabbbccc123'
    model: 'Article',
    modelId: 'abc123'
}
 */
    public static function execute(array $query, Connection $connection)
    {
        $modelRepo = RepositoryManager::getRepo($query['model']);
        $modelParams = $modelRepo->find($query['modelId']);

        NotificationManger::addListener($connection, $query['model'], $query['modelId']);

        $response = new Response(Response::RESPONSE_CODE_OK);
        $response->setData([
            'queryId' => $query['queryId'],
            'data' => $modelParams
        ]);
        $connection->send($response);
    }
}