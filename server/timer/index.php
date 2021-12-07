<?php
Swoole\Timer::tick(2000,function ($timerId)
{
    echo "2s timer id:{$timerId}\n";
});