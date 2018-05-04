<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/5/4
 * Time: 17:31
 */
namespace app\install\controller;

use think\Controller;

class Install extends Controller {

    /**
     * 安装-检测扩展
     * @author szh
     */
    public function index(){
        $this->needsExtensions();
        return $this->fetch();
        $lockFile = './install.lock';
        file_put_contents($lockFile, date('Y-m-d H-i-s'));
    }

    private function needsExtensions(){
        if(!extension_loaded('swoole'))
            die('请安装swoole扩展');
        if(!extension_loaded('openssl'))
            die('请安装openssl扩展');
        if(!extension_loaded('sockets'))
            die('请安装sockets扩展');
        if(!extension_loaded('curl'))
            die('请安装curl扩展');
        if(!extension_loaded('mbstring'))
            die('请安装mbstring扩展');
    }
}