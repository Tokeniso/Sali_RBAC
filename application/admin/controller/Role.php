<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/4/24
 * Time: 15:38
 */
namespace app\admin\controller;

use app\admin\model\Role as RoleModel;
use app\admin\model\Node;
use app\admin\model\Map;
use think\Db;

class Role extends Admin {

    /**
     * 权限组列表
     * @return mixed
     * @author szh
     */
    public function index(){
        $list = RoleModel::listRole();
        $this->assign('list', json_encode($list));
        return $this->fetch();
    }

    /**
     * 新增权限组
     * @return mixed
     * @author szh
     */
    public function add(){
        $this->roleAjax('add');
        return $this->fetch('role/add');
    }

    /**
     * 编辑权限组
     * @return mixed
     * @author szh
     */
    public function edit(){
        $this->roleAjax('edit');
        $id = input('get.id/d', 0);

        $role = RoleModel::getRoleById($id);
        if(empty($role))
            $this->error('错误的ID');
        $this->assign('role', $role);

        return $this->fetch('role/add');
    }

    /**
     * 用于节点控制时，区分新增、编辑功能节点权限
     * @param string $type  传入访问的节点-方法名
     * @author szh
     */
    private function roleAjax($type = 'add'){
        if(request()->isAjax()){
            $roleModel = new RoleModel();
            $data = input('form/a', [], 'text');
            $id = $data['id'] ?? 0;
            if(empty($data['name']) || mb_strlen($data['name']) > 50)
                $this->ajaxError('权限组名称是1-50个字符');
            if(mb_strlen($data['remark']) > 255)
                $this->ajaxError('权限组备注是0-255个字符');
            $name = '新增';
            if($id){
                if($type !== 'edit')
                    $this->ajaxError('越权操作');
                $name = '编辑';
                $res = $roleModel->where('id', $id)->update($data);
            } else {
                if($type !== 'add')
                    $this->ajaxError('越权操作');
                $res = $roleModel->insertGetId($data);
            }
            if(!$res)
                $this->ajaxError($roleModel->getError() ??  $name . '失败');
            $this->ajaxSuccess($name . '成功', url('role/index'));
        }
    }

    /**
     * 删除权限组
     * @author szh
     */
    public function delete(){
        $id = input('get.id/d', 0);

        $role = RoleModel::getRoleById($id);
        if(empty($role))
            $this->ajaxError('错误的ID');
        $res = RoleModel::where('id', $id)->delete();
        if(!$res)
            $this->ajaxError('删除错误');
        $this->ajaxSuccess('删除成功');
    }

    /**
     * 设置权限组
     * @author szh
     */
    public function setMap(){
        $id = input('id/d', 0);
        if($id === 1)
            $this->error('无法修改超级管理员权限');
        if(request()->isAjax()){
            $ids = input('authids/a', []);
            Db::startTrans();
            $mapModel = new Map();
            $res = true;
            if(Map::where('role_id', $id)->where('type', 'role')->find())
                $res = $mapModel->where('role_id', $id)->where('type', 'role')->delete();

            if($res){
                if($ids){
                    $date = [];
                    foreach ($ids as $node_id){
                        $date[] = [
                            'node_id' => text($node_id),
                            'role_id' => $id,
                            'type' => 'role',
                        ];
                    }
                    $res = $mapModel->saveAll($date);
                }

                if($res){
                    Db::commit();
                    cache('allow_node_by_role_id_' . $id, null);
                    $this->ajaxSuccess('设置成功', url('role/index'));
                }
            }
            Db::rollback();
            $this->ajaxError('设置失败');
        }
        $role = RoleModel::getRoleById($id);
        if(empty($role))
            $this->error('错误的ID');
        //当前授权节点
        $hasNode = Map::getRoleNode($id);

        //需要授权的节点
        $nodes = Node::getChildList(0, 0, true, 0, 'list');

        $nodes = Node::listToTree($nodes, $hasNode);
        $this->assign('list', json_encode($nodes));
        $this->assign('role', $role);

        return $this->fetch();
    }
}