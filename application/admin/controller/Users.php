<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\admin\controller;

use app\admin\model\Users as UM;
use app\ucenter\model\Users as UC; //全局用户模型
use app\admin\model\Role;

class Users extends Admin
{
    public function index()
    {

        return $this->fetch();
    }

    /**
     * 管理员
     * @author szh
     */
    public function admin()
    {
        $admins = UM::getAdmins();
        if ($admins) {
            foreach ($admins as &$user) {
                $user = query_user('id,name,avatar,role_id,last_ip,last_login,phone,email', $user);
                $user['last_login'] = friendly_date($user['last_login'], 'full');
                $role = Role::getRoleById($user['role_id']);
                $user['role_name'] = $role['name'] ?? '未定义';
            }
        }

        $this->assign('admins', json_encode($admins));
        return $this->fetch();
    }

    /**
     * 添加管理员权限给用户
     * @author szh
     */
    public function addAdmin()
    {
        if (request()->isAjax()) {
            $uid = input('id/d', 0);
            $user = query_user('id,is_admin', $uid);
            if (empty($user))
                $this->ajaxError('用户不存在');
            if ($user['is_admin'])
                $this->ajaxError('用户已经是管理员了');
            $res = UC::updateUser(['is_admin' => 1], $uid);
            if (!$res)
                $this->ajaxError('添加失败');
            cache('gl_admin_list', null);
            $this->ajaxSuccess('添加成功', url('users/admin'));
        }
        return $this->fetch();
    }

    /**
     * 移除用户的管理员权限
     * @author szh
     */
    public function removeAdmin()
    {
        if (request()->isAjax()) {
            $uid = input('id/d', 0);
            $user = query_user('id,is_admin', $uid);
            if (empty($user))
                $this->ajaxError('用户不存在');
            if (!$user['is_admin'])
                $this->ajaxSuccess('已移除');
            $res = UC::updateUser(['is_admin' => 0], $uid);
            if (!$res)
                $this->ajaxError('移除失败');
            cache('gl_admin_list', null);
            $this->ajaxSuccess('已移除');
        }
    }

    /**
     * 设置用户权限组
     * @author szh
     */
    public function setRole()
    {
        $uid = input('id/d', 0);
        //检测用户
        $user = query_user('role_id', $uid);
        if (empty($user))
            $this->error('用户不存在');
        if (request()->isAjax()) {
            $role_id = input('role_id/d', 0);
            //检测权限组真实性
            $role = Role::getRoleById($role_id);
            if (empty($role))
                $this->ajaxError('权限组不存在');
            if ($role_id === $user['role_id'])
                $this->ajaxSuccess('设置成功', url('users/admin'));
            //更新用户
            $res = UC::updateUser(['role_id' => $role_id], $uid);
            if (!$res)
                $this->ajaxError('设置失败');
            $this->ajaxSuccess('设置成功', url('users/admin'));
        }
        $role_id = $user['role_id'] ? $user['role_id'] : 0;
        //获取全部权限组
        $roles = Role::listRole();
        //添加无角色组
        $roles[] = [
            'id' => 0,
            'name' => '无角色',
        ];
        $this->assign('role', $roles);
        $this->assign('role_id', $role_id);

        return $this->fetch();
    }
}