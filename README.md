# What is DDNS-SCRIPT

这是一个 DDNS 的自动化脚本，可用于动态 IP 环境内的需要映射到某一固定域名的 WEB 服务器、Mail 服务器等。
目前是基于 PHP 环境，以后会提供 Python 版本。

## Requirements

PHP >= 5.6

## Changelog

Versioin 0.0.2 [2016-04-07]
    * add timestamp in log file

## How to use

在 Linux OS 中可以通过设置 crontab 来自动运行脚本，下面以 Debian Jessie 示例：

Step 1: 打开 crontab 设置窗口
    ```
    crontab -e
    ```
Step 2: 追加自动运行示例, 这里设置为每 5 分钟执行一次
    ```
    */5 * * * * /usr/local/php/bin/php /script/path/ddns.php >> /script/path/log 2>&1
    ```
Step 3: save and exit