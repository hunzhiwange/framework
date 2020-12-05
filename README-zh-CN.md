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

# QueryPHP 框架 

> 这是 QueryPHP 的核心框架代码。

QueryPHP 是一款现代化的高性能 PHP 渐进式框架, 以提供稳定可靠的高品质企业级框架为历史使命。**<span style="color:#e82e7d;">USE LEEVEL DO BETTER</span>**

## 运行测试文件

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

## 框架命令

```
$cd /data/codes/queryphp
$php leevel make:docwithin vendor/hunzhiwange/framework/tests
$php leevel make:idehelper Leevel\\Cache\\ICache
$php leevel make:idehelper:function vendor/hunzhiwange/framework/src/Leevel/Support/Arr
```

## 统一团队代码风格

### 安装 PHP 代码格式化工具

<https://github.com/friendsofphp/php-cs-fixer>

不需要安装即可使用，我们已经下载了版本。

### 基本使用

```diff
$cd /data/codes/queryphp/vendor/hunzhiwange/framework
- $php-cs-fixer fix --config=.php_cs.dist
+ $php ./build/php-cs-fixer fix --config=.php_cs.dist
+ $composer php-cs-fixer
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

## PHPStan 静态检查

```diff
- $php ./build/phpstan analyse
+ $composer phpstan
```

## 版权协议

QueryPHP 是一个基于 [MIT license](http://opensource.org/licenses/MIT) 授权许可协议的开源软件.
