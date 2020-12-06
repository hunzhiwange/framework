<?php

declare(strict_types=1);

namespace Tests\Docs\Started;

/**
 * @api(
 *     title="Namespace",
 *     zh-CN:title="命名空间",
 *     zh-TW:title="命名空間",
 *     path="started/namespace",
 *     zh-CN:description="QueryPHP 采用命名空间的方式来有效地组织项目代码，系统在运行过程中会自动注册一些命名空间，完全依赖 `Composer` 来管理文件自动加载。",
 * )
 */
class NamespaceDoc
{
    /**
     * @api(
     *     zh-CN:title="系统注册命名空间",
     *     zh-CN:description="
     * 为了满足项目开发需求, QueryPHP 在项目运行过程中会注册多个命名空间，命名空间遵循 **PSR-4** 自动加载规范。
     *
     * ### 应用命名空间
     *
     * 一个典型的应用会注册下面几个命令空间，命名空间在 `composer.json` 中定义。
     *
     * ``` json
     * {
     *     "autoload": {
     *         "psr-4": {
     *             "App\\" : "application/app",
     *             "Admin\\" : "application/admin",
     *             "Common\\" : "common"
     *         }
     *     }
     * }
     * ```
     *
     * ### 框架核心命名空间
     *
     * 例外框架核心部分的命名空间为 `Leevel`，命名空间在 `vendor/hunzhiwange/framework/composer.json` 中定义。
     *
     * ``` json
     * {
     *     "autoload": {
     *         "psr-4": {
     *             "Leevel\\": "src\\Leevel"
     *         }
     *     }
     * }
     * ```
     *
     * ### 应用测试命名空间
     *
     * 应用测试的命名空间为 `Tests`，命名空间在 `tests/bootstrap.php` 中定义。
     *
     * ``` php
     * $composer = include $vendorDir.'/autoload.php';
     * $composer->addPsr4('Tests\\', __DIR__);
     * ```
     *
     * ### 框架核心测试命名空间
     *
     * 框架核心测试的命名空间为 `Tests`，命名空间在 `vendor/hunzhiwange/framework/tests/bootstrap.php` 中定义。
     *
     * ``` php
     * $composer = include $vendorDir.'/autoload.php';
     * $composer->addPsr4('Tests\\', __DIR__);
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function doc1(): void
    {
    }

    /**
     * @api(
     *     zh-CN:title="自定义命名空间",
     *     zh-CN:description="
     * 为满足个性化需求，你可以注册自定义的命名空间，命名空间遵循 **PSR-4** 自动加载规范。
     *
     * ``` json
     * {
     *     "autoload": {
     *         "psr-4": {
     *             "Hello\\" : "hello"
     *         }
     *     }
     * }
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function doc2(): void
    {
    }
}
