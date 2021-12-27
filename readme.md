# contos7 php7 swoole 使用
## 关闭 SELINUX
vi /etc/sysconfig/selinux
然后将配置SELinux=enforcing改为SELinux=disabled，如下图所示。
```
SELINUX=disabled
```
需要重新启动系统，然后使用sestatus命令检查SELinux的状态
sestatus
## contos7 常用工具
yum -y install openssl
yum -y install openssl-devel
yum -y install curl
yum -y install curl-devel
yum -y install libjpeg
yum -y install libjpeg-devel
yum -y install libpng
yum -y install libpng-devel
yum -y install freetype
yum -y install freetype-devel
yum -y install pcre
yum -y install pcre-devel
yum -y install libxslt
yum -y install libxslt-devel
yum -y install bzip2
yum -y install bzip2-devel
## 源码安装php7
### https://www.php.net/downloads 下载php7 源码
### 操作安装php
    1、解压php压缩包 
     tar -xjvf php-7.4.26.tar.bz2 

    2、确保安装 gcc，libxml2，没有安装，
        执行安装gcc
        yum -y install gcc gcc-c++ kernel-devel
        执行安装
        yum -y install libxml2
        yum -y install libxml2-devel
        yum install sqlite-devel
        yum -y install autoconf
   3、编译
        执行shell 脚本,并指定安装路径，进行自动配置
         ./configure --prefix=/home/work/soft/php7

        //编译
        make
        //安装
        make install
    4、创建test.php 文件
    ```php
        <?php

        echo 'hello world!';
    ```
    执行 ./home/work/soft/php7/bin/php test.php

    5、设置别名，可以全局使用php
    vi ~/.bash_profile 
    增加一下配置
    ```
    alias php=/home/work/soft/php7/bin/php
    ```
    激活配置
    source ~/.bash_profile 
    执行php命令
    php -v

    6、查看php.ini 文件的默认路径
   php --ini
    把源码中的php.ini-development 复制到 /home/work/soft/php7/bin/php/lib/php.ini 文件夹

## 安装swoole4
###  https://github.com/swoole/swoole-src/releases 下载源码 tar.gzb 并解压

### 编译swoole安装
   1、 进入swoole文件夹
    执行命令生成配置文件
    /home/work/soft/php7/bin/phpize 

    2、执行configure
    ./configure --with-php-config=/home/work/soft/php7/bin/php-config

    3、编译
    make
    4、安装
    make install

### 配置php.ini 增加扩展
vi /home/work/soft/php7/lib/php.ini
增加配置
extension=swoole
查看扩展是否配置完成
php -m 

# swoole 使用

## 基本服务

### 常见服务
    1、tcp
    2、udp
    3、http
    4、webscoket
    等等
### 例子代码
    swoole/server

### 执行耗时的操作
    注意需要设置 
   ```php
    $serv->set([
    'task_worker_num' => 4
]);
```
    1、批量发邮件
    2、广播
    等等
### 定时器

# 异步扩展

## 4.3版本开始需要扩展异步功能
4.3的版本移除所有异步模块, 分离异步扩展到 async-ext 移除的异步模块如下:

从4.3版本开始需要额外安装swoole-async扩展才能使用异步模块

### 如何才能解决这个问题呢？
使用协程替换，或者安装async-ext扩展https://github.com/swoole/ext-async

## 其它
1、查看进程
 	netstat -apn | grep 端口号

2、停止进程
kill 进程号

## supervisor 管理 swoole

supervisor安装
```
yum install supervisor
```
supervisor配置文件：/etc/supervisord.conf
注：supervisor的配置文件默认是不全的，不过在大部分默认的情况下，上面说的基本功能已经满足。
supervisor.conf配置文件说明：
```
[unix_http_server]
file=/tmp/supervisor.sock   ;UNIX socket 文件，supervisorctl 会使用
;chmod=0700                 ;socket文件的mode，默认是0700
;chown=nobody:nogroup       ;socket文件的owner，格式：uid:gid
 
;[inet_http_server]         ;HTTP服务器，提供web管理界面
;port=127.0.0.1:9001        ;Web管理后台运行的IP和端口，如果开放到公网，需要注意安全性
;username=user              ;登录管理后台的用户名
;password=123               ;登录管理后台的密码
 
[supervisord]
logfile=/tmp/supervisord.log ;日志文件，默认是 $CWD/supervisord.log
logfile_maxbytes=50MB        ;日志文件大小，超出会rotate，默认 50MB，如果设成0，表示不限制大小
logfile_backups=10           ;日志文件保留备份数量默认10，设为0表示不备份
loglevel=info                ;日志级别，默认info，其它: debug,warn,trace
pidfile=/tmp/supervisord.pid ;pid 文件
nodaemon=false               ;是否在前台启动，默认是false，即以 daemon 的方式启动
minfds=1024                  ;可以打开的文件描述符的最小值，默认 1024
minprocs=200                 ;可以打开的进程数的最小值，默认 200
 
[supervisorctl]
serverurl=unix:///tmp/supervisor.sock ;通过UNIX socket连接supervisord，路径与unix_http_server部分的file一致
;serverurl=http://127.0.0.1:9001 ; 通过HTTP的方式连接supervisord
 
; [program:xx]是被管理的进程配置参数，xx是进程的名称
[program:xx]
command=/opt/apache-tomcat-8.0.35/bin/catalina.sh run  ; 程序启动命令
autostart=true       ; 在supervisord启动的时候也自动启动
startsecs=10         ; 启动10秒后没有异常退出，就表示进程正常启动了，默认为1秒
autorestart=true     ; 程序退出后自动重启,可选值：[unexpected,true,false]，默认为unexpected，表示进程意外杀死后才重启
startretries=3       ; 启动失败自动重试次数，默认是3
user=tomcat          ; 用哪个用户启动进程，默认是root
priority=999         ; 进程启动优先级，默认999，值小的优先启动
redirect_stderr=true ; 把stderr重定向到stdout，默认false
stdout_logfile_maxbytes=20MB  ; stdout 日志文件大小，默认50MB
stdout_logfile_backups = 20   ; stdout 日志文件备份数，默认是10
; stdout 日志文件，需要注意当指定目录不存在时无法正常启动，所以需要手动创建目录（supervisord 会自动创建日志文件）
stdout_logfile=/opt/apache-tomcat-8.0.35/logs/catalina.out
stopasgroup=false     ;默认为false,进程被杀死时，是否向这个进程组发送stop信号，包括子进程
killasgroup=false     ;默认为false，向进程组发送kill信号，包括子进程
 
;包含其它配置文件
[include]
files = relative/directory/*.ini    ;可以指定一个或多个以.ini结束的配置文件

```
子进程配置文件路径：/etc/supervisord.d/
注：默认子进程配置文件为ini格式，可在supervisor主配置文件中修改。

