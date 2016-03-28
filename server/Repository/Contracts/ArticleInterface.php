<?php namespace server\Repository\Contracts;

interface ArticleInterface
{
    /**
     * @param string $id
     * @return array params
     */
    public function find($id);

    /**
     * @param array $params
     *
     * @return array params
     */
    public function insert(array $params);

    /**
     * @param string $id
     * @param array $params
     *
     * @return array params
     */
    public function update($id, array $params);
}