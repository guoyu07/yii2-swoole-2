<?php

namespace xutl\swoole;

use yii\base\Configurable;

/**
 * 数据可落地的内存表
 *
 * @package xutl\swoole
 */
class Table extends \Swoole\Table implements Configurable
{
    public $size;

    /**
     * Table constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            foreach ($config as $name => $value) {
                $this->{$name} = $value;
            }
        }
        if ($this->size >= 1) {
            $this->size = bindec(str_pad(1, strlen(decbin((int)$this->size - 1)), 0)) * 2;
        } else {
            $this->size = 1024;
        }
        parent::__construct($this->size);
    }
}