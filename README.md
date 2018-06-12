![](queryphp.png)

<p align="center">
  <a href="https://github.styleci.io/repos/91284136"><img src="https://github.styleci.io/repos/91284136/shield?branch=master" alt="StyleCI"></a>
  <a href="https://github.com/hunzhiwange/framework/releases">
    <img alt="Latest Version" src="https://img.shields.io/packagist/vpre/hunzhiwange/framework.svg" /></a>
  <a href="https://travis-ci.org/hunzhiwange/framework">
    <img alt="Build Status" src="https://img.shields.io/travis/hunzhiwange/framework.svg" /></a>
  <a href="https://secure.php.net/">
    <img alt="Php Version" src="https://img.shields.io/packagist/php-v/hunzhiwange/framework.svg" /></a>
  <a href="http://opensource.org/licenses/MIT">
    <img alt="QueryPHP License" src="https://img.shields.io/packagist/l/hunzhiwange/framework.svg" /></a>
</p>

# The QueryPHP Framework

QueryPHP is a powerful PHP framework for code poem as free as wind. [Query Yet Simple]

QueryPHP was founded in 2010 and released the first version on 2010.10.03.

QueryPHP was based on the DoYouHaoBaby framework，we have a large code refactoring.

![](doyouhaobaby.png)

<p>DoYouHaoBaby has a lot of features: MVC, ActiveRecord, i18n, cache, databases, template engine, RBAC, and so on.</p>

<p>DoYouHaoBaby released 0.0.1 version at 2010/10/03, the last version was released in 2014/10 version 3, and now it has stopped maintenance.</p>

## Optional C Extension

<p>
  <a href="https://github.com/hunzhiwange/leevel">
    <img alt="Leevel Version" src="https://img.shields.io/badge/leevel-%3E=1.0.0-brightgreen.svg" /></a>
  <a href="http://pecl.php.net/package/swoole">
    <img alt="Swoole Version" src="https://img.shields.io/badge/swoole-%3E=2.1.1-brightgreen.svg" /></a>
  <a href="https://github.com/apache/thrift/tree/master/lib/php">
    <img alt="Thrift Version" src="https://img.shields.io/badge/thrift-%3E=0.10.0-brightgreen.svg" /></a>
  <a href="http://pecl.php.net/package/inotify">
    <img alt="Inotify Version" src="https://img.shields.io/badge/inotify-%3E=2.0.0-brightgreen.svg" /></a>
  <a href="http://pecl.php.net/package/v8js">
    <img alt="V8js Version" src="https://img.shields.io/badge/v8js-%3E=2.1.0-brightgreen.svg" /></a>
</p>

![](php7_swoole_cextension_redis.png)

<a href="https://github.com/ThinkDevelopers/PHPConChina/blob/master/PHPCON2016/PHP7%2BSwoole%E5%BC%80%E5%8F%91%E8%B6%85%E9%AB%98%E6%80%A7%E8%83%BD%E5%90%8E%E5%8F%B0%E7%A8%8B%E5%BA%8F--%E9%9F%A9%E5%A4%A9%E5%B3%B0%40PHPCon2016.pdf" target="_blank">@PHP7+Swoole开发超高性能后台程序--韩天峰@PHPCon2016.pdf</a>

We think the performance of PHP applications is very important and the development of pleasure also needs to be considered, and that's why we have developed the QueryPHP framework to achieve great ideals.

* PHP 7 - We choose the lowest version of 7.1.3, because php7 has a unique advantage over the earlier version.
* Leevel - We provides an optional c extension to takeover core components of the framework,such as IOC, log, cache.
* Redis - QueryPHP encapsulation a cache component, including files, redis, memcache and so on, so as to reduce the pressure of database.

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

## Official Documentation

Documentation for the framework can be found on the [QueryPHP website](http://www.queryphp.com).

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

## License

The QueryPHP framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
