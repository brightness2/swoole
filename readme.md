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