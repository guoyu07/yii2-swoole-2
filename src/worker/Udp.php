<?php

namespace xutl\swoole;

use xutl\swoole\Worker;

abstract class Udp extends Worker
{
    /**
     * UDP下收到数据回调
     *
     * @param $server
     * @param $data
     * @param $clientInfo
     */
    abstract public function onPacket($server, $data, $clientInfo);
}