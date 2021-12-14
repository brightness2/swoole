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
1.使用配置

./configure --prefix=/usr/local/nginx \
--with-stream \
--with-stream_ssl_module \
--with-http_ssl_module \
--with-http_v2_module \
--with-threads
2.自定义配置

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

## php 编译与安装
1、进入php 源码包
```
./configure -prefix=/usr/local/php --enable-fpm --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd 

make
make install
必须 开启fpm,nginx通过php-fpm服务监听运行php文件

```
2、把php源码包的php.ini-production 文件复制到/usr/local/php/lib/php.ini
```
cp php.ini-production /usr/local/php/lib/php.ini
```
3、把php源码包的 sapi/fpm/init.d.php-fpm 文件复制到 /etc/init.d/php-fpm
```
cp init.d.php-fpm /etc/init.d/php-fpm

chmod +x /etc/init.d/php-fpm 
```
4、php安装路径中 /usr/loacl/php 的 fpm配置
```
cp /usr/local/php/etc/php-fpm.conf.default /usr/local/php/etc/php-fpm.conf
cp /usr/local/php/etc/php-fpm.d/www.conf.default /usr/local/php/etc/php-fpm.d/www.conf
查看是否安装成功
ps -ef|grep php-fpm
php-fpm命令(开启/重启/停止):
/etc/init.d/php-fpm start/restart/stop
添加开机启动
chkconfig --add php-fpm
查看是否添加成功
chkconfig | grep php-fpm

注：该输出结果只显示 SysV 服务，并不包含
原生 systemd 服务。SysV 配置数据
可能被原生 systemd 配置覆盖。 

      要列出 systemd 服务，请执行 'systemctl list-unit-files'。
      查看在具体 target 启用的服务请执行
      'systemctl list-dependencies [target]'。

php-fpm        	0:关	1:关	2:开	3:开	4:开	5:开	6:关
2,3,4,5登录为开启状态,表示添加成功,如果为关闭状态可以用chkconfig php-fpm on开启
```


## 配置nginx可以运行php文件
修改文件 /usr/local/nginx/conf/nginx.conf
```
location ~ \.php$ {
      root /usr/local/nginx/html;     #网站目录
      fastcgi_pass 127.0.0.1:9000;
      fastcgi_index index.php;
      fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
      include fastcgi_params;
}
```