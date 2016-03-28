<?php namespace server\Repository\Contracts;

interface UserInterface
{
    public function find($id);
    public function insert(array $params);
    public function update($id, array $params);
}