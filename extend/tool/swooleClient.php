<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/5/8
 * Time: 14:56
 */
namespace tool\swooleclient;

class swooleClient
{

    public static $instance;
    protected static $config = [
        'host' => '127.0.0.1',
        'port' => '9502',
        'timeout' => '1',
    ];

    public function __construct($config = '')
    {
        if(!(self::$instance instanceof self)){
            if (is_array($config))
                self::$config = array_merge(self::$config, $config);
            self::$instance = new \swoole_client(SWOOLE_SOCK_TCP);
            if (!self::$instance->connect(self::$config['host'], self::$config['port'], self::$config['timeout']))
                throw new \Exception("Connect Error");
        }
    }
}