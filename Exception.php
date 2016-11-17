<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\swoole;

use yii\base\UserException;


class Exception extends UserException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Error';
    }
}