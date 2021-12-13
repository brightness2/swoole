# 基于centos7搭建LAMP服务器

## 系统初始设置
1、查看selinux 状态
```
sestatus
```

2、关闭selinux
```
vi /etc/sysconfig/selinux
```
修改
SELINUX=disabled

## 安装apache
安装
```
yum install httpd
yum install httpd-devel.x86_64
```
启动
```
systemctl start httpd
```

设置开机自启
```
systemctl enable httpd
```
查看apache状态
```
systemctl status httpd
```
如果外界访问不了，记得开启防火墙，80端口即可
```
firewall-cmd --zone=public --add-port=80/tcp --permanent
重启防火墙
      firewall-cmd --reload
```
静态文件根目录在 
/var/www/html

查看配置文件位置
```
httpd -V
```
/etc/httpd/conf/httpd.conf
## 安装mysql
下载mysql rpm 源
```
wget -i -c http://dev.mysql.com/get/mysql57-community-release-el7-10.noarch.rpm
```
安装 mysql rpm
```
yum install mysql57-community-release-el7-10.noarch.rpm
```
安装mysql
```
yum  install mysql-community-server
```
启动mysql
```
systemctl start mysqld.service
``
查看mysql状态
```
systemctl status mysqld.service
```
查看默认密码
```
grep "password" /var/log/mysqld.log
```
2021-12-11T17:45:59.530671Z 1 [Note] A temporary password is generated for root@localhost: fi%DTxp-r73X
密码是 fi%DTxp-r73X

登录并修改密码
```
mysql -uroot -p

ALTER USER 'root'@'localhost' IDENTIFIED BY 'new password';
```
其中‘new password’替换成你要设置的密码，注意:密码设置必须要大小写字母数字和特殊符号（,/';:等）,不然不能配置成功

如果只是测试机，不需要密码策略，可以修改
```
查看密码策略 并临时修改密码策略
SHOW VARIABLES LIKE 'validate_password%'; 

+--------------------------------------+--------+
| Variable_name                        | Value  |
+--------------------------------------+--------+
| validate_password_check_user_name    | OFF    |
| validate_password_dictionary_file    |        |
| validate_password_length             | 8      |
| validate_password_mixed_case_count   | 1      |
| validate_password_number_count       | 1      |
| validate_password_policy             | MEDIUM |
| validate_password_special_char_count | 1      |
+--------------------------------------+--------+
7 rows in set (0.01 sec)

降低密码强调
set global validate_password_policy=LOW;

修改密码最小长度
set global validate_password_length=4;

这样就可以设置简单的密码了
```


开启mysql的远程访问
```
指定ip 192.168.0.1
grant all privileges on *.* to 'root'@'192.168.0.1' identified by 'password' with grant option;

或者任意ip
grant all privileges on *.* to 'root'@'%' identified by 'password' with grant option;

密码是 'password'
刷新
flush privileges; 
```


退出
exit;

开启防火墙端口
```
firewall-cmd --zone=public --add-port=3306/tcp --permanent
firewall-cmd --reload
```
修改语言为utf-8
```
vi /etc/my.cnf
增加四行
[mysql]前面增加
[client]
default-character-set=utf8

末尾增加
character-set-server=utf8
collation-server=utf8_general_ci

然后重启mysql
```
systemctl restart mysqld.service
```

设置mysql开机自启
```
vi /etc/rc.local 
增加
systemctl restart mysqld.service
```

