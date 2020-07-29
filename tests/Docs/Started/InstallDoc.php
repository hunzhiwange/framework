<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Docs\Started;

/**
 * @api(
 *     title="Install",
 *     zh-CN:title="安装",
 *     zh-TW:title="安裝",
 *     path="started/install",
 *     description="QueryPHP 是一个渐进式 PHP 常驻框架，我们强调的是一个渐进式，它既可以运行在 PHP-FPM 场景，同时还支持在 Swoole 服务中运行。",
 *     zh-CN:description="QueryPHP 是一个渐进式 PHP 常驻框架，我们强调的是一个渐进式，它既可以运行在 PHP-FPM 场景，同时还支持在 Swoole 服务中运行。",
 *     zh-TW:description="QueryPHP 是一个渐进式 PHP 常驻框架，我们强调的是一个渐进式，它既可以运行在 PHP-FPM 场景，同时还支持在 Swoole 服务中运行。",
 * )
 */
class InstallDoc
{
    /**
     * @api(
     *     zh-CN:title="环境要求",
     *     zh-CN:description="
     * 事实上，QueryPHP 也是一个普通的 PHP 框架，目前最低版本要求 PHP 7.4.0，我们对环境并没有特别的要求。
     *
     *  * PHP ^7.4.0
     *  * ext-mbstring [字符处理](https://github.com/hunzhiwange/framework/blob/master/src/Leevel/Support/Str.php)
     *  * ext-openssl [加密组件](https://github.com/hunzhiwange/framework/blob/master/src/Leevel/Encryption/Encryption.php)
     *
     * 我们系统依赖的组件可以通过 [composer.json](https://github.com/hunzhiwange/queryphp/blob/master/composer.json) 找到，我们提供了大量开箱即用的功能。
     *
     * 实际上，QueryPHP 对于环境来说`只需要`安装一个 `PHP 7.4.0` 及以上版本即可，这个时候甚至无需安装 Nginx 而使用 PHP 内置 WebServer 即可将 QueryPHP 跑起来。
     *
     * 对于每位 PHP 工程师来说，您的电脑早已经运行着一个 PHP 7 版本，接着您可以进行安装了。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function doc1()
    {
    }

    /**
     * @api(
     *     zh-CN:title="国内镜像",
     *     zh-CN:description="
     * QueryPHP 使用 [Composer](https://developer.aliyun.com/composer) 来管理整个项目依赖，因此确保您已经安装了 Composer。
     *
     * 国外镜像访问速度很慢，我们建议使用国内阿里云镜像。
     *
     *  * 镜像 1 <https://developer.aliyun.com/composer>
     *
     * ``` sh
     * composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function doc2()
    {
    }

    /**
     * @api(
     *     zh-CN:title="Composer 安装",
     *     zh-CN:description="
     * 你可以在终端中运行 `create-project` 命令来安装 QueryPHP.
     *
     * ### 安装
     *
     * ``` sh
     * composer create-project --prefer-dist hunzhiwange/queryphp myapp
     * ```
     *
     * 或者体验开发版本
     *
     * ``` sh
     * composer create-project hunzhiwange/queryphp myapp dev-master
     * ```
     *
     * ### 运行
     *
     * 你也可以简单实用 PHP 内置的服务器来运行 QueryPHP,当然更好的选择配置 Nginx 站点。
     *
     * ``` sh
     * php leevel server <Visite http://127.0.0.1:9527/>
     * ```
     *
     * * 首页 <http://127.0.0.1:9527/>
     * * MVC 路由 <http://127.0.0.1:9527/api/test>
     * * MVC restful 路由 http://127.0.0.1:9527/restful/123
     * * 指定方法的 MVC restful 路由 http://127.0.0.1:9527/restful/123/show
     * * 注解路由 http://127.0.0.1:9527/api/v1/petLeevelForApi/helloworld
     * * 带有绑定的注解路由 http://127.0.0.1:9527/api/v2/withBind/foobar
     * * php leevel link:public <http://127.0.0.1:9527/public/css/page.css>
     * * php leevel link:storage <http://127.0.0.1:9527/storage/logo.png>
     * * php leevel link:apis <http://127.0.0.1:9527/apis/>
     * * php leevel link:debugbar <http://127.0.0.1:9527/debugbar/debugbar.css>
     *
     * ::: tip
     * QueryPHP 在 composer 安装过程中自动运行了创建软连接的命令将一些非 Web 根目录的站点映射到根目录，
     * 这样我们可以使用内置的服务来访问这些链接。这些服务包含: 公共资源（public）、上传文件（storage）、
     * Swagger Api（apis）、Debugbar 调试（debugbar）。
     * :::
     * ",
     *     zh-CN:note="",
     * )
     */
    public function doc3()
    {
    }

