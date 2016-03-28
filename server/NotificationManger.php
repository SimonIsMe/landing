<?php namespace server;

use server\Response\Response;

class NotificationManger
{
    private static $_modelIdListeners = [];
    private static $_modelListeners = [];

    public static function addListener(Connection $connection, string $modelName, string $modelId = null)
    {
        if ($connection->getDemoId() == null)
            return;

        if ($modelId == null) {
            self::$_modelListeners[$connection->getDemoId()][$modelName][] = $connection;
        } else {
            self::$_modelIdListeners[$connection->getDemoId()][$modelName][$modelId][] = $connection;
        }
    }

    public static function removeListener(Connection $connection, string $modelName, string $modelId = null)
    {
        if ($connection->getDemoId() == null)
            return;

        if ($modelId == null) {
            foreach (self::$_modelIdListeners[$connection->getDemoId()][$modelName] as $k => $val)
                if ($val == $connection)
                    unset(self::$_modelIdListeners[$connection->getDemoId()][$modelName][$k]);
        } else {
            foreach (self::$_modelIdListeners[$connection->getDemoId()][$modelName][$modelId] as $k => $val)
                if ($val == $connection)
                    unset(self::$_modelIdListeners[$connection->getDemoId()][$modelName][$modelId][$k]);
        }
    }

    public static function removeAllListeners(Connection $connection)
    {
        foreach (self::$_modelListeners[$connection->getDemoId()] as $modelName => $array) {
            foreach ($array as $k => $val) {
                if ($val == $connection) {
                    unset(self::$_modelListeners[$connection->getDemoId()][$modelName][$k]);
                }
            }
        }

        foreach (self::$_modelIdListeners[$connection->getDemoId()] as $modelName => $array) {
            foreach ($array as $modelId => $nextArray) {
                foreach ($nextArray as $k => $val) {
                    unset(self::$_modelIdListeners[$connection->getDemoId()][$modelName][$modelId][$k]);
                }
            }
        }
    }

    public static function notify(Connection $connection, array $data, string $modelName, string $modelId = null)
    {
        $response = new Response(Response::RESPONSE_CODE_OK);
        $response->setData($data);

        if ($modelId == null) {
            foreach (self::$_modelListeners[$connection->getDemoId()] as $modelName) {
                foreach (self::$_modelListeners[$connection->getDemoId()][$modelName] as $conn) {
                    $conn->send($response);
                }
            }
        } else {
            foreach (self::$_modelIdListeners[$connection->getDemoId()] as $modelName) {
                foreach (self::$_modeIdlListeners[$connection->getDemoId()][$modelName] as $modelId) {
                    foreach (self::$_modeIdlListeners[$connection->getDemoId()][$modelName][$modelId] as $conn) {
                        $conn->send($response);
                    }
                }
            }
        }
    }
}