<?php

namespace xutl\swoole\async;

use yii\base\Object;
use yii\base\Exception;

/**
 * 通用的连接池框架
 */
abstract class Pool extends Object
{
    /**
     * 连接池的尺寸，最大连接数
     *
     * @var int $poolSize
     */
    public $poolSize = 10;

    /**
     * idle connection
     *
     * @var array $resourcePool
     */
    protected $resourcePool = [];

    protected $resourceNum = 0;

    protected $failureCount = 0;

    /**
     * @var \SplQueue
     */
    protected $idlePool;

    /**
     * @var \SplQueue
     */
    protected $taskQueue;

    protected $createFunction;

    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();
        $this->taskQueue = new \SplQueue();
        $this->idlePool = new \SplQueue();
        $this->create([$this, 'connect']);
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * 加入到连接池中
     *
     * @param $resource
     */
    public function join($resource)
    {
        # 保存到空闲连接池中
        $this->resourcePool[spl_object_hash($resource)] = $resource;
        $this->release($resource);
    }

    /**
     * 失败计数
     */
    public function failure()
    {
        $this->resourceNum--;
        $this->failureCount++;
    }

    /**
     * @param $callback
     */
    function create($callback)
    {
        $this->createFunction = $callback;
    }

    /**
     * 修改连接池尺寸
     *
     * @param $newSize
     */
    function setPoolSize($newSize)
    {
        $this->poolSize = $newSize;
    }

    /**
     * 移除资源
     *
     * @param $resource
     * @return bool
     */
    function remove($resource)
    {
        $rid = spl_object_hash($resource);
        if (!isset($this->resourcePool[$rid])) {
            return false;
        }
        unset($this->resourcePool[$rid]);
        $this->resourceNum--;
        return true;
    }

    /**
     * 请求资源
     *
     * @param callable $callback
     * @return bool
     */
    public function request(callable $callback)
    {
        $this->taskQueue->enqueue($callback);
        if (count($this->idlePool) > 0) {
            $this->doTask();
            return true;
        } elseif (count($this->resourcePool) < $this->poolSize and $this->resourceNum < $this->poolSize) {
            call_user_func($this->createFunction);
            $this->resourceNum++;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 释放资源
     *
     * @param $resource
     */
    public function release($resource)
    {
        $this->idlePool->enqueue($resource);
        if (count($this->taskQueue) > 0) {
            $this->doTask();
        }
    }

    public function isFree()
    {
        return 0 == $this->taskQueue->count() && $this->idlePool->count() == count($this->resourcePool);
    }

    protected function doTask()
    {
        $resource = null;
        while (count($this->idlePool) > 0) {
            $_resource = $this->idlePool->dequeue();
            $rid = spl_object_hash($_resource);
            if (!isset($this->resourcePool[$rid])) {
                continue;
            } else {
                $resource = $_resource;
                break;
            }
        }
        if (!$resource) {
            if (count($this->resourcePool) == 0) {
                call_user_func($this->createFunction);
                $this->resourceNum++;
            }

            return;
        }
        $callback = $this->taskQueue->dequeue();
        call_user_func($callback, $resource);
    }

    abstract protected function connect();

    abstract protected function close();
}