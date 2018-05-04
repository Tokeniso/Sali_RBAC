<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/5/3
 * Time: 13:59
 */
namespace app\core\model;

use think\Model;

class Picture extends Model
{

    /**
     * 通过md5，查找文件
     * @param $md5
     * @return array|mixed|null|\PDOStatement|string|Model
     * @author szh
     */
    public function getFileByMd5($md5)
    {
        $tag = 'file_md5_' . $md5;
        $file = cache($tag);
        if ($file === false) {
            $file = $this->where('md5', $md5)->field(true)->find();
            cache($tag, $file);
        }
        return $file;
    }

    /**
     * 通过md5，查找文件
     * @param $id
     * @return array|mixed|null|\PDOStatement|string|Model
     * @author szh
     */
    public function getFileById($id)
    {
        $tag = 'file_id_' . $id;
        $file = cache($tag);
        if ($file === false) {
            $file = $this->where('id', $id)->field(true)->find();
            cache($tag, $file);
        }
        return $file;
    }

}