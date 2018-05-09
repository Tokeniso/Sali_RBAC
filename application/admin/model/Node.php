<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\admin\model;

use think\Model;

class Node extends Model
{

    protected $name = 'admin_node';


    /**
     * 获取某一父类下的所有子节点的数据列表
     * @param int|array $pid 父类id
     * @param null $auth 【null】全部节点 【1】公开的节点 【0】依赖权限组的节点
     * @param bool $data 【false】只需要节点的id 【true】需要节点的所有数据
     * @param int $deep 子类树状深度 【0】当前子类为顶级分类
     * @param string $tree 需要树状结构的数据  【$tree】父类下子类的字段名称
     * @return array
     * @author szh
     */
    public static function getChildList($pid = 0, $auth = null, $data = false, $deep = 0, $tree = '')
    {
        //获取当前条件的所有列表
        $needChildren = true;
        // array 只取当前分类下的子分类
        if (is_array($pid)) {
            if (isset($pid[0]) && is_numeric($pid[0])) {
                $needChildren = $pid[1] ?? true;
                $pid = $pid[0];
            } else {
                $pid = 0;
            }
        }
        $tag = 'node_pid_' . $pid . '_auth_' . $auth;
        $list = cache($tag);
        if ($list === false) {
            $map[] = ['pid', 'eq', $pid];
            //全部节点、公开的节点、依赖权限组的节点
            if (in_array($auth, [0, 1], true))
                $map[] = ['auth', 'eq', $auth];
            $list = self::where($map)->order('sort desc')->column('id');
            cache($tag, $list);
        }
        $lists = [];
        if ($list) {
            //递归查找子节点
            foreach ($list as $key => $id) {
                //【false】只需要节点的id 【true】需要节点的所有数据
                if ($data) {
                    //获取所有数据
                    $list[$key] = self::getNodeById($id);
                    $pre = '';
                    if ($deep > 0) {
                        for ($i = 0; $i < $deep; $i++)
                            $pre .= '　　';
                        $pre .= '|——';
                    }
                    //拼接树状结构的前缀
                    $list[$key]['pre'] = $pre;
                }
                //推进返回结果集中
                $lists[$id] = $list[$key];
                if ($needChildren) {
                    //查找所有子类
                    $child = self::getChildList($id, $auth, $data, $deep + 1, $tree);
                    //推进返回结果集中
                    if ($child) {
                        //需要树状结构的数据
                        if ($tree)
                            $lists[$id][$tree] = $child;
                        else
                            $lists = array_merge($lists, $child);
                    }
                }
            }
        }
        //返回结果集
        return $lists;
    }

    /**
     * 通过url查找节点信息
     * @param $url
     * @return array|mixed|null|\PDOStatement|string|Model
     * @author szh
     */
    public static function getNodeByUrl($url)
    {
        $tag = 'node_url_' . $url;
        $node = cache($tag);
        if ($node === false) {
            $node = self::field(true)->where('url', $url)->find();
            cache($tag, $node);
        }
        return $node ? $node : [];
    }

    /**
     * 通过url查找节点信息
     * @param $id
     * @return array|mixed|null|\PDOStatement|string|Model
     * @author szh
     */
    public static function getNodeById($id)
    {
        $tag = 'node_id_' . $id;
        $node = cache($tag);
        if ($node === false) {
            $node = self::field(true)->where('id', $id)->find();
            cache($tag, $node);
        }
        return $node ? $node : [];
    }

    /**
     * 循环节点id，获取节点信息
     * @param array $ids
     * @return array
     * @author szh
     */
    public static function idsToNode(array $ids)
    {
        $nodes = [];
        foreach ($ids as $id) {
            $node = self::getNodeById($id);
            if ($node)
                $nodes[$id] = $nodes;
        }
        return $nodes;
    }


    /**
     * 在所有允许访问的节点中找出导航，子导航和当前访问节点所属的按钮，操作
     * @param array $ids
     * @param int $f_id 当前节点顶级父级id
     * @param int $node_id 当前节点id
     * @return array
     * @author szh
     */
    public static function showNodes(array $ids, $f_id, $node_id)
    {
        $father = [];
        $action = [];
        $button = [];
        foreach ($ids as $id) {
            $node = self::getNodeById($id);
            if ($node && $node['show']) {
                if ($node['type'] === 0) {//主导航
                    $father[$id]['self'] = $node;
                    $father[$id]['sort'] = $node['sort'];
                    if ($f_id === $id)
                        $father[$id]['show'] = true;
                } elseif ($node['type'] === 1) {//子导航
                    $father[$node['pid']]['son'][] = $node;
                } elseif ($node['pid'] === $node_id && $node['type'] === 2) {//按钮
                    $button[] = $node;
                } elseif ($node['pid'] === $node_id && $node['type'] === 3) {//操作
                    $action[] = $node;
                }
            }
        }
        //排序
        $father = node_deep_sort($father, 'son');
        $action = node_deep_sort($action);
        $button = node_deep_sort($button);
        return [$father, $action, $button];
    }


    /**
     * 查找最顶级导航id
     * @param $id
     * @return int|mixed
     * @author szh
     */
    public static function topNodeId($id)
    {
        $node = self::getNodeById($id);
        if ($node) {
            if ($node['pid'] !== 0)
                return self::topNodeId($node['pid']);
            else
                return $node['id'];
        }
        return 0;
    }

    /**
     * 节点列表生成树状名称
     * @param $list
     * @param  array $checked
     * @return array
     * @author szh
     */
    public static function listToTree($list, $checked)
    {
        if ($list && is_array($list)) {
            foreach ($list as $key => $value) {
                $list[$key]['value'] = $value['id'];
                if ($checked && is_array($checked))
                    $list[$key]['checked'] = in_array($value['id'], $checked);
                if (isset($list[$key]['list']))
                    $list[$key]['list'] = self::listToTree($list[$key]['list'], $checked);
            }
        }
        return $list;
    }

    /**
     * 获取当前节点的父级导航路径
     * @param $id
     * @return array
     * @author szh
     */
    public static function getNodeRoad($id)
    {
        $road = [];
        $node = self::getNodeById($id);
        if (!empty($node)) {
            if ($node['pid'] === 0) {
                $node['url_show'] = false;
                $road[] = $node;
            } else {
                $node['url_show'] = true;
                $father = self::getNodeById($node['pid']);
                if (empty($father)) {
                    $node['url_show'] = false;
                    $road[] = $node;
                } else {
                    $old = self::getNodeRoad($father['pid']);
                    if ($old) {
                        $road[] = $old;
                    } else {
                        $father['url_show'] = false;
                        $road[] = $father;
                    }
                    $road[] = $node;
                }
            }
        }
        return $road;
    }

}