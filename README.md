<p align="center">
  <a href="https://queryphp.com">
    <img src="./leevel.png" />
  </a>
</p>

<p align="center">
  <a href='https://packagist.org/packages/hunzhiwange/framework'><img src='http://img.shields.io/packagist/v/hunzhiwange/framework.svg' alt='Latest Stable Version' /></a> 
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-%3E%3D%208.0.0-8892BF.svg" alt="Minimum PHP Version"></a>
  <a href="https://www.swoole.com/"><img src="https://img.shields.io/badge/Swoole-%3E%3D%204.5.9-008de0.svg" alt="Minimum Swoole Version"></a>
  <a href="https://github.com/spiral/roadrunner"><img alt="RoadRunner Version" src="https://img.shields.io/badge/RoadRunner-%3E=1.8.2-brightgreen.svg" /></a>
  <a href="http://opensource.org/licenses/MIT">
    <img alt="QueryPHP License" src="https://poser.pugx.org/hunzhiwange/framework/license.svg" /></a>
  <br />
  <a href="https://github.styleci.io/repos/91284136"><img src="https://github.styleci.io/repos/91284136/shield?branch=master" alt="StyleCI"></a>
  <a href='https://www.queryphp.com/docs/'><img src='https://img.shields.io/badge/docs-passing-green.svg?maxAge=2592000' alt='QueryPHP Doc' /></a>
  <a href="https://github.com/hunzhiwange/framework/actions">
    <img alt="Build Status" src="https://github.com/hunzhiwange/framework/workflows/tests/badge.svg" /></a>
  <a href="https://codecov.io/gh/hunzhiwange/framework">
    <img src="https://codecov.io/gh/hunzhiwange/framework/branch/master/graph/badge.svg?token=GMWV1X9F7T"/>
  </a>
</p>

<p align="center">
  <a href="https://github.com/hunzhiwange/queryphp"><b>The QueryPHP Application</b></a>
  <br />
  <a href="https://github.com/hunzhiwange/queryphp/actions">
    <img alt="Build Status" src="https://github.com/hunzhiwange/queryphp/workflows/tests/badge.svg" /></a>
  <a href="https://codecov.io/gh/hunzhiwange/queryphp">
    <img src="https://codecov.io/gh/hunzhiwange/queryphp/branch/master/graph/badge.svg?token=D4WV1IC2R3"/>
  </a>
</p>

<p align="center">
    <a href="./README.md">English</a> | <a href="./README-zh-CN.md">中文</a>
</p>

# The QueryPHP Framework

> This is the core framework code of QueryPHP.

QueryPHP is a modern, high performance PHP progressive framework, to provide a stable and reliable high-quality enterprise level framework as its historical mission. **<span style="color:#e82e7d;">USE LEEVEL DO BETTER</span>**

*The PHP Framework For Code Poem As Free As Wind*

* Site: <https://www.queryphp.com/>
* China Mirror Site: <https://queryphp.gitee.io/>
* Documentation: <https://www.queryphp.com/docs/>

![](doyouhaobaby.png)

QueryPHP was based on the [DoYouHaoBaby](https://github.com/hunzhiwange/dyhb.blog-x/tree/master/Upload/DoYouHaoBaby) framework which released 0.0.1 version at 2010.10.03,the latest version of DoYouHaoBaby is renamed as [QeePHP](https://github.com/hunzhiwange/windsforce/tree/master/upload/System/include/QeePHP).

## The core packages

 * QueryPHP On Github: <https://github.com/hunzhiwange/queryphp/>
 * QueryPHP On Gitee: <https://gitee.com/dyhb/queryphp/>
 * Framework On Github: <https://github.com/hunzhiwange/framework/>
 * Framework On Gitee: <https://gitee.com/dyhb/framework/>
 * Packages: <https://github.com/leevels/>
 * Packages From Hunzhiwange: <https://packagist.org/packages/hunzhiwange/>
 * Packages From Leevel: <https://packagist.org/packages/leevel/>

## Why is QueryPHP?

We think the performance of PHP applications is very important and the development of pleasure also needs to be considered, and that's why we have developed the QueryPHP framework to achieve great ideals.

* PHP 8 - We choose the lowest version of 8.0.0, because php 8 has a unique advantage over the earlier version.
* Swoole - Enable PHP developers to write high-performance, scalable, concurrent TCP, UDP, Unix socket, HTTP, Websocket services in PHP programming language.
* Redis - QueryPHP encapsulation a cache component, including files, redis and so on, so as to reduce the pressure of database.
* RoadRunner - RoadRunner is an open source high-performance PHP application server, load balancer and process manager. It supports running as a service with the ability to extend its functionality on a per-project basis.

```
Wow! Cool! Query Yet Simple!
```

## How to install

```
composer require hunzhiwange/framework
```

## The components that make up the QueryPHP framework

Components for the framework can be found on the [Github website](https://github.com/leevels) and [Packagist website](https://packagist.org/packages/leevel/).

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
+ $php ./build/phpunit tests
+ $composer test
+ $composer test-coverage
```

## Framework Command

```
$cd /data/codes/queryphp
$php leevel make:docwithin vendor/hunzhiwange/framework/tests
$php leevel make:idehelper Leevel\\Cache\\ICache
$php leevel make:idehelper:function vendor/hunzhiwange/framework/src/Leevel/Support/Arr
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
+ $composer php-cs-fixer
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

```diff
- $php ./build/phpstan analyse
+ $composer phpstan
```

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
 * Swoft: <https://github.com/swoft-cloud/>

## License

The QueryPHP framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
