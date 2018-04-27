<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/4/23
 * Time: 13:34
 */
namespace app\admin\model;

use think\Model;

class Node extends Model {

    protected $name = 'admin_node';

    /**
     * 获取公共、依赖授权节点
     * @param int $auth  1为公开节点，0依赖权限节点
     * @return array
     * @author szh
     */
    public static function getAuthNode($auth=1) : array {
        $tag = 'public_node_list_' . $auth;
        $node = cache($tag);
        if($node === false){
            $node = self::where('auth', $auth)->column('id');
            cache($tag, $node);
        }
        return $node ? $node : [];
    }

    /**
     * 通过url查找节点信息
     * @param $url
     * @return array|mixed|null|\PDOStatement|string|Model
     * @author szh
     */
    public static function getNodeByUrl($url){
        $tag = 'node_url_' . $url;
        $node = cache($tag);
        if($node === false){
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
    public static function getNodeById($id){
        $tag = 'node_id_' . $id;
        $node = cache($tag);
        if($node === false){
            $node = self::field(true)->where('id', $id)->find();
            cache($tag, $node);
        }
        return $node ? $node : [];
    }

    /**
     * 获取所有列表节点
     * @return array|mixed|\PDOStatement|string|\think\Collection
     * @author szh
     */
    public static function getNodeList() {
        $list = self::field(true)->order('sort desc')->select();
        return $list ? $list : [];
    }

    /**
     * 循环节点id，获取节点信息
     * @param array $ids
     * @return array
     * @author szh
     */
    public static function idsToNode(array $ids){
        $nodes = [];
        foreach ($ids as $id){
            $node = self::getNodeById($id);
            if($node)
                $nodes[$id] = $nodes;
        }
        return $nodes;
    }


    /**
     * 在所有允许访问的节点中找出导航，子导航和当前访问节点所属的按钮，操作
     * @param array $ids
     * @param int $f_id  当前节点顶级父级id
     * @param int $node_id  当前节点id
     * @return array
     * @author szh
     */
    public static function showNodes(array $ids, $f_id, $node_id){
        $father = [];
        $action = [];
        $button = [];
        foreach ($ids as $id){
            $node = self::getNodeById($id);
            if($node && $node['show']){
                if($node['type'] === 0){//主导航
                    $father[$id]['self'] = $node;
                    $father[$id]['sort'] = $node['sort'];
                    if($f_id === $id)
                        $father[$id]['show'] = true;
                }elseif($node['type'] === 1){//子导航
                    $father[$node['pid']]['son'][] = $node;
                }elseif($node['pid'] === $node_id && $node['type'] === 2){//按钮
                    $button[] = $node;
                }elseif($node['pid'] === $node_id && $node['type'] === 3){//操作
                    $action[] = $node;
                }
            }
        }
        //排序
        node_deep_sort($father, 'son');
        node_deep_sort($action);
        node_deep_sort($button);
        return [$father, $action, $button];
    }

    /**
     * 获取所有节点
     * @return array|mixed|\PDOStatement|string|\think\Collection
     * @author szh
     */
    public static function getAllNode(){
        $tag = 'node_all_id';
        $nodes = cache($tag);
        if($nodes === false){
            $nodes = self::field(true)->column('id');
            cache($tag, $nodes);
        }
        return $nodes ? $nodes : [];
    }

    /**
     * 查找最顶级导航id
     * @param $id
     * @return int|mixed
     * @author szh
     */
    public static function topNodeId($id){
        $node = self::getNodeById($id);
        if($node){
            if($node['pid'] !== 0)
                return self::topNodeId($node['pid']);
            else
                return $node['id'];
        }
        return 0;
    }

    /**
     * 节点列表生成树状名称
     * @param $list
     * @param int $pid
     * @param bool $checked
     * @return array
     * @author szh
     */
    public static function listToTree($list, $pid = 0, $checked = false) {
        $data = [];
        foreach ($list as $key => $value) {
            if($value['pid'] === $pid){
                $child = self::listToTree($list, $value['id'], $checked);
                if($child)
                    $value['list'] = $child;
                if(is_array($checked)){
                    $value['value'] = $value['id'];
                    $value['checked'] = in_array($value['id'], $checked);
                }
                $data[] = $value;
            }
        }
        return $data;
    }

    /**
     * 树状图转列表
     * @param $array
     * @param int $deep
     * @return array
     * @author szh
     */
    public static function treeToList($array, $deep = 0){
        $data = [];
        foreach ($array as $key => $value){
            if($deep === 0 && $value['pid'] > 0)
                continue;

            $pre = '';
            if($deep > 0){
                for($i=0;$i<$deep;$i++)
                    $pre .='　　';
                $pre .= '|——';
            }
            $value['pre'] = $pre;

            if(isset($value['list'])){
                $child = self::treeToList($value['list'], $deep+1);
                unset($value['list']);
                $data[] = $value;
                if($child)
                    $data = array_merge($data, $child);
            }else{
                $data[] = $value;
            }
        }
        return $data;
    }


    /**
     * 获取当前节点的父级导航路径
     * @param $id
     * @return array
     * @author szh
     */
    public static function getNodeRoad($id){
        $road = [];
        $node = self::getNodeById($id);
        if(!empty($node)){
            if($node['pid'] === 0){
                $node['url_show'] = false;
                $road[] = $node;
            } else {
                $node['url_show'] = true;
                $father = self::getNodeById($node['pid']);
                if(empty($father)){
                    $node['url_show'] = false;
                    $road[] = $node;
                } else {
                    $old = self::getNodeRoad($father['pid']);
                    if($old){
                        $road[] = $old;
                    }else{
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