<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\admin\validate;

use think\Validate;

class Node extends Validate
{

    protected $rule = [
        'name' => 'require|max:20',
        'url' => 'max:50',
        'icon' => 'max:20',
        'sort' => 'number|max:5',
        'extra' => 'max:50',
    ];

    protected $message = [
        'name.require' => '节点名称不能为空',
        'name.max' => '节点名称是1-20个字符',
        'url.max' => '节点url是0-50个字符',
        'icon.require' => '节点图标不能为空',
        'sort.number' => '节点排序为数字',
        'sort.require' => '节点排序最大5位数',
        'extra.max' => '控件属性最多50个字符',
    ];
}