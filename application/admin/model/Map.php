<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/4/23
 * Time: 13:55
 */
namespace app\admin\model;

use think\Model;

class Map extends Model {

    protected $name = 'admin_map';


    /**
     * 获取用户可访问的所有节点
     * @param $role_id
     * @param $uid
     * @return array
     * @author szh
     */
    public static function getAllowNode($role_id, $uid) : array {
        if($role_id === 1)
            return Node::getChildList();

        //权限组节点
        $roleNode = self::getRoleNode($role_id);
        //用户自定义节点
        $userNode = self::getUserNode($uid);
        //公共节点
        $public = Node::getChildList(0, 1);

        return array_merge($roleNode, $userNode, $public);
    }

    /**
     * 获取权限组节点
     * @param $role_id
     * @return array
     * @author szh
     */
    public static function getRoleNode($role_id) : array {
        $tag = 'allow_node_by_role_id_' . $role_id;
        $node = cache($tag);
        if($node === false){
            $node = self::where('role_id', $role_id)->where('type', 'role')->column('node_id');
            cache($tag, $node);
        }
        return $node ? $node : [];
    }

    /**
     * 获取用户自定义节点
     * @param $uid
     * @return array
     * @author szh
     */
    public static function getUserNode($uid) : array {
        $tag = 'user_node_by_user_id_' . $uid;
        $node = cache($tag);
        if($node === false){
            $node = self::where('role_id', $uid)->where('type', 'user')->column('node_id');
            cache($tag, $node);
        }
        return $node ? $node : [];
    }

}