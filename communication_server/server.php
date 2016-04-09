<?php
$loader = require_once __DIR__.'/../vendor/autoload.php';
$loader->add('communication_server', __DIR__.'/../');

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

    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $json = json_decode($msg);
        var_dump($json);

        if (!isset($this->_connections[$json->token])) {
            $this->_connections[$json->token] = [];
        }

        $exist = false;
        foreach ($this->_connections[$json->token] as $connection) {
            if ($connection === $from) {
                $exist = true;
                break;
            }
        }

        if ($exist == false) {
            $this->_connections[$json->token][] = $from;
        }

        if ($json->message != '' && $json->type != '') {
            $response = json_encode($json);

            foreach ($this->_connections[$json->token] as $connection) {
                if ($connection != $from) {
                    $connection->send($response);
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        foreach ($this->_connections as $token => $connections) {
            foreach ($connections as $k => $connection) {
                if ($connection === $conn) {
                    unset($this->_connections[$token][$k]);
                    if (empty($this->_connections[$token])) {
                        unset($this->_connections[$token]);
                    }
                    break;
                }
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
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

/*

1. user łączy się z WS serwerem

2. user wysyła pustą wiadomość (tylko z tokenem) { "token":"abc123", "from":"Szymon", "message":"", "type":"" }

3. serwer wrzuca go do zbioru połączeć wspólnego z danym tokenem

4. serwer po odebraniu wiadomości wysyła ją wszystkim klienom o tym samym tokenie














 */