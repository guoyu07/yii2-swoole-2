<?php

namespace xutl\swoole\async;

use yii\base\InvalidConfigException;

class Redis extends Pool
{
    /**
     * @var string redis host
     */
    public $host;

    /**
     * @var int redis port
     */
    public $port = 6379;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty ($this->host)) {
            throw new InvalidConfigException ('The "host" property must be set.');
        }
    }

    protected function connect()
    {
        $redis = new \Swoole\Redis();
        $redis->on('close', function ($redis) {
            $this->remove($redis);
        });

        return $redis->connect($this->host, $this->port, function ($redis, $result) {
            if ($result) {
                $this->join($redis);
            } else {
                $this->failure();
                trigger_error("connect to redis server[{$this->host}:{$this->port}] failed. Error: {$redis->errMsg}[{$redis->errCode}].");
            }
        });
    }

    /**
     * 关闭连接池
     */
    public function close()
    {
        foreach ($this->resourcePool as $conn) {
            /**
             * @var $conn \Swoole\Redis
             */
            $conn->close();
        }
    }

    function __call($call, $params)
    {
        return $this->request(function (\Swoole\Redis $redis) use ($call, $params) {
            call_user_func_array([$redis, $call], $params);
            //必须要释放资源，否则无法被其他重复利用
            $this->release($redis);
        });
    }
}