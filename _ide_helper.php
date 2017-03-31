<?php

namespace {

    define('HTTP_GLOBAL_ALL', 1);
    define('HTTP_GLOBAL_GET', 2);
    define('HTTP_GLOBAL_POST', 4);
    define('HTTP_GLOBAL_COOKIE', 8);
    define('WEBSOCKET_OPCODE_TEXT', 1);

    define('SWOOLE_VERSION', '1.8.8'); //当前Swoole的版本号

    /**
     * new Server 构造函数参数
     */
    define('SWOOLE_BASE', 1);     //使用Base模式，业务代码在Reactor中直接执行
    define('SWOOLE_THREAD', 2);   //使用线程模式，业务代码在Worker线程中执行
    define('SWOOLE_PROCESS', 3);  //使用进程模式，业务代码在Worker进程中执行
    define('SWOOLE_PACKET', 0x10);

    /**
     * new Client 构造函数参数
     */
    define('SWOOLE_SOCK_TCP', 1);           //创建tcp socket
    define('SWOOLE_SOCK_TCP6', 3);          //创建tcp ipv6 socket
    define('SWOOLE_SOCK_UDP', 2);           //创建udp socket
    define('SWOOLE_SOCK_UDP6', 4);          //创建udp ipv6 socket
    define('SWOOLE_SOCK_UNIX_DGRAM', 5);    //创建udp socket
    define('SWOOLE_SOCK_UNIX_STREAM', 6);   //创建udp ipv6 socket

    define('SWOOLE_SSL', 5);

    define('SWOOLE_TCP', 1);        //创建tcp socket
    define('SWOOLE_TCP6', 2);       //创建tcp ipv6 socket
    define('SWOOLE_UDP', 3);        //创建udp socket
    define('SWOOLE_UDP6', 4);       //创建udp ipv6 socket
    define('SWOOLE_UNIX_DGRAM', 5);
    define('SWOOLE_UNIX_STREAM', 6);

    define('SWOOLE_SOCK_SYNC', 0);  //同步客户端
    define('SWOOLE_SOCK_ASYNC', 1); //异步客户端

    define('SWOOLE_SYNC', 0);   //同步客户端
    define('SWOOLE_ASYNC', 1);  //异步客户端

    /**
     * new Lock构造函数参数
     */
    define('SWOOLE_FILELOCK', 2);   //创建文件锁
    define('SWOOLE_MUTEX', 3);      //创建互斥锁
    define('SWOOLE_RWLOCK', 1);     //创建读写锁
    define('SWOOLE_SPINLOCK', 5);   //创建自旋锁
    define('SWOOLE_SEM', 4);        //创建信号量

    define('SWOOLE_EVENT_WRITE', 1);
    define('SWOOLE_EVENT_READ', 2);
}

namespace Swoole {
    class Server
    {
        /**
         * @var array swoole_server::set()函数所设置的参数会保存到swoole_server::$setting属性上。在回调函数中可以访问运行参数的值。
         * @since 1.6.11
         */
        public $setting = [];

        /**
         * @var int 当前服务器主进程的PID
         * 只能在onStart/onWorkerStart之后获取到
         */
        public $master_pid;

        /**
         * @var int 当前服务器管理进程的PID
         * 只能在onStart/onWorkerStart之后获取到
         */
        public $manager_pid;

        /**
         * @var int 得到当前Worker进程的编号，包括Task进程。
         * int $server->worker_id;
         * 这个属性与onWorkerStart时的$worker_id是相同的。
         *
         * Worker进程ID范围是[0, $serv->setting['worker_num'])
         * task进程ID范围是[$serv->setting['worker_num'], $serv->setting['worker_num'] + $serv->setting['task_worker_num'])
         * 工作进程重启后worker_id的值是不变的
         */
        public $worker_id;

        /**
         * @var int 得到当前Worker进程的操作系统进程ID。与posix_getpid()的返回值相同。
         */
        public $worker_pid;

        /**
         * @var bool true表示当前的进程是Task工作进程
         * false表示当前的进程是Worker进程
         */
        public $taskworker;

        /**
         * @var \Traversable  一个迭代器对象
         *
         * foreach($server->connections as $fd)
         * {
         *      $server->send($fd, "hello");
         * }
         *
         * echo "当前服务器共有 ".count($server->connections). " 个连接\n";
         * @since 1.7.16
         */
        public $connections;

        /**
         * Server constructor.
         * @param string $host 用来指定监听的ip地址，如127.0.0.1，或者外网地址，或者0.0.0.0监听全部地址
         * IPv4使用 127.0.0.1表示监听本机，0.0.0.0表示监听所有地址
         * IPv6使用::1表示监听本机，:: (0:0:0:0:0:0:0:0) 表示监听所有地址
         * @param int $port 监听的端口，如9501，监听小于1024端口需要root权限，如果此端口被占用server->start时会失败
         * @param int $mode 运行的模式，swoole提供了3种运行模式，默认为多进程模式
         * @param int $sock_type 指定socket的类型，支持TCP/UDP、TCP6/UDP6、UnixSock Stream/Dgram 6种
         * 使用$sock_type | SWOOLE_SSL可以启用SSL加密。启用SSL后必须配置ssl_key_file和ssl_cert_file
         * 1.7.11后增加了对Unix Socket的支持，详细请参见 /wiki/page/16.html
         * 构造函数中的参数与swoole_server::addlistener中是完全相同的
         */
        public function __construct(string $host, int $port, int $mode = SWOOLE_PROCESS, int $sock_type = SWOOLE_SOCK_TCP)
        {
        }

        /**
         * 设置swoole_server运行时的各项参数。
         * 服务器启动后通过$serv->setting来访问set函数设置的参数数组。
         * @param array $setting
         */
        public function set(array $setting)
        {
        }

        public function on(string $event, mixed $callback)
        {
        }

        public function addListener(string $host, int $port, $type = SWOOLE_SOCK_TCP)
        {
        }
    }
}