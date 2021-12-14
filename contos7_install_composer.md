# centos7 安装composer

## 准备
    需要正确安装好php
    openssl php扩展 
    ```
    yum install openssl
    yum install openssl-devel
    ```
    进入openssl的扩展目录 ：php-7.0.11/ext/openssl
    ```
    /home/work/soft/php7/bin/phpize 

    ./configure --with-openssl --with-php-config=/home/work/soft/php7/bin/php-config

    make

    make install
    ```
    修改php.ini文件
    增加
    ```
    extension=openssl
    ```
    composer 依赖git，zip ，unzip
    yum -y install git 
    yum install -y unzip zip
## 安装
```
curl -sS https://getcomposer.org/installer | php
```
执行后，会看到 Composer (version 2.1.14) successfully installed to: /root/composer.phar

进入/root/
```
mv composer.phar /usr/local/bin/composer
```
就可以 composer -v

如果 提示 /usr/bin/env: php:
创建php执行文件快捷文件 到 /usr/local/bin/php
```
ln -s /home/work/soft/php7/bin/php  /usr/local/bin/php
```

## composer使用

初始化项目
composer init

```
root@dduan:/home/dduan/test_composer# php composer.phar init
Do not run Composer as root/super user! See https://getcomposer.org/root for details

  Welcome to the Composer config generator
  
This command will guide you through creating your composer.json config.

# 1. 输入项目命名空间
# 注意<vendor>/<name> 必须要符合 [a-z0-9_.-]+/[a-z0-9_.-]+
Package name (<vendor>/<name>) [root/test_composer]:yourname/projectname

# 2. 项目描述
Description []:这是一个测试composer init 项目

# 3. 输入作者信息，直接回车可能出现如下提示，有的系统可以直接回车，具体为什么？这里不详细介绍
 Invalid author string.  Must be in the format: John Smith <john@example.com>
# 3.1. 注意必须要符合 John Smith <john@example.com>
Author [, n to skip]: John Smith <john@example.com>

# 4. 输入最低稳定版本，stable, RC, beta, alpha, dev
Minimum Stability []:dev

# 5. 输入项目类型
Package Type (e.g. library, project, metapackage, composer-plugin) []:library

# 6. 输入授权类型
License []:

Define your dependencies.

# 7. 输入依赖信息
Would you like to define your dependencies (require) interactively [yes]?

# 7.1. 如果需要依赖，则输入要安装的依赖
Search for a package:php

# 7.2. 输入版本号
Enter the version constraint to require (or leave blank to use the latest version): >=5.4.0

#  如需多个依赖，则重复以上两个步骤(7.1/7.2)
Search for a package:

# 8. 是否需要require-dev，
Would you like to define your dev dependencies (require-dev) interactively [yes]?

# 9.composer.json 例子
{
    "name": "brightness/demo",
    "type": "project",
    "minimum-stability": "dev",
    "require": {},
    "autoload": {
        "psr-4": {
            "app\\": "app"
        },
        "psr-0": {
            "": "extend/"
        }
    }
}




# 10.安装依赖
在项目一层文件夹中
composer install
# 11.添加依赖
在项目一层文件夹中
例如安装  firebase/php-jwt
```
composer require firebase/php-jwt
```
# 11.文件结构
.
├── app
├── composer.json
├── composer.lock
├── public
│   └── index.php
├── server
└── vendor
    ├── autoload.php
    ├── composer

    