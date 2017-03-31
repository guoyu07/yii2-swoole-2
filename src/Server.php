<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\swoole;

use Yii;
use yii\base\Component;

/**
 * Class Server
 * @package xutl\swoole
 */
class Server extends Component
{
    public $schemaMap = [
        'http' => 'Swoole\Http\Server', // Http
        'websocket' => 'Swoole\WebSocket\Server', // WebSocket
        'redis' => 'Swoole\Redis\Server', // Redis
        'default' => 'Swoole\Server', // default
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}