<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/4/25
 * Time: 14:11
 */
namespace app\common\controller;

use think\Controller;

class Base extends Controller {
    public function __construct(){
        parent::__construct();
        //TODO 安装检测
        $lockFile = './install.lck';
        if(!file_exists($lockFile)){
            file_put_contents($lockFile, date('Y-m-d H-i-s'));
        }

    }
}