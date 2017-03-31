<?php
namespace xutl\swoole;

class WorkerAPI extends WorkerHttp
{
    /**
     * 接口前缀
     *
     * @var string
     */
    public $prefix = '/api/';

    /**
     * 前缀长度
     *
     * @var int
     */
    public $prefixLength = 5;


    /**
     * 判断是否API路径
     *
     * 默认 /api/ 路径开头为API路径
     *
     * @param \Swoole\Http\Request $request
     * @return bool
     */
    public function isApi($request)
    {
        if (substr($request->server['request_uri'], 0, $this->prefixLength) === $this->prefix)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * HTTP 接口请求处理的方法
     *
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public function onRequest($request, $response)
    {
        $response->header('Content-Type', 'application/json');

        try
        {
            if (!$this->verify($request))
            {
                $response->status(401);
            }

            $uri  = $this->uri($request);
            $file = __DIR__ .'/../../../../api/'. $uri . (substr($uri, -1) === '/' ? 'index' : '') .'.php';
            $this->debug("request api: $file");

            if (!is_file($file))
            {
                throw new \Exception('can not found api', 1);
            }

            $rs = include($file);
            if (!$rs)
            {
                throw new \Exception('api result empty', 2);
            }
            elseif (is_string($rs))
            {
                $rs = ['data' => $rs, 'status' => 'success'];
            }
            elseif (!is_array($rs))
            {
                $rs = (array)$rs;
            }

            if (!isset($rs['status']))$rs['status'] = 'success';

            $response->end(json_encode($rs, JSON_UNESCAPED_UNICODE));
        }
        catch (\Exception $e)
        {
            $response->status(500);
            $response->end(json_encode(['status' => 'error', 'code' => - $e->getCode(), 'msg' => $e->getMessage()], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 返回当前URI部分（不含前缀）
     *
     * @param \Swoole\Http\Request $request
     * @return string
     */
    protected function uri($request)
    {
        return substr($request->server['request_uri'], $this->prefixLength);
    }

    /**
     * 验证API是否通过
     *
     * 请自行扩展
     *
     * @param \Swoole\Http\Request $request
     * @return bool
     */
    protected function verify($request)
    {
        return true;
    }
}