<?php namespace server\Repository\MySQL;

use server\Repository\Contracts\ArticleInterface;

class ArticleRepository implements ArticleInterface
{
    public function find($id)
    {
        return [
            "id" => $id,
            "title" => 'aa',
            "content" => 'Lorem ipsum dolorem'
        ];
    }

    public function insert(array $params)
    {

    }

    public function update($id, array $params)
    {

    }
}