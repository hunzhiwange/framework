![](queryphp.png)

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

# The QueryPHP Framework

> This is the core framework code of QueryPHP application, starts from this moment with [QueryPHP](https://github.com/hunzhiwange/queryphp).

QueryPHP is a modern, high performance PHP 7 resident framework, with engineer user experience as its historical mission, let every PHP application have a good framework.

A hundred percent coverage of the unit tests to facing the bug,based on Zephir implemented framework resident,with Swoole ecology to achieve business resident,
now or in the future step by step. Our vision is **<span style="color:#e82e7d;">USE LEEVEL WITH SWOOLE DO BETTER</span>**, let your business to support more user services.

*The PHP Framework For Code Poem As Free As Wind, Starts From This Moment With QueryPHP.*

* Site: <https://www.queryphp.com/>
* API: <https://api.queryphp.com>
* Document: <https://www.leevel.vip/>

![](doyouhaobaby.png)

QueryPHP was based on the [DoYouHaoBaby](https://raw.githubusercontent.com/hunzhiwange/framework/master/doyouhaobaby-googlecode.jpg) framework which released 0.0.1 version at 2010.10.03.

## The core packages

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

## Optional C Extension

<p>
  <a href="https://github.com/hunzhiwange/leevel">
    <img alt="Leevel Version" src="https://img.shields.io/badge/leevel-=1.0.0-brightgreen.svg" /></a>
  <a href="http://pecl.php.net/package/swoole">
    <img alt="Swoole Version" src="https://img.shields.io/badge/swoole-%3E=4.1.0-brightgreen.svg" /></a>
  <a href="https://github.com/apache/thrift/tree/master/lib/php">
    <img alt="Thrift Version" src="https://img.shields.io/badge/thrift-=0.10.0-brightgreen.svg" /></a>
  <a href="http://pecl.php.net/package/v8js">
    <img alt="V8js Version" src="https://img.shields.io/badge/v8js-%3E=2.1.0-brightgreen.svg" /></a>
</p>

We think the performance of PHP applications is very important and the development of pleasure also needs to be considered, and that's why we have developed the QueryPHP framework to achieve great ideals.

* PHP 7 - We choose the lowest version of 7.1.3, because php7 has a unique advantage over the earlier version.
* Leevel - We provides an optional c extension to takeover core components of the framework,such as ioc, log, cache.
* Swoole - Enable PHP developers to write high-performance, scalable, concurrent TCP, UDP, Unix socket, HTTP, Websocket services in PHP programming language.
* Redis - QueryPHP encapsulation a cache component, including files, redis and so on, so as to reduce the pressure of database.

```
Wow! Cool! Query Yet Simple!
```

## How to install

```
composer require hunzhiwange/framework
```

## The components that make up the QueryPHP framework

Components for the framework can be found on the [Github website](https://github.com/queryyetsimple) and [Packagist website](https://packagist.org/packages/leevel/).

```
composer require leevel/di
composer require leevel/cache

... and more
```

## Run Tests

```
_____________                           _______________
 ______/     \__  _____  ____  ______  / /_  _________
  ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
   __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
     \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
        \_\                /_/_/         /_/

$cd /data/codes/queryphp/vendor/hunzhiwange/framework      
$composer install
$php vendor/bin/phpunit tests
```

## Unified Code Style

```
$cd /data/codes/queryphp/vendor/hunzhiwange/framework
$php-cs-fixer fix --config=.php_cs.dist
```

## Travis CI Supported

Let code poem.

## Official Documentation

Documentation for the framework can be found on the [QueryPHP website](http://www.queryphp.com).

## Thanks

Thanks my colleague [John.mao](https://github.com/maosea0125) for your selfless help in the development of this project and and let me have a new understanding, it makes QueryPHP more beautiful.

Thanks for these excellent projects, we have absorbed a lot of excellent design and ideas, standing on the shoulders of giants for innovation.

 * QeePHP: <https://github.com/dualface/qeephp2_x/>
 * JeCat: <https://github.com/JeCat/>
 * ThinkPHP: <https://github.com/top-think/>
 * Laravel: <https://github.com/laravel/>
 * Symfony: <https://github.com/symfony/>
 * Doctrine: <https://github.com/doctrine/>


## License

The QueryPHP framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
