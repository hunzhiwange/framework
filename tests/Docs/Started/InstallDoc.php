<?php

declare(strict_types=1);

namespace Tests\Docs\Started;

use Leevel\Kernel\Utils\Api;

#[Api([
    'title' => 'Install',
    'zh-CN:title' => '安装',
    'zh-TW:title' => '安裝',
    'path' => 'started/install',
    'description' => <<<'EOT'
QueryPHP 是一个渐进式 PHP 常驻框架，我们强调的是一个渐进式，它既可以运行在 PHP-FPM 场景，同时还支持在 RoadRunner 服务中运行。
EOT,
    'zh-CN:description' => <<<'EOT'
QueryPHP 是一个渐进式 PHP 常驻框架，我们强调的是一个渐进式，它既可以运行在 PHP-FPM 场景，同时还支持在 RoadRunner 服务中运行。
EOT,
    'zh-TW:description' => <<<'EOT'
QueryPHP 是一个渐进式 PHP 常驻框架，我们强调的是一个渐进式，它既可以运行在 PHP-FPM 场景，同时还支持在 RoadRunner 服务中运行。
EOT,
])]
class InstallDoc
{
    #[Api([
        'zh-CN:title' => '环境要求',
        'zh-CN:description' => <<<'EOT'
事实上，QueryPHP 也是一个普通的 PHP 框架，目前最低版本要求 PHP 7.4.0，我们对环境并没有特别的要求。

 * PHP ^8.1.0
 * ext-mbstring [字符处理](https://github.com/hunzhiwange/framework/blob/master/src/Leevel/Support/Str.php)
 * ext-openssl [加密组件](https://github.com/hunzhiwange/framework/blob/master/src/Leevel/Encryption/Encryption.php)

我们系统依赖的组件可以通过 [composer.json](https://github.com/hunzhiwange/queryphp/blob/master/composer.json) 找到，我们提供了大量开箱即用的功能。

实际上，QueryPHP 对于环境来说`只需要`安装一个 `PHP 8.1.0` 及以上版本即可，这个时候甚至无需安装 Nginx 而使用 PHP 内置 WebServer 即可将 QueryPHP 跑起来。

对于每位 PHP 工程师来说，您的电脑早已经运行着一个 PHP 7 版本，接着您可以进行安装了。
EOT,
    ])]
    public function doc1(): void
    {
    }

    #[Api([
        'zh-CN:title' => '国内镜像',
        'zh-CN:description' => <<<'EOT'
QueryPHP 使用 [Composer](https://developer.aliyun.com/composer) 来管理整个项目依赖，因此确保您已经安装了 Composer。

国外镜像访问速度很慢，我们建议使用国内阿里云镜像。

 * 镜像 1 <https://developer.aliyun.com/composer>

``` sh
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```
EOT,
    ])]
    public function doc2(): void
    {
    }

    #[Api([
        'zh-CN:title' => 'Composer 安装',
        'zh-CN:description' => <<<'EOT'
你可以在终端中运行 `create-project` 命令来安装 QueryPHP.

### 安装

``` sh
composer create-project --prefer-dist hunzhiwange/queryphp myapp
```

或者体验开发版本

``` sh
composer create-project hunzhiwange/queryphp myapp dev-master
```

### 运行

你也可以简单实用 PHP 内置的服务器来运行 QueryPHP,当然更好的选择配置 Nginx 站点。

``` sh
php leevel server <Visite http://127.0.0.1:9527/>
```

* 首页 <http://127.0.0.1:9527/>
* MVC 路由 <http://127.0.0.1:9527/api/test>
* MVC restful 路由 http://127.0.0.1:9527/restful/123
* 指定方法的 MVC restful 路由 http://127.0.0.1:9527/restful/123/show
* 注解路由 http://127.0.0.1:9527/api/v1/petLeevelForApi/helloworld
* 带有绑定的注解路由 http://127.0.0.1:9527/api/v2/withBind/foobar
* php leevel link:public <http://127.0.0.1:9527/public/css/page.css>
* php leevel link:storage <http://127.0.0.1:9527/storage/logo.png>
* php leevel link:apis <http://127.0.0.1:9527/apis/>
* php leevel link:debugbar <http://127.0.0.1:9527/debugbar/debugbar.css>

