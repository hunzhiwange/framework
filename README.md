![](queryphp.png)

<p align="center">
  <a href="https://github.com/hunzhiwange/framework/releases">
    <img alt="Latest Version" src="https://img.shields.io/packagist/vpre/hunzhiwange/framework.svg?style=for-the-badge" /></a>
  <a href="https://travis-ci.org/hunzhiwange/framework">
    <img alt="Build Status" src="https://img.shields.io/travis/hunzhiwange/framework.svg?style=for-the-badge" /></a>
  <a href="https://secure.php.net/">
    <img alt="Php Version" src="https://img.shields.io/packagist/php-v/hunzhiwange/framework.svg?style=for-the-badge" /></a>
  <a href="https://github.com/swoole/swoole-src">
    <img alt="Swoole Version" src="https://img.shields.io/badge/swoole-%3E=2.1.1-brightgreen.svg?style=for-the-badge" /></a>
  <a href="https://github.com/hunzhiwange/framework/blob/master/LICENSE">
    <img alt="QueryPHP License" src="https://img.shields.io/packagist/l/hunzhiwange/framework.svg?style=for-the-badge" /></a>
</p>

# The QueryPHP Framework

QueryPHP is a powerful PHP framework for code poem as free as wind. [Query Yet Simple]

QueryPHP was founded in 2010 and released the first version on 2010.10.03.

QueryPHP was based on the DoYouHaoBaby framework.

## About The Old DoYouHaoBaby Framework

![](doyouhaobaby.png)

<p>DoYouHaoBaby 具备了大量丰富的特性: 包括 MVC、ActiveRecord、国际化语言包、缓存组件、主从数据库、模式扩展、模板引擎、RBAC 权限扩展等等。</p>

<p>DoYouHaoBaby 主要用于 WindsForce 社区（停止维护）、Dyhb-blog-x（停止维护）、114.MS 家居装修网（已挂停止维护）等自主产品的开发。</p>

<p>DoYouHaoBaby 于 2010/10/03 发布 0.0.1 版本，最后版本于 2014/10 发布 3.0 版本，感觉功能已经够自己用了并进入停止开发阶段。</p>

<p align="right">小牛哥 2014.10 @ HTTP://DoYouHaoBaby.NET（官网已挂）</p>

## How to install

```
composer require queryyetsimple/composer
```

## The components that make up the QueryPHP framework

Components for the framework can be found on the [Github website](https://github.com/queryyetsimple) and [Github website](https://packagist.org/packages/queryyetsimple/).

```
composer require queryyetsimple/di
composer require queryyetsimple/cache

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
        
composer install
vendor/bin/phpunit tests
```

## License

The QueryPHP framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
