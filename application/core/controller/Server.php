<?php
/**
 * Created by PhpStorm.
 * User: szh
 */
namespace app\core\controller;

use app\common\controller\Base;

class Server extends Base {

    public function __construct()
    {
        parent::__construct();

        if(!request()->isCli()){
            echo '必须运行在cli中。';
            die;
        }
    }


    /**
     * 异步处理程序
     * @author szh <sali_hub@163.com>
     */
    public function tasksServer(){
        $_this = $this;
        $server = new \swoole_server("127.0.0.1", 9502);
        $server->set(array('task_worker_num' => 4));

        //接受异步任务
        $server->on('receive', function($server, $fd, $reactor_id, $data) use ($_this) {
            $task_id = $server->task($data);
            $_this->log("Dispath AsyncTask: [id=$task_id] - {$data}");
            echo "Dispath AsyncTask: [id=$task_id]" . PHP_EOL;
        });

        //处理异步任务
        $server->on('task', function ($server, $task_id, $reactor_id, $string) use ($_this) {
            $data = json_decode($string, true);
            if (!isset($data['type']) || empty($data['type'])){
                $_this->log([
                    'message' => "无效的任务 - {$string}",
                    'type' => 1]);
                die("无效的任务 - {$string}" . PHP_EOL);
            }
            //匹配任务
            switch($data['type']){
                case 'email':
                    $email = $data['data'] ?? '';
                    $_this->sendEmail($email);
                    break;
                case 'log':
                    $this->log($data['data']);
                    break;
            }
            $server->finish("$string -> OK");
        });

        //异步任务结果处理
        $server->on('finish', function ($server, $task_id, $data) use ($_this) {
            $_this->log("AsyncTask[$task_id] finished: {$data}");
            echo "AsyncTask[$task_id] finished: {$data} " . PHP_EOL;
        });

        $server->start();
    }

    /**
     * 发送邮件
     * @param $email
     * @author szh <sali_hub@163.com>
     */
    private function sendEmail($email){
        swoole_timer_after(10000, function () use ($email) {
            //send_email($email);
        });
    }

    /**
     * 异步记录日志
     * @param $data
     * @author szh <sali_hub@163.com>
     */
    private function log($data){
        if(is_array($data)){
            $type = $data['type'] ?? 0;
            $message = $data['message'] ?? '';
        }else{
            $type = 0;
            $message = $data;
        }

        $msg = [
            'info',//记录信息
            'error',//异常记录
            'waring',//错误信息
        ];
        $head = $msg[$type] ?? 'info';
        if (!empty($message)) {
            $message = "[ $head ] [ " . date('Y-m-d H-i-s') . " ]" . $message . PHP_EOL;
            error_log($message, 3, '../runtime/log/running.log');
        }
    }
}