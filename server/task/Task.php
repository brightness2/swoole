<?php
require_once "../http/HttpServer.php";
class Task extends HttpServer{
    
    public function __construct($host,$port,$settings=[])
    {
        $taskSetting = ['task_worker_num' => 4];//必须设置
        $settings = array_merge($taskSetting,$settings);
        parent::__construct($host,$port,$settings);
        $this->http->on('Task', [$this,'onTask']);
        $this->http->on('Finish', [$this,'onFinish']);
    }

    public function onRequest($request, $response)
    {
        $data = [
            'userId'=>1,
            'name'=>'Brightness',
        ];
        $this->http->task($data);
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->end('<h1>Hello Brightness. #' . rand(1000, 9999) . '</h1>');
    }

    function onTask ($serv, $task_id, $reactor_id, $data) {
        echo "执行task [id={$task_id}] ".PHP_EOL;
        //场景5s
        sleep(5);
        //返回任务执行的结果
        $this->http->finish("{$data['name']}-> OK");
      
    }

    function onFinish($serv, $task_id, $data) {
        echo "task [{$task_id}] 完成: {$data}".PHP_EOL;
    }

}

$server = new Task('127.0.0.1',5901);
$server->start();