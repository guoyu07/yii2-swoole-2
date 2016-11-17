<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\swoole;

class Request extends \yii\base\Request
{
    private $_params;


    /**
     * Returns the command line arguments.
     * @return array the command line arguments. It does not include the entry script name.
     */
    public function getParams()
    {
        if ($this->_params === null) {
            if (isset($_SERVER['argv'])) {
                $this->_params = $_SERVER['argv'];
                array_shift($this->_params);
            } else {
                $this->_params = [];
            }
        }
        return $this->_params;
    }

    /**
     * Sets the command line arguments.
     * @param array $params the command line arguments
     */
    public function setParams($params)
    {
        $this->_params = $params;
    }

    /**
     * 将当前请求解析为路由和相关参数。
     * @return array the first element is the route, and the second is the associated parameters.
     */
    public function resolve()
    {
        $rawParams = $this->getParams();
        if (isset($rawParams[0])) {
            $route = $rawParams[0];
            array_shift($rawParams);
        } else {
            $route = '';
        }

        $params = [];
        foreach ($rawParams as $param) {
            if (preg_match('/^--(\w+)(?:=(.*))?$/', $param, $matches)) {
                $name = $matches[1];
                $params[$name] = isset($matches[2]) ? $matches[2] : true;
            } elseif (preg_match('/^-(\w+)(?:=(.*))?$/', $param, $matches)) {
                $name = $matches[1];
                $params['_aliases'][$name] = isset($matches[2]) ? $matches[2] : true;
            } else {
                $params[] = $param;
            }
        }

        return [$route, $params];
    }
}