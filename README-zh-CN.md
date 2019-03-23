<img src="./queryphp.png" />

<p align="center">
  <a href="https://github.styleci.io/repos/91284136"><img src="https://github.styleci.io/repos/91284136/shield?branch=master" alt="StyleCI"></a>
  <a href="https://travis-ci.org/hunzhiwange/framework">
    <img alt="Build Status" src="https://img.shields.io/travis/hunzhiwange/framework.svg" /></a>
  <a href='https://coveralls.io/github/hunzhiwange/framework?branch=master'><img src='https://coveralls.io/repos/github/hunzhiwange/framework/badge.svg?branch=master' alt='Coverage Status' /></a>
  <a href="https://github.com/hunzhiwange/framework/releases">
    <img alt="Latest Version" src="https://poser.pugx.org/hunzhiwange/framework/version" /></a>
  <a href="http://opensource.org/licenses/MIT">
    <img alt="QueryPHP License" src="https://poser.pugx.org/hunzhiwange/framework/license.svg" /></a>
</p>

<p align="center">
    <a href="./README.md">English</a> | <a href="./README-zh-CN.md">中文</a>
</p>

# QueryPHP 渐进式 PHP 常驻框架引擎 (核心包)

> 这里是 QueryPHP 框架的核心包, 此刻携手 [QueryPHP](https://github.com/hunzhiwange/queryphp) 共创美好.

QueryPHP 是一款现代化的高性能 PHP 7 常驻框架，以工程师用户体验为历史使命，让每一个 PHP 应用都有一个好框架。

百分之百单元测试覆盖直面 Bug 一剑封喉，基于 Zephir 实现框架常驻，依托 Swoole 生态实现业务常驻，此刻未来逐步渐进。 我们的愿景是 **<span style="color:#e82e7d;">USE LEEVEL WITH SWOOLE DO BETTER</span>**, 让您的业务撑起更多的用户服务。

*代码如诗，自由如风, 此刻携手 QueryPHP 共创美好.*

* 官方网站: <https://www.queryphp.com/>
* API 文档: <https://api.queryphp.com>
* 开发文档: <https://www.queryphp.com/docs/>

![](doyouhaobaby.png)

QueryPHP 基于一款成立于 2010 年的 PHP 框架 [DoYouHaoBaby](https://raw.githubusercontent.com/hunzhiwange/framework/master/doyouhaobaby-googlecode.jpg) 开发，继承了上一代产品的优秀之处，彻底革新并进行了长达 2 年重构.

## 核心包

 * QueryPHP On Github: <https://github.com/hunzhiwange/queryphp/>
 * QueryPHP On Gitee: <https://gitee.com/dyhb/queryphp/>
 * Framework On Github: <https://github.com/hunzhiwange/framework/>
 * Framework On Gitee: <https://gitee.com/dyhb/framework/>
 * Leevel On Github: <https://github.com/hunzhiwange/leevel/>
 * Leevel On Gitee: <https://gitee.com/dyhb/leevel>
 * Tests: <https://github.com/leevels/tests/>
 * Packages: <https://github.com/leevels/>
 * Packages From Hunzhiwange: <https://packagist.org/packages/hunzhiwange/>
 * Packages From Leevel: <https://packagist.org/packages/leevel/>

## 可选 C 扩展

<p>
  <a href="https://github.com/hunzhiwange/leevel">
    <img alt="Leevel Version" src="https://img.shields.io/badge/leevel-=1.0.0-brightgreen.svg" /></a>
  <a href="http://pecl.php.net/package/swoole">
    <img alt="Swoole Version" src="https://img.shields.io/badge/swoole-%3E=4.2.6-brightgreen.svg" /></a>
  <a href="http://pecl.php.net/package/v8js">
    <img alt="V8js Version" src="https://img.shields.io/badge/v8js-%3E=2.1.0-brightgreen.svg" /></a>
  <a href="https://github.com/spiral/roadrunner">
    <img alt="Roadrunner Version" src="https://img.shields.io/badge/roadrunner-%3E=1.3.5-brightgreen.svg" /></a>
</p>

我们认为，PHP 应用程序的性能非常重要，但是工程师开发乐趣也需要被考虑，这就是我们开发 QueryPHP 框架以实现伟大理想的原因。

* PHP 7 - 我们选择 7.1.3 最为最低版本，因为 PHP7 相对于早期版本具有独特的优势。
* Leevel - 我们提供了一个可选的 C 扩展来接管框架的核心组件，如 IOC、日志、缓存。
* Swoole - 使 PHP 开发人员可以编写高性能的异步并发 TCP、UDP、Unix Socket、HTTP，WebSocket 服务。
* Redis - QueryPHP 实现了缓存组件封装，包括文件、Redis，它们可以降低数据库压力。
* Roadrunner - RoadRunner 是一个开源的高性能 PHP 应用服务器、负载均衡器和流程管理器。它支持作为一个服务运行，能够在每个项目的基础上扩展其功能。

```
Wow! Cool! Query Yet Simple!
```

## 如何安装

```
composer require hunzhiwange/framework
```

## 基于组件化的 QueryPHP

QueryPHP 框架提供的组件可以在 [Github website](https://github.com/queryyetsimple) 和 [Packagist website](https://packagist.org/packages/leevel/) 上面找到.

```
composer require leevel/di
composer require leevel/cache

... and more
```

## 运行测试文件

```
_____________                           _______________
 ______/     \__  _____  ____  ______  / /_  _________
  ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
   __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
     \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
        \_\                /_/_/         /_/

$cd /data/codes/queryphp/vendor/hunzhiwange/framework      
$composer install
$cp ./tests/config.php ./tests/config.local.php // Modify the config
$php vendor/bin/phinx migrate
$php vendor/bin/phpunit tests
```

## 框架文档生成

```
$cd /data/codes/queryphp
$php leevel make:docwithin test
```

## 统一团队代码风格

### 安装 PHP 代码格式化工具

<https://github.com/friendsofphp/php-cs-fixer>

### 基本使用

```
$cd /data/codes/queryphp/vendor/hunzhiwange/framework
$php-cs-fixer fix --config=.php_cs.dist
```

### 使用 Git 钩子

添加一个 pre-commit 钩子.

```
cp ./build/pre-commit.sh ./.git/hooks/pre-commit
chmod 777 ./.git/hooks/pre-commit
```

跳过钩子

```
# git commit -h
# git commit -n -m 'pass hook' #bypass pre-commit and commit-msg hooks
```

## Travis CI 持续集成支持

让代码提交更值得信赖.

## 官方文档

请访问官方网站即即可查看 [Leevel website](http://www.queryphp.com).

## 致谢

感谢同事 [毛飞](https://github.com/maosea0125) 在开发这个项目过程中的无私帮助，让我有了很多新的认识, 这让 QueryPHP 变得更加的美好.

也非常感谢下面的这些优秀的开源软件, 我们也参考了很多的设计与思想, 让我们可以站在巨人的肩膀上保持创新.

 * QeePHP: <https://github.com/dualface/qeephp2_x/>
 * Swoole: <https://github.com/swoole/>
 * JeCat: <https://github.com/JeCat/>
 * ThinkPHP: <https://github.com/top-think/>
 * Laravel: <https://github.com/laravel/>
 * Symfony: <https://github.com/symfony/>
 * Doctrine: <https://github.com/doctrine/>
 * Phalcon: <https://github.com/phalcon/>

## 版权协议

QueryPHP 是一个基于 [MIT license](http://opensource.org/licenses/MIT) 授权许可协议的开源软件.
