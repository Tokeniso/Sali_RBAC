<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/5/2
 * Time: 16:32
 */
namespace app\admin\controller;

class Orders extends Admin
{

    public function index()
    {
        tasks_push([
            'type' => 'email',
            'data' => '946206343@qq.com'
        ]);
        $this->assign('list', "{}");
        return $this->fetch();
    }

    public function add()
    {

        return $this->fetch();
    }
}