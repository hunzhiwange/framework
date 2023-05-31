<?php

declare(strict_types=1);

namespace Tests\Docs\Started;

/**
 * @api(
 *     title="Directory",
 *     zh-CN:title="目录结构",
 *     zh-TW:title="目錄結構",
 *     path="started/directory",
 *     zh-CN:description="QueryPHP 遵循 **“约定优于配置”** 的原则，主张通过领域驱动设计来构建更可靠的软件。",
 * )
 */
class DirectoryDoc
{
    /**
     * @api(
     *     zh-CN:title="基本结构",
     *     zh-CN:description="
     * 下面是整个应用基本目录结构，系统结构可以自由定义。
     *
     * ```
     * .
     * ├── assets 资源目录
     * │── app （默认应用）
     * │   ├── Domain 领域模型层（Domain Model）
     * │   │   └── Entity 实体 (Entity)
     * │   │   └── Event 事件（Event）
     * │   │   └── Listener 监听器（Listener）
     * │   │── Infra 基础设施层（Infrastructure）
     * │   │   └── Provider 服务提供者 (Provider)
     * │   │   └── Repository 仓储 (Repository)
     * │-- option 配置目录
     * │-- storage 缓存目录
     * │-- tests 单元测试目录
     * │-- vendor Composer 第三方库目录
     * │-- www Web 入口目录
     * │-- .env 环境变量
     * │-- .env.phpunit 单元测试环境变量
     * │-- .php-cs-fixer.dist.php 统一团队风格配置
     * │-- composer.json Composer 配置
     * │-- leevel 命令行工具集 php leevel
     * │-- phinx.php 数据库迁移配置
     * │-- phpunit.xml.dist PHPUnit 配置
     * │-- phpunit.xml.coverage PHPUnit 生成 HTML 覆盖率配置，需要安装 xdebug
     * └── ...
     * ```
     *
     * ::: warning 注意
     * 请留意目录名的大写。
     * :::
     *
     * ::: danger
     * 在 Mac 或者 Linux 环境下面，注意需要设置 `storage` 目录权限为 0777。
     * :::
     * ",
     *     zh-CN:note="",
     * )
     */
    public function doc1(): void
    {
    }

    /**
     * @api(
     *     zh-CN:title="多应用",
     *     zh-CN:description="
     * QueryPHP 设计了一个很简单的规则来访问多应用，只需要加 `app:admin` 即可，该目录会自动识别为应用，例如:
     *
     * ```
     * http://127.0.0.1:9527/ 默认应用首页
     * http://127.0.0.1:9527/app:admin/ Admin 应用首页
     * http://127.0.0.1:9527/api/show 默认应用 API 控制器 show 方法
     * http://127.0.0.1:9527/app:admin/api/show Admin 应用 API 控制器 show 方法
     * ```
     *
     * ::: warning 注意
     * 1. 默认应用不需要加 `app:`
     * 2. 默认应用的 `app` 目录可以省略
     * 3. 可以通过 App\Infra\Provider\Router 中的配置 $withDefaultAppNamespace（应用是否带有默认应用命名空间）
     *    来控制路由是否带有默认应用命名空间
     * :::
     * ",
     *     zh-CN:note="",
     * )
     */
    public function doc2(): void
    {
    }
}
