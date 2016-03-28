<?php namespace server\Repository\Contracts;

interface CommentInterface
{
    public function find($id);
    public function insert(array $params);
    public function update($id, array $params);
}