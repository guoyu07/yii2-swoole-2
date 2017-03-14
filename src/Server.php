<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\swoole;

use yii\base\Component;

class Server extends Component
{
    /**
     * @var string 绑定地址
     */
    public $host;
    public $port = 9502;
    public $mode = SWOOLE_PROCESS;
    public $sockType;
    public $config = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}