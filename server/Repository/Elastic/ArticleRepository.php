<?php namespace server\Repository\MySQL;

use server\Repository\Contracts\ArticleInterface;

class ArticleRepository implements ArticleInterface
{
    public function __construct()
    {
        $config = require PATH.'/config.php';

        $client = new \Elasticsearch\Client([
            'hosts' => [
                $config['bonsai']['url']
            ],
            'connectionParams' => [
                'auth' => [$config['bonsai']['login'], $config['bonsai']['password'], 'Basic']
            ]
        ]);
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function insert(array $params)
    {

    }

    public function update($id, array $params)
    {

    }
}