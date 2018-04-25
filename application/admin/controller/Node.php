<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/4/20
 * Time: 11:08
 */
namespace app\admin\controller;

use app\admin\model\Node as NodeModel;

class Node extends Admin {

    protected $nodeDb;

    public function initialize(){

        $this->nodeDb = db('admin_node');
    }


    /**
     * 节点列表
     * @return mixed
     * @author szh
     */
    public function index(){
        //获取所有节点
        $list = NodeModel::getNodeList();
        //节点转树状图
        $list = NodeModel::listToTree($list);
        //节点树状图转列表
        $list = NodeModel::treeToList($list);

        $this->assign('list', json_encode($list));

        return $this->fetch();
    }

    /**
     * 编辑节点
     * @return mixed
     * @author szh
     */
    public function add(){
        $nodeModel = new NodeModel();
        if(request()->isAjax()){
            $data = input('form/a', []);
            $validate = validate('admin/node');

            if(!$validate->check($data))
                $this->ajaxError($validate->getError());

            $name = '新增';
            if($data['id']){
                $node = $nodeModel::getNodeById($data['id']);
                if(empty($node))
                    $this->ajaxError('错误的id');
                $name = '编辑';
                $res = $nodeModel->where('id', $data['id'])->update($data);
            } else {
                $res = $nodeModel->insertGetId($data);
            }
            if(!$res)
                $this->ajaxError($nodeModel->getError() ?? $name . '失败');
            if($data['id']){
                if($data['auth'] !== $node['auth'])
                    cache('public_node_list_' . $data['auth'], null);

                cache('node_url_' . $node['url'], null);
                cache('node_id_' . $data['id'], null);
            } else {
                cache('public_node_list_' . $data['auth'], null);
                cache('node_all_id', null);
            }
            $this->ajaxSuccess($name . '成功', url('node/index'));
        }
        $id = input('get.id/d', 0);
        $type = input('get.type/s', '');
        if($id){
            $node = NodeModel::getNodeById($id);
            if(in_array($type, ['addNav', 'addBtn', 'addAct'])){
                if(empty($node))
                    $this->error('错误的id', url('node/index'));
                $this->$type($node);
            }else{
                $this->assign('node', $node);
            }
        }
        return $this->fetch();
    }


    /**
     * 添加子导航
     * @param $node
     * @return mixed
     * @author szh
     */
    private function addNav($node){
        if($node['type'] !== 0)
            $this->error('父级不是导航', url('node/index'));
        $this->assign('node', [
            'pid' => $node['id'],
            'type' => 1,
        ]);
    }

    /**
     * 新增按钮
     * @return mixed
     * @author szh
     */
    private function addBtn($node){
        if($node['type'] !== 1)
            $this->error('父级不是子导航', url('node/index'));
        $this->assign('node', [
            'pid' => $node['id'],
            'type' => 2,
        ]);
    }

    /**
     * 新增操作
     * @return mixed
     * @author szh
     */
    private function addAct($node){
        if($node['type'] !== 1)
            $this->error('父级不是子导航', url('node/index'));
        $this->assign('node', [
            'pid' => $node['id'],
            'type' => 3,
        ]);
    }

    /**
     * 删除节点
     * @author szh
     */
    public function delete(){
        $id = input('get.id/d', 0);
        $node = NodeModel::getNodeById($id);
        if(empty($node))
            $this->ajaxError('错误的id');

        $res = NodeModel::where('id', $id)->delete();
        if(!$res)
            $this->ajaxError('删除失败');
        cache('node_url_' . $node['url'], null);
        cache('node_id_' . $id, null);
        cache('node_all_id', null);
        $this->ajaxSuccess('删除成功');
    }
}