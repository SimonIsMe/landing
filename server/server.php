<?php
$loader = require_once __DIR__.'/../vendor/autoload.php';
$loader->add('server', __DIR__.'/../');

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoConnection;
use server\Connection;
use server\NotificationManger;
use \server\Response\Response;

define('PATH', __DIR__ . '/../');

class Server implements MessageComponentInterface
{
    private $_connections = [];

    public function onOpen(ConnectionInterface $conn)
    {
        $connection = new Connection($conn);
        $this->_connections[] = $connection;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $connection = $this->findConnection($from);
        $connection->parseRequest($msg);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $key = $this->findKeyConnection($conn);

        $connection = $this->findConnection($conn);
        NotificationManger::removeAllListeners($connection);

        if ($key >= 0)
            unset($this->_connections[$key]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }

    public function findKeyConnection(\Ratchet\WebSocket\Version\RFC6455\Connection $connection) : int
    {
        foreach ($this->_connections as $k => $connectionItem)
            if ($connectionItem->compareConnections($connection))
                return $k;

        return -1;
    }

    public function findConnection(\Ratchet\WebSocket\Version\RFC6455\Connection $conn) : Connection
    {
        foreach ($this->_connections as $k => $connectionItem)
            if ($connectionItem->compareConnections($conn))
                return $connectionItem;

        return null;
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Server()
        )
    ),
    8080
);

$server->run();