<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\admin\model;

use think\Model;
use app\ucenter\model\Users as UC; //全局用户模型

class Users extends Model
{

    /**
     * 用户登录
     * @param $phone
     * @param string $pass 用户密码加密方式  md5(md5(原文 . '_glasses') . '_glasses')
     * @return bool
     * @author szh
     */
    public function login($phone, $pass)
    {
        $uid = UC::getUserByPhone($phone);
        //用户存在性
        if (empty($uid)) {
            $this->error = '用户不存在';
            return false;
        }
        //无权限
        $user = query_user('*', $uid);
        if (empty($user) || $user['is_admin'] === 0 || $user['role_id'] === 0) {
            $this->error = '无权限登录';
            return false;
        }
        //验证密码
        if (md5($pass . '_glasses') !== $user['password']) {
            $this->error = '用户名或密码错误';
            return false;
        }
        $update = [
            'last_ip' => request()->ip(),
            'last_login' => time(),
        ];
        UC::updateUser($update, $uid);
        session('glasses_user', $user);
        return true;
    }


    /**
     * 获取所有管理员
     * @return array|mixed
     * @author szh
     */
    public static function getAdmins()
    {
        $tag = 'gl_admin_list';
        $admins = cache($tag);
        if ($admins === false) {
            $admins = self::where('is_admin', 'eq', '1')->column('id');
            cache($tag, $admins);
        }
        return $admins;
    }
}