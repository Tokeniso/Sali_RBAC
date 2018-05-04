<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/5/2
 * Time: 17:34
 */
namespace app\core\controller;

use app\common\controller\Base;

class Picture extends Base
{

    /**
     * 上传本地文件
     * @author szh
     */
    public function uploadPicture()
    {
        $file = request()->file('picture');

        if (empty($file))
            $this->ajaxError('请上传文件');
        if (is_array($file))
            $file = array_shift($file);
        $md5 = $file->hash('md5');
        //检测图片是否存在
        $data = get_file_by_md5($md5);

        if ($data === false) {
            $config = config('upload_config');
            $driver = $config['driver'] ?? 'local';
            $method = $driver . 'Upload';
            if (!method_exists($this, $method))
                $this->ajaxError('错误的上传驱动');
            $fileName = $this->fileName($file->getInfo('name'));
            //上传驱动需要返回图片的地址数组，path或者url
            $upload = $this->$method($file, $md5, $fileName);

            if (empty($upload))
                $this->ajaxError('上传失败');
            //记录新图片
            $data = array_merge([
                'md5' => $md5,
                'name' => $fileName,
                'driver' => $driver,
                'created_at' => time(),
            ], $upload);

            $pic = db('picture')->insertGetId($data);
            if (empty($pic))
                $this->ajaxError('存储信息失败');

        }
        $result = [
            'id' => $data['id'] ?? $pic,
            'path' => isset($data['path']) && !empty($data['path']) ? $data['path'] : $data['url'],
        ];
        $this->ajaxSuccess('上传成功', $result);
    }

    /**
     * 处理图片原始名称
     * @param $name
     * @return string
     * @author szh
     */
    private function fileName($name)
    {
        $name = text($name);
        $code = 'utf-8';
        if (mb_strlen($name, $code) > 128)
            $name = mb_substr($name, -1, 128, $code);
        return $name;
    }

    /**
     * 本地上传图片文件
     * @param $file
     * @return array
     * @author szh
     */
    private function localUpload($file)
    {
        $path = './uploads/images';
        $res = $file->validate(['size' => 10240, 'ext' => 'jpg,jpeg,bmp,png,gif'])->move($path);
        if ($res === false)
            $this->ajaxError($file->getError());
        $result = [
            'path' => trim($path, '.') . '/' . str_replace('\\', '/', $res->getSaveName()),
        ];
        return $result;
    }
}