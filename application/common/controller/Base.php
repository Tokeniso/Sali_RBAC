<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\common\controller;

use think\Controller;

class Base extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //TODO 安装检测
        $lockFile = './install.lock';
        if (!file_exists($lockFile)) {
            //$this->redirect(url('install/install/index'));
        }

    }

    /**
     * ajax返回信息
     * @param $code
     * @param $info
     * @param $data
     * @param $url
     * @author szh
     */
    public function ajaxReturn($code, $info, $data, $url)
    {
        if (!is_array($data) && empty($url)) {
            $url = $data;
            $data = [];
        }
        $return = [
            'code' => $code,
            'info' => $info,
            'data' => $data,
            'url' => $url,
        ];
        echo json_encode($return);
        exit;
    }

    /**
     * ajax成功返回
     * @param string $info
     * @param $data
     * @param $url
     * @author szh
     */
    public function ajaxSuccess($info = '操作成功', $data = [], $url = '')
    {
        if (is_string($data)) {
            $url = $data;
            $data = [];
        }
        if (is_array($info)) {
            $data = $info;
            $info = '操作成功';
        }
        $this->ajaxReturn(1, $info, $data, $url);
    }

    /**
     * ajax失败返回
     * @param string $info
     * @param $data
     * @param $url
     * @author szh
     */
    public function ajaxError($info = '操作失败', $data = [], $url = '')
    {
        if (is_string($data)) {
            $url = $data;
            $data = [];
        }
        if (is_array($info)) {
            $data = $info;
            $info = '操作失败';
        }
        $this->ajaxReturn(0, $info, $data, $url);
    }
}