## 源码安装php
下载php7.4源码
```
wget https://www.php.net/distributions/php-7.4.26.tar.bz2

```
安装依赖工具
```
yum -y install bzip2
yum -y install gcc gcc-c++ kernel-devel
yum -y install libxml2 libxml2-devel
yum -y install sqlite-devel
yum -y install autoconf
```
解压php
```
tar -xjvf  php-7.4.26.tar.bz2 
```
执行shell 脚本,并指定安装路径，进行自动配置
```
查看apxs文件位置
which apxs


cd php-7.4.26
./configure –prefix=/usr/local/php  --with-apxs2=/usr/bin/apxs
```
```
常用参数
–prefix=/usr/local/php                  php安装目录
–with-apxs2=/usr/local/apache/bin/apxs
–with-config-file-path=/usr/local/php/etc      指定php.ini位置
–with-mysql=/usr/local/mysql           mysql安装目录，对mysql的支持
–with-mysqli=/usr/local/mysql/bin/mysql_config    mysqli文件目录,优化支持
–enable-safe-mode                              打开安全模式
–enable-ftp                                 打开ftp的支持
–enable-zip                                 打开对zip的支持
–with-bz2                    打开对bz2文件的支持                        
–with-jpeg-dir                                 打开对jpeg图片的支持
–with-png-dir                                 打开对png图片的支持
–with-freetype-dir              打开对freetype字体库的支持
–without-iconv                关闭iconv函数，种字符集间的转换
–with-libxml-dir                 打开libxml2库的支持
–with-xmlrpc              打开xml-rpc的c语言
–with-zlib-dir                                 打开zlib库的支持
–with-gd                                    打开gd库的支持
–enable-gd-native-ttf               支持TrueType字符串函数库
–with-curl                      打开curl浏览工具的支持
–with-curlwrappers                 运用curl工具打开url流
–with-ttf                      打开freetype1.*的支持，可以不加了
–with-xsl            打开XSLT文件支持，扩展了libxml2库 ，需要libxslt软件
–with-gettext                      打开gnu的gettext 支持，编码库用到
–with-pear            打开pear命令的支持，php扩展用的
–enable-calendar             打开日历扩展功能
–enable-mbstring                  多字节，字符串的支持
–enable-bcmath                  打开图片大小调整,用到zabbix监控的时候用到了这个模块
–enable-sockets                  打开sockets 支持
–enable-exif                     图片的元数据支持
–enable-magic-quotes               魔术引用的支持
–disable-rpath                     关闭额外的运行库文件
–disable-debug                  关闭调试模式
–with-mime-magic=/usr/share/file/magic.mime      魔术头文件位置

cgi方式安装才用的参数
–enable-fpm                     打上php-fpm补丁后才有这个参数，cgi方式安装的启动程序
–enable-fastcgi                  支持fastcgi方式启动php
–enable-force-cgi-redirect            同上,帮助里没有解释
–with-ncurses                     支持ncurses屏幕绘制以及基于文本终端的图形互动功能的动态库
–enable-pcntl           freeTDS需要用到的，可能是链接mssql才用到

mhash和mcrypt算法的扩展
–with-mcrypt                     算法
–with-mhash                     算法

–with-gmp
–enable-inline-optimization
–with-openssl           openssl的支持，加密传输时用到的
–enable-dbase
–with-pcre-dir=/usr/local/bin/pcre-config    perl的正则库案安装位置
–disable-dmalloc
–with-gdbm                    dba的gdbm支持
–enable-sigchild
–enable-sysvsem
–enable-sysvshm
–enable-zend-multibyte              支持zend的多字节
–enable-mbregex
–enable-wddx
–enable-shmop
–enable-soap
```

编译和安装
```
make

make install
```

以下是安装信息

```
Installing shared extensions:     /usr/local/php/lib/php/extensions/no-debug-non-zts-20190902/
Installing PHP CLI binary:        /usr/local/php/bin/
Installing PHP CLI man page:      /usr/local/php/php/man/man1/
Installing phpdbg binary:         /usr/local/php/bin/
Installing phpdbg man page:       /usr/local/php/php/man/man1/
Installing PHP CGI binary:        /usr/local/php/bin/
Installing PHP CGI man page:      /usr/local/php/php/man/man1/
Installing build environment:     /usr/local/php/lib/php/build/
Installing header files:          /usr/local/php/include/php/
Installing helper programs:       /usr/local/php/bin/
  program: phpize
  program: php-config
Installing man pages:             /usr/local/php/php/man/man1/
  page: phpize.1
  page: php-config.1
/home/php-7.4.26/build/shtool install -c ext/phar/phar.phar /usr/local/php/bin/phar.phar
ln -s -f phar.phar /usr/local/php/bin/phar
Installing PDO headers:           /usr/local/php/include/php/ext/pdo/
```

测试是否安装成功

```
cd /usr/local/php/bin/

php -v
```
查看php.ini 文件的默认路径
```
    php --ini
```
把源码中的php.ini-development 复制到 /usr/local/php/lib/php.ini 文件夹

## apache 结合php 运行php文件
修改 /etc/httpd/conf/httpd.conf 文件
在最后增加
```
IncludeOptional conf.d/*.conf
LoadModule php7_module modules/libphp7.so
<FilesMatch \.php$>
	SetHandler application/x-httpd-php
</FilesMatch>
```