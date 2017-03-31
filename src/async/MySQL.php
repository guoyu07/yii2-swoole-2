<?php

namespace xutl\swoole\async;

use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * MySQLi 的异步连接池
 *
 * @package xutl\swoole\async
 */
class MySQL extends Pool
{
    /**
     * @var string mysql host
     */
    public $host;

    /**
     * @var int mysql port
     */
    public $port = 3306;

    public function init()
    {
        parent::init();
        if (empty ($this->host)) {
            throw new InvalidConfigException ('The "host" property must be set.');
        }
    }

    /**
     * 连接服务器
     */
    protected function connect()
    {
        $db = new \Swoole\MySQL();
        $db->on('close', function ($db) {
            $this->remove($db);
        });

        $db->connect([
            'host' => $this->host,
            'port' => $this->port
        ], function ($db, $result) {
            if ($result) {
                $this->join($db);
            } else {
                $this->failure();
                trigger_error("connect to mysql server[{$this->host}:{$this->port}] failed. Error: {$db->connect_error}[{$db->connect_errno}].");
            }
        });
    }

    /**
     * 关闭连接池
     */
    public function close()
    {
        /**
         * @var $conn \Swoole\MySQL
         */
        foreach ($this->resourcePool as $conn) {
            $conn->close();
        }
    }

    /**
     * 发起一个异步查询请求
     *
     * 成功返回 true（但还没执行 $callback）
     *
     * @param string $sql
     * @param callable $callback
     * @return bool
     */
    function query($sql, callable $callback)
    {
        return $this->request(function (\Swoole\MySQL $db) use ($callback, $sql) {
            return $db->query($sql, function (\Swoole\MySQL $db, $result) use ($callback) {
                call_user_func($callback, $db, $result);
                $this->release($db);
            });
        });
    }
}