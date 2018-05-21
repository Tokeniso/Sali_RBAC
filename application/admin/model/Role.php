<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\admin\model;

use think\Model;

class Role extends Model
{

    protected $name = 'admin_role';

    /**
     * 权限组列表
     * @return array|\PDOStatement|string|\think\Collection
     * @author szh <sali_hub@163.com>
     */
    public static function listRole()
    {
        $list = self::field(true)->select();
        return $list;
    }


    /**
     * 通过id查找权限组
     * @param $id
     * @return array|null|\PDOStatement|string|Model
     * @author szh <sali_hub@163.com>
     */
    public static function getRoleById($id)
    {
        $role = self::field(true)->find($id);
        return $role;
    }

}