    /**
     * @api(
     *     zh-CN:title="基础配置",
     *     zh-CN:description="
     * QueryPHP 在初始化应用程序会自动帮您创建 `.env`、`.env.phpunit`、`frontend/.env.local` 和 `frontend/.env.production` 文件。
     *
     *  * .env (环境配置)
     *  * .env.phpunit (单元测试环境配置)
     *  * frontend/.env.local (前端环境配置)
     *  * frontend/.env.production (前端生产环境配置)
     *
     * ### 入口目录
     *
     * 您必须将 Web 站点的根目录指向 `www` 目录，其中 `index.php` 是整个应用的单一入口文件，例如 Nginx。
     *
     * ```
     * root /data/codes/queryphp/www;
     * index index.html index.php;
     * ```
     *
     * ### 目录权限
     *
     * 系统有几个目录需要配置写入权限 `storage`、`bootstrap` 和 `runtime`,一个是资源上传目录，例外的是系统运行过程中的缓存。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function doc4()
    {
    }

    /**
     * @api(
     *     zh-CN:title="搭建站点",
     *     zh-CN:description="
     * 笔者的 QueryPHP 项目采用 VirtualBox + Vagrant 搭建的开发环境，可以运行在各种环境。
     *
     *   * Macos High Sierra 10.13.2
     *   * Atom with vim plugin、Subtime text3
     *   * VirtualBox 5.2.8
     *   * Vagrant
     *   * ubuntu-16.04-LTS
     *   * mysql-5.6.28
     *   * nginx-1.6.2
     *   * php-5.6.23
     *   * php-7.1.6
     *   * php-7.2.1
     *   * php-7.4.0
     *   * redis-2.8.17
     *
     * Windows 开发者如果不需要 Swoole 则可以按照其他普通的 PHP 项目来搭建就是了，如果依赖 Swoole 可以采用上面这种虚拟机的方式来搭建环境。
     *
     * ### Nginx
     *
     * 首先需要在 Ubuntu 虚拟机创建一个站点的配置文件,例如 `/server/nginx-1.6.2/vhosts/queryphp.conf`:
     *
     * ```
     * server {
     *     add_header HostName php-7.4.0-app1;
     *     listen 8080;
     *     server_name queryphp.cn  *.queryphp.cn;
     *     error_log  /var/log/nginx/queryphp.error.log;
     *     access_log /var/log/nginx/queryphp.access.log main;
     *     root /data/codes/queryphp/www;
     *     index  index.html index.php;
     *
     *     location / {
     *        try_files $uri $uri/ /index.php?$query_string;
     *     }
     *
     *     location ~ \.php$ {
     *         fastcgi_split_path_info ^(.+\.php)(/.+)$;
     *         fastcgi_pass 127.0.0.1:9000;
     *         fastcgi_index index.php;
     *         include fastcgi_params;
     *         fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
     *     }
     *
     *     location ~ /nginx_status$ {
     *         stub_status on;
     *         access_log off;
     *         allow  all;
     *     }
     * }
     * ```
     *
     * ::: tip
     * 笔者因为会在 Mac 中将端口转发到虚拟机中的 8080 端口，您的开发环境直接配置 80 端口即可。
     * :::
     *
     * 修改虚拟机 `/etc/hosts`,添加几个测试域名，后面 `vip` 这些主要用于调试路由域名匹配，可以不要 。
     * Mac 系统的 `/etc/hosts` 也需要添加这些如下域名。
     *
     * ```
     * 127.0.0.1      queryphp.cn
     * 127.0.0.1      www.queryphp.cn
     * 127.0.0.1      test.queryphp.cn
     * 127.0.0.1      vip.queryphp.cn
     * 127.0.0.1      x.vip.queryphp.cn
     * ```
     *
     * 刷新虚拟机 Ubuntu 网络使域名生效
     *
     * ``` sh
     * /etc/rc.d/init.d/network restart
     * ```
     *
     * 重启 `Nginx`
     *
     * ``` sh
     * service nginx restart
     * ```
     *
     * 访问地址
     *
     * * 首页 <http://queryphp.cn/>
     * * MVC 路由 <http://queryphp.cn/api/test>
     * * MVC restful 路由 http://queryphp.cn/restful/123
     * * 指定方法的 MVC restful 路由 http://queryphp.cn/restful/123/show
     * * 注解路由 http://queryphp.cn/api/v1/petLeevelForApi/helloworld
     * * 带有绑定的注解路由 http://queryphp.cn/api/v2/withBind/foobar
     * * php leevel link:public <http://queryphp.cn/public/css/page.css>
     * * php leevel link:storage <http://queryphp.cn/storage/logo.png>
     * * php leevel link:apis <http://queryphp.cn/apis/>
     * * php leevel link:debugbar <http://queryphp.cn/debugbar/debugbar.css>
     *
     * ### Apache
     *
     * Web 根目录已经内置了 `www/.htaccess` 文件来为隐藏 index.php,需要启用 mod_rewrite 模块。
     *
     * ```
     * <IfModule mod_rewrite.c>
     *     <IfModule mod_negotiation.c>
     *         Options -MultiViews -Indexes
     *     </IfModule>
     *
     *     RewriteEngine On
     *
     *     RewriteCond %{HTTP:Authorization} .
     *     RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
     *
     *     RewriteCond %{REQUEST_FILENAME} !-d
     *     RewriteCond %{REQUEST_FILENAME} !-f
     *     RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
     * </IfModule>
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function doc5()
    {
    }
}
