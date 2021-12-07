<?php
require_once "HttpServer.php";
$settings = [
    'enable_static_handler'=>true,
    'document_root'=>"/home/www/swoole/html",
];
$server = new HttpServer('127.0.0.1',9501,$settings);
$server->start();