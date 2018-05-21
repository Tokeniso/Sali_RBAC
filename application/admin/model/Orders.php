<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\admin\model;

use think\Model;

class Orders extends Model
{

    /**
     * 获取介绍人积分百分比
     * @param int $id
     * @return int|mixed
     * @author szh <sali_hub@163.com>
     */
    public function getInteger($id = 1)
    {
        $tag = 'orders_integer';
        $integer = cache($tag);
        if ($integer === false) {
            $integer = db('integer')->where('id', $id)->value('value');
            if (empty($integer))
                $integer = 10;
            cache($tag, $integer, 0);
        }
        return $integer;
    }

    /**
     * 设置介绍人积分百分比
     * @param $integer
     * @return bool
     * @author szh <sali_hub@163.com>
     */
    public function setInteger($integer)
    {
        $data = [
            'value' => number_format($integer, 2),
        ];
        $res = db('integer')->where('id', 1)->update($data);
        if (!$res)
            return false;
        cache('orders_integer', $data['value']);
        return true;
    }
}