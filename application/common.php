<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 检测手机号码
 * @param $phone
 * @return string
 * @author szh
 */
function is_phone($phone){
    if(is_numeric($phone)){
        $result = preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $phone);
        if($result){
            return $phone;
        }
    }
    return '';
}

/**
 * 输出纯文本
 * @param $text
 * @return string
 * @author szh
 */
function text($text){
    if(is_array($text)){
        array_map('text', $text);
    }else{
        //插入换行
        $text = nl2br($text);
        //去除php、html标签
        $text = strip_tags($text);
        //去除收尾默认字符集
        $text = trim($text);
    }
    return $text;
}

/**
 * 获取用户的基本信息
 * @param $field
 * @param int $uid
 * @return array|bool
 * @author szh
 */
function query_user(string $field, $uid = 0){
    if(!$uid) return [];
    $user = model('ucenter/users')->getUserById($uid);
    if(!$user) return [];
    if($field === '*')
        return $user;
    $fields = explode(',', $field);
    $array = [];
    foreach ($fields as $value) {
        if(isset($user[$value]))
            $array[$value] = $user[$value];
        else
            $array[$value] = '';
    }
    return $array;
}

/**
 * 输出友好的时间
 * @param $sTime
 * @param string $type      vague 模糊的时间
 *                           full  完整的时间
 *                           ymd   输出‘.’间隔的 Y.m.d
 * @return false|string
 * @author szh
 */
function friendlyDate($sTime,$type = 'vague') {
    if (!$sTime)
        return '未定义';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime      =   time();
    $dTime      =   $cTime - $sTime;
    //相差几天
    $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
    //相差几年
    $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
    //vague（模糊）：n秒前，n分钟前，n小时前，n天前
    if($type=='vague'){
        if( $dTime < 60 ){
            return $dTime . '秒前';
        }elseif( $dTime < 3600 ){
            return intval($dTime/60) . '分钟前';
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600) . '小时前';
        }elseif( $dDay > 0 && $dDay <= 7 ){
            return intval($dDay) . '天前';
        }elseif( $dDay > 7 &&  $dDay <= 30 ){
            return intval($dDay/7) . '星期前';
        }elseif( $dDay > 30 && $dYear == 0){
            return intval($dDay/30) . '月前';
        }elseif( $dYear > 0 ){
            return $dYear . '年前';
        }
    }elseif($type=='full'){
        return date("Y-m-d H:i:s",$sTime);
    }elseif($type=='ymd'){
        return date("Y.m.d",$sTime);
    }
}

/**
 * 通过图片id查找oss url
 * @param $id
 * @return mixed|string
 * @author szh
 */
function get_pic_url($id){
    if(!$id) return '';
    $tag = 'picture_by_id_' . $id;
    $picture = cache($tag);
    if(!$picture){
        $picture = db('picture')->where('id',$id)->value('url');
        if($picture){
            cache($tag,$picture,3600*24);
            return $picture;
        }
    }else{
        return $picture;
    }
    return '';
}

/**
 * 节点排序
 * @param $array
 * @param string $child
 * @param string $key
 * @return mixed
 * @author szh
 */
function node_deep_sort($array, $child = '', $key = 'sort'){
    if(count($array) > 1){
        $keys = array_column($array, $key);
        if($keys)
            array_multisort($keys, SORT_DESC, SORT_NUMERIC, $array);
    }
    foreach ($array as $key => $value){
        if(isset($value[$child]))
            $array[$key][$child] = node_deep_sort($value[$child], $child, $key);
    }
    return $array;
}