<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\ucenter\model;

use think\Model;

class Users extends Model
{

    /**
     * 通过id查找用户
     * @param int $id
     * @return array|bool|mixed|null|\PDOStatement|string|Model
     * @author szh
     */
    public function getUserById(int $id)
    {
        if (empty($id))
            return false;
        $tag = 'user_detail_id_' . $id;
        $user = cache($tag);
        if ($user === false) {
            $user = $this->where('status > -1')->field(true)->find($id);
            if ($user) {
                $user['avatar'] = is_numeric($user['avatar']) ? get_pic_url($user['avatar']) : $user['avatar'];
            }
            cache($tag, $user);
        }
        return $user ? $user : false;
    }

    /**
     * 通过手机号码获取用户id
     * @param $phone
     * @return array|bool
     * @author szh
     */
    public static function getUserByPhone($phone)
    {
        $tag = 'user_detail_id_' . $phone;
        $uid = cache($tag);
        if ($uid === false) {
            $uid = self::where('phone', $phone)->where('status > -1')->value('id');
            cache($tag, $uid);
        }
        return $uid ? $uid : false;
    }

    /**
     * 更新用户信息
     * @param $data
     * @param $uid
     * @return bool|static
     * @author szh
     */
    public static function updateUser($data, $uid)
    {
        if (empty($data))
            return false;
        $res = self::where('id', $uid)->update($data);
        if ($res)
            cache('user_detail_id_' . $uid, null);
        return $res;
    }
}