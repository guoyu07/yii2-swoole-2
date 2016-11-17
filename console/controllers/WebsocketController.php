<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\swoole;

use yii\console\Controller;

class WebsocketController extends Controller
{
    public $component = 'websocket';

    /**
     * 启动服务器
     * @param $server
     */
    public function actionStart($server)
    {
        $WebsocketServer = new \morozovsk\websocket\Server(Yii::$app->get($this->component)->servers[$server]);
        call_user_func(array($WebsocketServer, 'start'));
    }

    /**
     * 停止服务器
     * @param $server
     */
    public function actionStop($server)
    {
        $WebsocketServer = new \morozovsk\websocket\Server(Yii::$app->get($this->component)->servers[$server]);
        call_user_func(array($WebsocketServer, 'stop'));
    }

    /**
     * 重启服务器
     * @param $server
     */
    public function actionRestart($server)
    {
        $WebsocketServer = new \morozovsk\websocket\Server(Yii::$app->get($this->component)->servers[$server]);
        call_user_func(array($WebsocketServer, 'restart'));
    }
}