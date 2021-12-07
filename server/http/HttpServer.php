<?php
class HttpServer{

    
    protected $http;
    
    public function __construct($host,$port,$settings=[])
    {
        $this->http = new Swoole\Http\Server($host, $port);
        $this->http->set($settings);
        $this->http->on('Request', [$this,'onRequest']);
    }

    public function onRequest($request, $response)
    {
        print_r($request->get);
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->end('<h1>Hello Brightness. #' . rand(1000, 9999) . '</h1>');
    }

    public function start()
    {
        $this->http->start();
    }
}