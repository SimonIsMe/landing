<?php namespace server\Repository;

use server\Repository\MySQL\{
    ArticleRepository,
    CommentRepository,
    UserRepository
};

class RepositoryManager
{
    public static $_repos;

    public static function getRepo($name)
    {
        self::_initRepos();
        return self::$_repos[$name];
    }

    private static function _initRepos()
    {
        if (self::$_repos != null) {
            return;
        }

        self::$_repos = [
            'User'      => new UserRepository(),
            'Article'   => new ArticleRepository(),
            'Comment'   => new CommentRepository()
        ];
    }
}