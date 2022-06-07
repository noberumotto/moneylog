# Moneylog.cloud API

Moneylog API 服务端，开发框架是基于 ThinkPHP 的 FastAdmin

## 环境要求

*建议*

PHP 7.3

MySQL 5.6

## 搭建

- 创建您的数据库，将 `moneylog.sql` 导入
- 将 `application\database.php.template` 更名为：`application\database.php`，并打开修改相应的数据库连接信息
- 根据需求修改 `application\config.php` 中的 `cache.type` 配置项，默认使用了 `redis`
- 将 `public` 目录设置为访问目录
- 访问后台 > 常规管理 > 系统配置 > 邮件配置 修改正确的配置，因为需要使用邮件发送注册验证码

## 访问后台

地址：http://url/admin.php

默认账号：admin

默认密码：123456

## 配置导入账单服务

Moneylog.cloud 通过定时执行 `command` 完成账单导入服务

```
使用命令 php think Import 处理1个文件

使用命令 php think Importclear 清空所有待处理账单队列缓存
```
