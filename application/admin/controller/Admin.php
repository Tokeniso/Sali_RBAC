<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/4/19
 * Time: 16:33
 */
namespace app\admin\controller;

use app\admin\model\Map;
use app\admin\model\Node;

class Admin extends Common {

    protected $user;

    public function __construct(){
        parent::__construct();

        $this->user = session('glasses_user');
        if(empty($this->user))
            $this->redirect(url('common/login'));

        //当前用户权限组
        $role_id = $this->user['role_id'];
        //访问的节点信息
        $url = strtolower(request()->controller() . '/' . request()->action());
        $node = Node::getNodeByUrl($url);
        //没有节点
        if(empty($node))
            $this->error('没有权限');

        //获取节点路径
        $road_nav = Node::getNodeRoad($node['pid']);
        $node['url_show'] = false;
        $road_nav[] = $node;
        //权限组允许节点
        $allowAccess = Map::getAllowNode($role_id, $this->user['id']);

        //没有权限，跳转至无权限
        if(empty($allowAccess) || !in_array($node['id'], $allowAccess))
            $this->error('没有权限');
        //当前顶级节点id
        $fId = Node::topNodeId($node['id']);

        //查找展示导航，按钮，操作
        list($nav, $actions, $buttons) = Node::showNodes($allowAccess, $fId, $node['id']);

        $this->assign('seo', $node);
        $this->assign('buttons', $buttons);
        $this->assign('actions', $actions);
        $this->assign('nav', $nav);
        $this->assign('road_nav', $road_nav);
        $this->assign('admin', $this->user);
    }

    /**
     * 系统页面
     * @return mixed
     * @author szh
     */
    public function index(){
        $info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time').'秒',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s",time()+8*3600),
            'ThinkPHP版本' => app()->version(),
        );

        $system = config('system');
        $this->assign('system',$system);
        $this->assign('info',$info);
        return $this->fetch();
    }
}