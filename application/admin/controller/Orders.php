<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\admin\controller;

class Orders extends Admin
{
    private $ordersM;

    public function __construct()
    {
        parent::__construct();

        $this->ordersM = model('admin/orders');
    }

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

    /**
     * 用户积分策略
     * @author szh <sali_hub@163.com>
     */
    public function integer()
    {
        //设置积分策略
        if (request()->isAjax()) {
            $integer = input('integer/f', 10);
            $res = $this->ordersM->setInteger($integer);
            if (!$res)
                $this->ajaxError('设置失败');
            $this->ajaxSuccess('设置成功', url('orders/integer'));
        }
        $integer = $this->ordersM->getInteger();
        $this->assign('integer', $integer);
        return $this->fetch();
    }
}