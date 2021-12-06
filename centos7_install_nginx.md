# centos7 安装nginx

## 依赖工具
一. gcc 安装
安装 nginx 需要先将官网下载的源码进行编译，编译依赖 gcc 环境，如果没有 gcc 环境，则需要安装：

yum install gcc-c++

二. PCRE pcre-devel 安装
PCRE(Perl Compatible Regular Expressions) 是一个Perl库，包括 perl 兼容的正则表达式库。nginx 的 http 模块使用 pcre 来解析正则表达式，所以需要在 linux 上安装 pcre 库，pcre-devel 是使用 pcre 开发的一个二次开发库。nginx也需要此库。命令：

yum install -y pcre pcre-devel

三. zlib 安装
zlib 库提供了很多种压缩和解压缩的方式， nginx 使用 zlib 对 http 包的内容进行 gzip ，所以需要在 Centos 上安装 zlib 库。

yum install -y zlib zlib-devel

四. OpenSSL 安装
OpenSSL 是一个强大的安全套接字层密码库，囊括主要的密码算法、常用的密钥和证书封装管理功能及 SSL 协议，并提供丰富的应用程序供测试或其它目的使用。
nginx 不仅支持 http 协议，还支持 https（即在ssl协议上传输http），所以需要在 Centos 安装 OpenSSL 库。

yum install -y openssl openssl-devel


## 安装 nginx

### 下载
使用wget命令下载（推荐）。确保系统已经安装了wget，如果没有安装，执行 yum install wget 安装。

wget -c https://nginx.org/download/nginx-1.12.0.tar.gz

### 解压
tar -zxvf nginx-1.12.0.tar.gz

### 安装
cd nginx-1.12.0

其实在 nginx-1.12.0 版本中你就不需要去配置相关东西，默认就可以了。当然，如果你要自己配置目录也是可以的。
1.使用默认配置

./configure
2.自定义配置（不推荐）

./configure \
--prefix=/usr/local/nginx \
--conf-path=/usr/local/nginx/conf/nginx.conf \
--pid-path=/usr/local/nginx/conf/nginx.pid \
--lock-path=/var/lock/nginx.lock \
--error-log-path=/var/log/nginx/error.log \
--http-log-path=/var/log/nginx/access.log \
--with-http_gzip_static_module \
--http-client-body-temp-path=/var/temp/nginx/client \
--http-proxy-temp-path=/var/temp/nginx/proxy \
--http-fastcgi-temp-path=/var/temp/nginx/fastcgi \
--http-uwsgi-temp-path=/var/temp/nginx/uwsgi \
--http-scgi-temp-path=/var/temp/nginx/scgi
注：将临时文件目录指定为/var/temp/nginx，需要在/var下创建temp及nginx目录

3.编译
make
4.安装
make install

5.查找安装路径：

whereis nginx

6.设置命令别名，方便全局使用
vi ~/.bash_profile 
source ~/.bash_profile
启动、停止nginx
nginx 
或者
cd /usr/local/nginx/sbin/
./nginx 
./nginx -s stop
./nginx -s quit
./nginx -s reload

启动时报80端口被占用:
nginx: [emerg] bind() to 0.0.0.0:80 failed (98: Address already in use)

查询nginx进程：

ps aux|grep nginx

重新加载配置文件：
当 nginx的配置文件 nginx.conf 修改后，要想让配置生效需要重启 nginx，使用-s reload不用先停止 ngin x再启动 nginx 即可将配置信息在 nginx 中生效，如下：
./nginx -s reload

开机自启动
即在rc.local增加启动代码就可以了。

vi /etc/rc.local
增加一行 /usr/local/nginx/sbin/nginx
设置执行权限：

chmod 755 rc.local

### 默认目录

/usr/local/nginx

安装配置好nginx服务器后默认目录是/usr/local/nginx/html

更改目录
vi /etc/nginx/conf.d/default.conf

### 访问不了虚拟机nignx
确定主机ip和虚拟机是同一网段下考虑虚拟机防火墙
systemctl status firewalld.service
关闭防火墙
systemctl stop firewalld.service #关闭firewall
或者开放某个端口
firewall-cmd --zone=public --add-port=80/tcp --permanent


 命令含义：
--zone #作用域
--add-port=1935/tcp  #添加端口，格式为：端口/通讯协议
--permanent  #永久生效，没有此参数重启后失效

重启防火墙
      firewall-cmd --reload

查看端口号
netstat -ntlp   //查看当前所有tcp端口·

netstat -ntulp |grep 80   //查看所有1935端口使用情况·

# ngnix + php

## 配置nginx可以运行php文件

### 配置
1、配置php.ini
首先定位配置文件php.ini 的位置。

php --ini | grep Loaded

编辑php.ini
cd /home/work/soft/php7/lib/
vi php.ini
编辑fix_pathinfo的设置内容。
在这个文件中，找到设置cgi.fix_pathinfo的参数。 这将用分号（;）注释掉，默认设置为“1”。
这是一个非常不安全的设置，因为它告诉PHP尝试执行最近的文件，如果找不到请求的PHP文件，它可以找到它。 这可以允许用户以一种允许他们执行不应允许执行的脚本的方式制作PHP请求。
取消注释并将其设置为“0”，如下所示：


2、配置nginx的PHP解析

vi /usr/local/nginx/conf/nginx.conf


注意，这里用户用从nginx改为www-data。

修改前	修改后	修改目的
user nginx;	user www-data www-data;	为了保证解析php时，对/run/php/php7.2-fpm.sock有正确的存取权限。
nginx安装好之后，会创建/etc/nginx/conf.d/default.conf文件，里面记录网站的配置信息。


修改内容

将root(网站根目录) 设置放在外面
将index设置放在外面
打开.php文件的解析设置内容。就是“ location ~ .php$ { … }”这一段内容
修改前	|修改后|	修改目的
#fastcgi_pass localhost:9000|	fastcgi_pass unix:/run/php/php7.2-fpm.sock;|	默认安装是sock方式
#fastcgi_param SCRIPT_FILENAME /scripts$fastcgi_script_name;|	fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;	|
php-fpm的fpm 指FastCGI Process Manager：FastCGI进程管理器，是一种面向高负载的PHP解释器。现在算是主流的PHP解释器。关于PHP-FPM更多的资料可以参考nginx 如何解析php文件php-fpm的解释。