::: tip
QueryPHP 在 composer 安装过程中自动运行了创建软连接的命令将一些非 Web 根目录的站点映射到根目录，
这样我们可以使用内置的服务来访问这些链接。这些服务包含: 公共资源（public）、上传文件（storage）、
Swagger Api（apis）、Debugbar 调试（debugbar）。
:::
EOT,
    ])]
    public function doc3(): void
    {
    }

    #[Api([
        'zh-CN:title' => '基础配置',
        'zh-CN:description' => <<<'EOT'
QueryPHP 在初始化应用程序会自动帮您创建 `.env`、`.env.phpunit` 文件。

 * .env (环境配置)
 * .env.phpunit (单元测试环境配置)

### 入口目录

您必须将 Web 站点的根目录指向 `www` 目录，其中 `index.php` 是整个应用的单一入口文件，例如 Nginx。

```
root /data/codes/queryphp/www;
index index.html index.php;
```

### 目录权限

系统有几个目录需要配置写入权限 `storage`。
EOT,
    ])]
    public function doc4(): void
    {
    }

    #[Api([
        'zh-CN:title' => '搭建站点',
        'zh-CN:description' => <<<'EOT'
首先需要创建一个站点的配置文件,例如 `/server/nginx-1.6.2/vhosts/queryphp.conf`:

```
server {
    add_header HostName php-7.4.0-app1;
    listen 8080;
    server_name queryphp.cn  *.queryphp.cn;
    error_log  /var/log/nginx/queryphp.error.log;
    access_log /var/log/nginx/queryphp.access.log main;
    root /data/codes/queryphp/www;
    index  index.html index.php;

    location / {
       try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /nginx_status$ {
        stub_status on;
        access_log off;
        allow  all;
    }
}
```

::: tip
笔者因为会在 Mac 中将端口转发到虚拟机中的 8080 端口，您的开发环境直接配置 80 端口即可。
:::

修改虚拟机 `/etc/hosts`,添加几个测试域名，后面 `vip` 这些主要用于调试路由域名匹配，可以不要 。
Mac 系统的 `/etc/hosts` 也需要添加这些如下域名。

```
127.0.0.1      queryphp.cn
127.0.0.1      www.queryphp.cn
127.0.0.1      test.queryphp.cn
127.0.0.1      vip.queryphp.cn
127.0.0.1      x.vip.queryphp.cn
```

刷新虚拟机 Ubuntu 网络使域名生效

``` sh
/etc/rc.d/init.d/network restart
```

重启 `Nginx`

``` sh
service nginx restart
```

访问地址

* 首页 <http://queryphp.cn/>
* MVC 路由 <http://queryphp.cn/api/test>
* MVC restful 路由 http://queryphp.cn/restful/123
* 指定方法的 MVC restful 路由 http://queryphp.cn/restful/123/show
* 注解路由 http://queryphp.cn/api/v1/petLeevelForApi/helloworld
* 带有绑定的注解路由 http://queryphp.cn/api/v2/withBind/foobar
* php leevel link:public <http://queryphp.cn/public/css/page.css>
* php leevel link:storage <http://queryphp.cn/storage/logo.png>
* php leevel link:apis <http://queryphp.cn/apis/>
* php leevel link:debugbar <http://queryphp.cn/debugbar/debugbar.css>

### Apache

Web 根目录已经内置了 `www/.htaccess` 文件来为隐藏 index.php,需要启用 mod_rewrite 模块。

```
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Configs -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
```
EOT,
    ])]
    public function doc5(): void
    {
    }
}
