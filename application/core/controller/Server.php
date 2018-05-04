<?php
/**
 * Created by PhpStorm.
 * User: szh
 * Date: 2018/5/4
 * Time: 13:59
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
     * @author szh
     */
    public function tasksServer(){
        $_this = $this;
        $server = new \swoole_server("127.0.0.1", 9502);
        $server->set(array('task_worker_num' => 4));

        //接受异步任务
        $server->on('receive', function($server, $fd, $reactor_id, $data) {
            $task_id = $server->task($data);
            _log("Dispath AsyncTask: [id=$task_id] - {$data}");
            echo "Dispath AsyncTask: [id=$task_id]" . PHP_EOL;
        });

        //处理异步任务
        $server->on('task', function ($server, $task_id, $reactor_id, $string) use ($_this) {
            $data = json_decode($string, true);
            if (!isset($data['type']) || empty($data['type'])){
                _log("无效的任务 - {$string}", 1);
                die("无效的任务 - {$string}" . PHP_EOL);
            }
            //匹配任务
            switch($data['type']){
                case 'email':
                    $email = $data['data'] ?? '';
                    $_this->sendEmail($email);
                    break;
            }
            $server->finish("$string -> OK");
        });

        //异步任务结果处理
        $server->on('finish', function ($server, $task_id, $data) {
            _log("AsyncTask[$task_id] finished: {$data}");
            echo "AsyncTask[$task_id] finished: {$data} " . PHP_EOL;
        });

        $server->start();
    }

    /**
     * 发送邮件
     * @param $email
     * @author szh
     */
    private function sendEmail($email){
        swoole_timer_after(10000, function () use ($email) {
            //send_email($email);
        });
    }
}