<?php
use Swoole\Http\Server;
use Swoole\Process;


$http = new Server('127.0.0.1', 5901);
//指定资源根目录
$http->set([
    'enable_static_handler'=>true,
    'document_root'=>"/home/www/swoole/html",
    'worker_num'=>1,
    'enable_coroutine'=>false,
]);
$http->on('WorkerStart',function($serv,$worker_id) {
    include_once dirname(__DIR__).DIRECTORY_SEPARATOR."php".DIRECTORY_SEPARATOR."load.php";
});
$http->on('Request', function ($request, $response) use($http) {

    $response->header('Content-Type', 'text/html; charset=utf-8');
    $data = $request->post['links'];
    if(!is_array($data)){
        $response->status(400);
        $res = [
            "msg"=>"参数错误",
            "data"=>null,
        ];
        $response->end(json_encode($res));
        return;
    }

    $data = array_chunk($data,ceil(count($data)/6));

    // $links = [
    //     'https://www.amazon.co.uk/dp/B08DK1NDSQ',
    //     'https://www.amazon.co.uk/dp/B08C53F45F',
    //     'https://www.amazon.co.uk/dp/B08JTNJWWL',
    //     'https://www.amazon.co.uk/dp/B08YDDB5QR',
    //     'https://www.amazon.co.uk/dp/B08DK1NDSQ',
    //     'https://www.amazon.co.uk/dp/B08YDDB5QR',
    //     'https://www.amazon.co.uk/dp/B08YDDB5QR',
    //     'https://www.amazon.co.uk/dp/B08DK1NDSQ',
    //     'https://www.amazon.co.uk/dp/B08YDDB5QR',
    // ];
    
    $processes = [];
    $referer = "http://amazon.co.uk";
    $crawler = new Crawler($referer);
    $selector = new SelectorAmazon();
    foreach($data as $links){
        $process = new Process(function ($work) use($links,$processes,$crawler,$selector)
        {  
            foreach($links as $link){
               
                $html = $crawler->doRequest($link);
                print_r($html);
                // $imgs = $selector->parseMainImages($html);
                // $data = $selector->parseHtml($html);
            }  
        },false);
        $pid = $process->start();
        $processes[$pid] = $process;
    }
    Process::wait();
    
    $res = [
        "msg"=>"",
        "data"=>true,
    ];
    $response->end(json_encode($res));
});




$http->start();
