<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\admin\controller;

use app\admin\model\Node as NodeModel;

class Node extends Admin
{

    protected $nodeDb;

    public function initialize()
    {

        $this->nodeDb = db('admin_node');
    }


    /**
     * 节点列表
     * @return mixed
     * @author szh <sali_hub@163.com>
     */
    public function index()
    {
        if (request()->isAjax()) {
            $id = input('id/d', 0);
            if (!empty($id)) {
                $sonNodes = NodeModel::getChildList([$id, false], null, true);
                if (!empty($sonNodes)) {
                    $sonNodes = array_merge($sonNodes);
                    $this->ajaxSuccess($sonNodes);
                }
            }
            $this->ajaxError('错误的ID');
        }
        $tId = input('tId/d', 0);//顶级节点
        $sId = input('sId/d', 0);//顶级节点下的子节点

        //获取节点
        $sonNodes = [];
        if (!empty($sId)) {
            //有子搜索选项
            $list = NodeModel::getChildList($sId, null, true);
        } else {
            //某一节点下的子节点
            $list = NodeModel::getChildList($tId, null, true);
        }
        //搜索顶级节点，展示搜索的子节点
        if (!empty($tId))
            $sonNodes = NodeModel::getChildList([$tId, false], null, true);

        $list = array_merge($list);
        //用于搜索的顶级节点
        $topNodes = NodeModel::getChildList([0, false], null, true);

        $this->assign('list', json_encode($list));//展示的列表
        $this->assign('topNodes', $topNodes);//搜索的顶级节点
        $this->assign('sonNodes', $sonNodes);//搜索的子节点
        $this->assign('tId', $tId);//当前搜索的顶级节点id
        $this->assign('sId', $sId);//当前搜索的子节点id

        return $this->fetch();
    }

    /**
     * 新增页面节点
     * @return mixed
     * @author szh <sali_hub@163.com>
     */
    public function add()
    {

        $this->editAssign('edit');

        return $this->fetch();
    }

    /**
     * 编辑节点
     * @return mixed
     * @author szh <sali_hub@163.com>
     */
    public function edit()
    {

        $this->editAssign('edit');

        $id = input('get.id/d', 0);
        $node = NodeModel::getNodeById($id);

        if (empty($node))
            $this->error('错误的id', url('node/index'));
        $this->assign('node', $node);//编辑节点
        return $this->fetch('node/add');
    }


    /**
     * 添加子导航
     * @return mixed
     * @author szh <sali_hub@163.com>
     */
    public function addNav()
    {
        $node = $this->editAssign('addNav');
        if ($node['type'] !== 0)
            $this->error('父级不是导航', url('node/index'));
        $this->assign('node', [
            'pid' => $node['id'],
            'type' => 1,
        ]);
        return $this->fetch('node/add');
    }

    /**
     * 新增按钮
     * @return mixed
     * @author szh <sali_hub@163.com>
     */
    public function addBtn()
    {
        $node = $this->editAssign('addBtn');
        if ($node['type'] !== 1)
            $this->error('父级不是子导航', url('node/index'));
        $this->assign('node', [
            'pid' => $node['id'],
            'type' => 2,
        ]);
        return $this->fetch('node/add');
    }

    /**
     * 新增操作
     * @return mixed
     * @author szh <sali_hub@163.com>
     */
    public function addAct()
    {
        $node = $this->editAssign('addAct');
        if ($node['type'] !== 1)
            $this->error('父级不是子导航', url('node/index'));
        $this->assign('node', [
            'pid' => $node['id'],
            'type' => 3,
        ]);
        return $this->fetch('node/add');
    }

    /**
     * 编辑、新增节点展示数据
     * @param string $type
     * @return array|mixed|null|\PDOStatement|string|\think\Model
     * @author szh <sali_hub@163.com>
     */
    private function editAssign($type)
    {
        $this->nodeAjax($type);
        if (in_array($type, ['addNav', 'addBtn', 'addAct'])) {
            $id = input('get.id/d', 0);
            $node = NodeModel::getNodeById($id);

            if (empty($node))
                $this->error('错误的id', url('node/index'));
            return $node;//新增节点，返回父级
        }
    }

    /**
     * ajax节点操作
     * @param string $type 访问的节点的方法名
     * @author szh <sali_hub@163.com>
     */
    private function nodeAjax($type)
    {
        if (request()->isAjax()) {
            $nodeModel = new NodeModel();

            $data = input('form/a', []);
            $validate = validate('admin/node');

            if (!$validate->check($data))
                $this->ajaxError($validate->getError());

            $pid = $data['pid'] ?? 0;
            $father = $nodeModel::getNodeById($pid);
            //检测权限
            $this->checkRightNode($type, $data, $father);

            $name = '新增';
            if ($data['id']) {
                $node = $nodeModel::getNodeById($data['id']);
                if (empty($node))
                    $this->ajaxError('错误的id');
                $name = '编辑';
                $res = $nodeModel->where('id', $data['id'])->update($data);
            } else {
                $res = $nodeModel->insertGetId($data);
            }
            if (!$res)
                $this->ajaxError($nodeModel->getError() ?? $name . '失败');
            $pid = isset($data['pid']) && is_numeric($data['pid']) ? $data['pid'] : 0;
            $data['auth'] = $data['auth'] ?? 0;
            if ($data['id']) {
                if ($data['auth'] != $node['auth']) {
                    cache('node_pid_' . $pid . '_auth_' . $data['auth'], null);
                    cache('node_pid_' . $pid . '_auth_' . $node['auth'], null);
                }
                cache('node_url_' . $node['url'], null);
                cache('node_id_' . $data['id'], null);
            } else {
                cache('node_pid_' . $pid . '_auth_', null);
                cache('node_pid_' . $pid . '_auth_' . $data['auth'], null);
            }
            $this->ajaxSuccess($name . '成功', url('node/index'));
        }
    }


    /**
     * 操作节点，严格要求进入的节点的权限
     * @param $type
     * @param $data
     * @param $father
     * @author szh <sali_hub@163.com>
     */
    private function checkRightNode($type, $data, $father = [])
    {
        switch ($type) {
            case 'addAct':
                if ($data['type'] != 3 || !isset($father['type']) || $father['type'] !== 1)
                    $this->ajaxSuccess('越权操作');
                break;
            case 'addBtn':
                if ($data['type'] != 2 || !isset($father['type']) || $father['type'] !== 1)
                    $this->ajaxSuccess('越权操作');
                break;
            case 'addNav':
                if ($data['type'] != 1 || !isset($father['type']) || $father['type'] !== 0)
                    $this->ajaxSuccess('越权操作');
                break;
            case 'add':
                if ($data['type'] != 0)
                    $this->ajaxSuccess('越权操作');
                break;
            default:
                if ($type !== 'edit')
                    $this->ajaxSuccess('越权操作');
                break;
        }
    }

    /**
     * 删除节点
     * @author szh <sali_hub@163.com>
     */
    public function delete()
    {
        $id = input('get.id/d', 0);
        $node = NodeModel::getNodeById($id);
        if (empty($node))
            $this->ajaxError('错误的id');

        $res = NodeModel::where('id', $id)->delete();
        if (!$res)
            $this->ajaxError('删除失败');
        cache('node_pid_' . $node['pid'] . '_auth_', null);
        cache('node_pid_' . $node['pid'] . '_auth_' . $node['auth'], null);
        cache('node_url_' . $node['url'], null);
        cache('node_id_' . $id, null);
        $this->ajaxSuccess('删除成功');
    }
}