<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\admin\controller;

use think\Controller;

class Index extends Controller {

    public function _empty(){
        $this->error('404', url('admin/index'));
    }
}