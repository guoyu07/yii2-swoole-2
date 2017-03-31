<?php

namespace xutl\swoole\worker;

use xutl\swoole\Worker;

/**
 * Class WorkerTCP
 * @package xutl\swoole\worker
 */
abstract class Tcp extends Worker
{
    /**
     * @param \Swoole\Server $server
     * @param $fd
     * @param $fromId
     * @param $data
     */
    abstract public function onReceive($server, $fd, $fromId, $data);
}