子进程配置文件说明：

给需要管理的子进程(程序)编写一个配置文件，放在/etc/supervisor.d/目录下，以.ini作为扩展名（每个进程的配置文件都可以单独分拆也可以把相关的脚本放一起）。如任意定义一个和脚本相关的项目名称的选项组（/etc/supervisord.d/test.conf）：
```
#项目名
[program:blog]
#脚本目录
directory=/opt/bin
#脚本执行命令
command=/usr/bin/python /opt/bin/test.py

#supervisor启动的时候是否随着同时启动，默认True
autostart=true
#当程序exit的时候，这个program不会自动重启,默认unexpected，设置子进程挂掉后自动重启的情况，有三个选项，false,unexpected和true。如果为false的时候，无论什么情况下，都不会被重新启动，如果为unexpected，只有当进程的退出码不在下面的exitcodes里面定义的
autorestart=false
#这个选项是子进程启动多少秒之后，此时状态如果是running，则我们认为启动成功了。默认值为1
startsecs=1

#脚本运行的用户身份 
user = test

#日志输出 
stderr_logfile=/tmp/blog_stderr.log 
stdout_logfile=/tmp/blog_stdout.log 
#把stderr重定向到stdout，默认 false
redirect_stderr = true
#stdout日志文件大小，默认 50MB
stdout_logfile_maxbytes = 20MB
#stdout日志文件备份数
stdout_logfile_backups = 20

```

示例：/etc/supervisord.d/thinkswoole.ini
```
#说明同上
[program:thinkswoole]
command= php /home/www/tp/think swoole ;the program (relative uses PATH, can take args)
autorestart=true ;自动启动
startretries=3 ;启动失败时的最多重试次数
stderr_logfile=/home/www/tp/runtime/log/supervisor/blog_stderr.log 
stdout_logfile=/home/www/tp/runtime/log/supervisor/blog_stdout.log 
redirect_stderr = true
stdout_logfile_backups = 20
#user = root  

```
进入supervisorctl 控制台
```
supervisorctl
#更新配置
supervisor> update
thinkswoole: added process group
#查看状态
supervisor> status
thinkswoole                      RUNNING   pid 66193, uptime 0:00:08
#重新启动配置中的所有程序
supervisor> reload
Really restart the remote supervisord process y/N? y
Restarted supervisord
#停止
supervisor> stop thinkswoole
thinkswoole: stopped
#退出
supervisor> exit
```

supervisor命令说明
```
常用命令
supervisorctl status        //查看所有进程的状态
supervisorctl stop es       //停止es
supervisorctl start es      //启动es
supervisorctl restart       //重启es
supervisorctl update        //配置文件修改后使用该命令加载新的配置
supervisorctl reload        //重新启动配置中的所有程序

注：把es换成all可以管理配置中的所有进程。直接输入supervisorctl进入supervisorctl的shell交互界面，此时上面的命令不带supervisorctl可直接使用。

注意事项

使用supervisor进程管理命令之前先启动supervisord，否则程序报错。
使用命令supervisord -c /etc/supervisord.conf启动。
若是centos7：

systemctl start supervisord.service     //启动supervisor并加载默认配置文件
systemctl enable supervisord.service    //将supervisor加入开机启动项

```
常见问题
```
1、  unix:///var/run/supervisor.sock no such file
问题描述：安装好supervisor没有开启服务直接使用supervisorctl报的错
解决办法：supervisord -c /etc/supervisord.conf

2、  command中指定的进程已经起来，但supervisor还不断重启
问题描述：command中启动方式为后台启动，导致识别不到pid，然后不断重启，这里使用的是elasticsearch，command指定的是$path/bin/elasticsearch -d
解决办法：supervisor无法检测后台启动进程的pid，而supervisor本身就是后台启动守护进程，因此不用担心这个

3、启动了多个supervisord服务，导致无法正常关闭服务
问题描述：在运行supervisord -c /etc/supervisord.conf之前，直接运行过supervisord -c /etc/supervisord.d/xx.conf导致有些进程被多个superviord管理，无法正常关闭进程。
解决办法：使用ps -fe | grep supervisord查看所有启动过的supervisord服务，kill相关的进程。

4、官网 http://supervisord.org
```

## swoole 热重载

