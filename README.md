<p align="center">
  <a href="https://queryphp.com">
    <img src="./leevel.png" />
  </a>
</p>

<p align="center">
  <a href="https://php.net"><img src="https://img.shields.io/badge/php-%3E%3D%207.4.0-8892BF.svg" alt="Minimum PHP Version"></a>
  <a href="https://www.swoole.com/"><img src="https://img.shields.io/badge/swoole-%3E%3D%204.4.5-008de0.svg" alt="Minimum Swoole Version"></a>
  <a href="https://github.styleci.io/repos/91284136"><img src="https://github.styleci.io/repos/91284136/shield?branch=master" alt="StyleCI"></a>
  <a href='https://www.queryphp.com/docs/'><img src='https://img.shields.io/badge/docs-passing-green.svg?maxAge=2592000' alt='QueryPHP Doc' /></a>
  <a href="https://travis-ci.org/hunzhiwange/framework">
    <img alt="Build Status" src="https://img.shields.io/travis/hunzhiwange/framework.svg" /></a>
  <a href='https://coveralls.io/github/hunzhiwange/framework?branch=master'><img src='https://coveralls.io/repos/github/hunzhiwange/framework/badge.svg?branch=master' alt='Coverage Status' /></a>
  <a href='https://packagist.org/packages/hunzhiwange/framework'><img src='http://img.shields.io/packagist/v/hunzhiwange/framework.svg' alt='Latest Stable Version' /></a> 
  <a href="http://opensource.org/licenses/MIT">
    <img alt="QueryPHP License" src="https://poser.pugx.org/hunzhiwange/framework/license.svg" /></a>
</p>

<p align="center">
    <a href="./README.md">English</a> | <a href="./README-zh-CN.md">中文</a>
</p>

# The QueryPHP Framework

> This is the core framework code of QueryPHP application, starts from this moment with [QueryPHP](https://github.com/hunzhiwange/queryphp).

QueryPHP is a modern, high performance PHP progressive coroutine framework, we still focus on traditional PHP-FPM scenarios,with engineer user experience as its historical mission, let every PHP application have a good framework.

A hundred percent coverage of the unit tests to facing the bug,with our continuous commitment to creating high quality products for level level leevel,with Swoole coroutine to improving business performance,now or in the future step by step. Our vision is **<span style="color:#e82e7d;">USE LEEVEL WITH SWOOLE DO BETTER</span>**, let your business to support more user services.

*The PHP Framework For Code Poem As Free As Wind, Starts From This Moment With QueryPHP.*

* Site: <https://www.queryphp.com/>
* API: <https://api.queryphp.com>
* Document: <https://www.queryphp.com/docs/>

![](doyouhaobaby.png)

QueryPHP was based on the [DoYouHaoBaby](https://raw.githubusercontent.com/hunzhiwange/framework/master/doyouhaobaby-googlecode.jpg) framework which released 0.0.1 version at 2010.10.03.

## The core packages

 * QueryPHP On Github: <https://github.com/hunzhiwange/queryphp/>
 * QueryPHP On Gitee: <https://gitee.com/dyhb/queryphp/>
 * Framework On Github: <https://github.com/hunzhiwange/framework/>
 * Framework On Gitee: <https://gitee.com/dyhb/framework/>
 * Packages: <https://github.com/leevels/>
 * Packages From Hunzhiwange: <https://packagist.org/packages/hunzhiwange/>
 * Packages From Leevel: <https://packagist.org/packages/leevel/>

## Optional Extension

<p>
  <a href="http://pecl.php.net/package/swoole">
    <img alt="Swoole Version" src="https://img.shields.io/badge/swoole-%3E=4.4.5-brightgreen.svg" /></a>
  <a href="https://github.com/spiral/roadrunner">
    <img alt="Roadrunner Version" src="https://img.shields.io/badge/roadrunner-%3E=1.3.5-brightgreen.svg" /></a>
</p>

We think the performance of PHP applications is very important and the development of pleasure also needs to be considered, and that's why we have developed the QueryPHP framework to achieve great ideals.

* PHP 7 - We choose the lowest version of 7.4.0, because php7 has a unique advantage over the earlier version.
* Swoole - Enable PHP developers to write high-performance, scalable, concurrent TCP, UDP, Unix socket, HTTP, Websocket services in PHP programming language.
* Redis - QueryPHP encapsulation a cache component, including files, redis and so on, so as to reduce the pressure of database.
* Roadrunner - RoadRunner is an open source high-performance PHP application server, load balancer and process manager. It supports running as a service with the ability to extend its functionality on a per-project basis.

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

```diff
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
- $php vendor/bin/phpunit tests
+ $php ./build/phpunit tests
```

## Make Doc For Framework

```
$cd /data/codes/queryphp
$php leevel make:docwithin tests
```

## Unified Code Style

### Install PHP Coding Standards Fixer

<https://github.com/friendsofphp/php-cs-fixer>

It can be used without installation,we download a version for you.

### Base use

```diff
$cd /data/codes/queryphp/vendor/hunzhiwange/framework
- $php-cs-fixer fix --config=.php_cs.dist
+ $php ./build/php-cs-fixer fix --config=.php_cs.dist
```

### With Git hooks

Add a pre-commit for it.

```
cp ./build/pre-commit.sh ./.git/hooks/pre-commit
chmod 777 ./.git/hooks/pre-commit
```

Pass hook

```
# git commit -h
# git commit -n -m 'pass hook' #bypass pre-commit and commit-msg hooks
```

## PHPStan 

```
php ./build/phpstan analyse
```

## Travis CI Supported

Let code poem.

## Official Documentation

Documentation for the framework can be found on the [QueryPHP website](http://www.queryphp.com).

## Thanks

Thanks my colleague [John.mao](https://github.com/maosea0125) for your selfless help in the development of this project and and let me have a new understanding, it makes QueryPHP more beautiful.

Thanks for these excellent projects, we have absorbed a lot of excellent design and ideas, standing on the shoulders of giants for innovation.

 * QeePHP: <https://github.com/dualface/qeephp2_x/>
 * Swoole: <https://github.com/swoole/>
 * JeCat: <https://github.com/JeCat/>
 * ThinkPHP: <https://github.com/top-think/>
 * Laravel: <https://github.com/laravel/>
 * Symfony: <https://github.com/symfony/>
 * Doctrine: <https://github.com/doctrine/>
 * Phalcon: <https://github.com/phalcon/>

## License

The QueryPHP framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
