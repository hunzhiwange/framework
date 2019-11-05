<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     *     title="基本结构",
     *     description="
     * ::: vue
     * .
     * ├── apis Swagger API 目录
     * ├── application
     * │   ├── admin （后台应用）_(**通用后台 API 接口**)_
     * │   ├── app （默认应用）
     * │   │   ├── `App` _(**应用层（Application）**)_
     * │   │   ├── `Domain` _(**领域模型层（Domain Model）**)_
     * │   │   │   └── Entity _(**实体 (Entity)**)_
     * │   │   │   └── Event _(**事件（Event）**)_
     * │   │   │   └── Listener _(**监听器（Listener）**)_
     * │   │   ├── `Infra` _(**基础设施层（Infrastructure）**)_
     * │   │   │   └── Provider _(**服务提供者 (Provider)**)_
     * │   │   │   └── Repository _(**仓储 (Repository)**)_
     * │   │   ├── `ui` _(**用户界面 User Interface（表现层 Presentation）**)_
     * │-- build 一些脚本
     * │-- common 公共组件 (结构类 application/app)
     * │-- database 数据库迁移
     * │   ├── migrations _(**数据库迁移**)_
     * │   ├── seeds _(**填充数据**)_
     * │-- frontend 基于 Vue2+IView 前端通用权限管理系统
     * │-- i18n 语言包
     * │   ├── zh-CN _(**中文**)_
     * │   ├── zh-TW _(**台湾繁体**)_
     * │   ├── en-US _(**美国英语**)_
     * │-- option 配置目录
     * │-- public 静态资源目录，比如图片、CSS
     * │-- runtime 运行缓存目录
     * │-- storage 文件上传目录
     * │-- tests 单元测试目录
     * │-- themes 视图文件目录
     * │-- vendor Composer 第三方库目录
     * │-- www Web 入口目录
     * │-- ...
     * │-- .env 环境变量
     * │-- .env.phpunit 单元测试环境变量
     * │-- .php_cs.dist 统一团队风格配置
     * │-- .travis.yml Travis 持续集成配置
     * │-- composer.json Composer 配置
     * │-- leevel 命令行工具集 php leevel
     * │-- package.json 前端包
     * │-- phinx.php 数据库迁移配置
     * │-- phpunit.xml.dist PHPUnit 配置
     * │-- phpunit.xml.dist PHPUnit 生成 HTML 覆盖率配置，需要安装 xdebug
     * └── ...
     * :::
     *
     * ::: warning 注意
     * 请留意目录名的大写。
     * :::
     *
     * ::: danger
     * 在 Mac 或者 Linux 环境下面，注意需要设置 `runtime`、`bootstrap` 和 `storage` 目录权限为 0777。
     * 当然实际上我们在 Mac 开发环境直接给某个目录授权给用户。
     * ```
     * drwxr-xr-x  62 dyhb  staff   1.9K 11 19 11:14 codes
     * ```
     * :::
     * ",
     *     note="",
     * )
     */
    public function doc1()
    {
    }

    /**
     * @api(
     *     title="多应用",
     *     description="
     * QueryPHP 设计了一个很简单的规则来访问多应用，只需要加 `:` 即可，该目录会自动识别为应用，例如:
     *
     * ```
     * http://127.0.0.1:9527/ 默认应用首页
     * http://127.0.0.1:9527/:admin/ Admin 应用首页
     * http://127.0.0.1:9527/api/show 默认应用 API 控制器 show 方法
     * http://127.0.0.1:9527/:admin/api/show Admin 应用 API 控制器 show 方法
     * ```
     * ",
     *     note="",
     * )
     */
    public function doc2()
    {
    }
}
