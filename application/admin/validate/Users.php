<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\admin\validate;

use think\Validate;

class Users extends Validate
{

    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'verify' => 'require|captcha',
        'phone' => 'require|phone|token',
        'pass' => 'require|length:32',
    ];

    protected $message = [
        'verify.require' => '验证码不能为空',
        'verify.captcha' => '验证码错误',
        'phone.require' => '手机号码不能为空',
        'phone.phone' => '手机号码格式错误',
        'phone.token' => '登录超时',
        'pass.require' => '密码不能为空',
        'pass.length' => '密码加密错误',
    ];

    /**
     * 验证手机号码
     * @param $value
     * @return string
     * @author szh
     */
    protected function phone($value)
    {
        if (empty(is_phone($value))) {
            return '手机号码格式错误';
        }
        return true;
    }

    /**
     * 验证场景
     * @var array
     */
    protected $scene = [
        //更新
        'update' => ['phone', 'pass'],
        //登录
        'login' => ['verify', 'phone', 'pass'],
    ];

}