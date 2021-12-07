<?php
use Swoole\Process;
echo "开始时间:".date("Ymd H:i:s").PHP_EOL;
$urls = [
    // 'http://www.baidu.com',
    'https://www.amazon.co.uk/dp/B08DK1NDSQ',
    // 'https://www.amazon.co.uk/dp/B08DK1NDSQ',
    // 'https://www.amazon.co.uk/dp/B08DK1NDSQ',
    // 'https://www.amazon.co.uk/dp/B08DK1NDSQ',
    // 'https://www.amazon.co.uk/dp/B08DK1NDSQ',

];
$processes = [];
foreach($urls as $url){
    $process = new Process(function($worker) use($url){
      $res =  curlData($url);
      $worker->write($res);
    },true);
    $pid = $process->start();
    $processes[$pid] = $process;
}

foreach($processes as $process){
    echo  $process->read();
}

function curlData($url)
{
    $html = file_get_contents($url);
    print_r($html);
    return $url."--success".PHP_EOL;
}
Process::wait();
echo "结束时间:".date("Ymd H:i:s").PHP_EOL;
