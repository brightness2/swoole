<?php
//创建客户端对象
$client = new swoole_client(SWOOLE_SOCK_TCP);
//连接服务端
if(!$client->connect("127.0.0.1",9501)){
    echo "连接失败";
    exit;
}
//接收客户端输入的数据
fwrite(STDOUT,"请输入消息");
$msg = trim(fgets(STDIN));
//发送数据到服务端
$client->send($msg);

//接收服务端的数据
$result = $client->recv();
echo $result;
