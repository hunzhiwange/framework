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

namespace Tests\Docs;

/**
 * 如何成为 QueryPHP 开发者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.30
 *
 * @version 1.0
 * @api(
 *     title="如何成为 QueryPHP 开发者",
 *     path="developer/README",
 *     description="QueryPHP 非常欢迎各位给我们共同的伟大的作品添砖加瓦，实现为 PHP 社区提供一个好框架的美好愿景。
 *
 *  * 文档开发.基于单元测试实现的自动化文档 [当前文档开发](https://github.com/hunzhiwange/framework/projects/2)
 *  * 计划功能.开发 [当前计划功能](https://github.com/hunzhiwange/framework/projects/6)
 *  * 技术债务.清偿 [当前技术债务](https://github.com/hunzhiwange/framework/projects/7)
 *  * 单元测试.尽可能减少 Bug [当前单元测试](https://github.com/hunzhiwange/framework/projects/4)
 *
 * 成为开发者需要加入我们的组织，如有相关意愿请发送邮件至 `小牛哥 <635750556@qq.com>`，我们会联系你的。
 *
 * 成为开发者并没有什么任务负担，一切主要以你的意愿，兴趣才是最重要的。
 *
 * 本篇指南将带你搭建的 QueryPHP 开发框架的开发环境，使得你可以参与 QueryPHP 底层代码、单元测试和文档等开发工作。
 *
 * 这里以笔者的 Mac 为例子说明，其实 Windows 下面还更简单些。
 * ",
 * )
 */
class BecomeAQueryphpDeveloperDoc
{
    /**
     * @api(
     *     title="克隆 hunzhiwange/queryphp 仓库",
     *     description="QueryPHP 框架的开发来自于从克隆主仓库开始，由于国内访问 Github 网速的问题，只需要等待一小段时间。
     *
     * **下载代码**
     *
     * ```
     * $cd /data/codes/test
     * $git clone git@github.com:hunzhiwange/queryphp.git
     * ```
     *
     * **Composer 安装**
     *
     * ```
     * composer install
     * ```
     *
     * > 如果你电脑没有安装 `composer`，那么已经为你下载一个版本。
     *
     * ```
     * sudo chmod 777 ./build/composer
     * ./build/composer install
     * ```
     *
     * 安装过程
     *
     * ```
     * Cloning into 'queryphp'...
     * remote: Enumerating objects: 54, done.
     * remote: Counting objects: 100% (54/54), done.
     * remote: Compressing objects: 100% (39/39), done.
     * remote: Total 17821 (delta 19), reused 36 (delta 14), pack-reused 17767
     * Receiving objects: 100% (17821/17821), 45.12 MiB | 693.00 KiB/s, done.
     * Resolving deltas: 100% (8700/8700), done.
     * ```
     *
     * **测试是否安装成功**
     *
     * 如果可以访问，那么恭喜你第一阶段即安装完毕。
     * ",
     *     note="",
     *     lang="shell",
     * )
     */
    public function doc1()
    {
        // php leevel server <Visite http://127.0.0.1:9527/>
    }

    /**
     * @api(
     *     title="标准后台 API 端",
     *     description="首先我们需要创建一个数据库来运行我们的后台，让我们对 QueryPHP 有一个直观的感受，同时方便后期开发调试等。
     *
     * **首先创建一个数据库**
     *
     * 可以用 Navicat For Mysql 创建一个数据库 `queryphp_development_db`.
     *
     * ```
     * CREATE DATABASE IF NOT EXISTS queryphp_development_db DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
     * ```
     *
     * **修改 .env**
     *
     * ```
     * ...
     * // Database
     * DATABASE_DRIVER = mysql
     * DATABASE_HOST = 127.0.0.1
     * DATABASE_PORT = 3306
     * DATABASE_NAME = queryphp_development_db
     * DATABASE_USER = root
     * DATABASE_PASSWORD =
     * ...
     *
     * 修改为
     *
     * ...
     * // Database
     * DATABASE_DRIVER = mysql
     * DATABASE_HOST = 127.0.0.1
     * DATABASE_PORT = 3306
     * DATABASE_NAME = queryphp_development_db
     * DATABASE_USER = root
     * DATABASE_PASSWORD = 123456
     * ...
     * ```
     *
     * **执行数据库迁移命令**
     *
     * ```
     * php leevel migrate:migrate
     * ```
     *
     * 安装过程
     *
     * ```
     * using config file ./phinx.php
     * using config parser php
     * using migration paths
     * - /data/codes/test/queryphp/database/migrations
     * using seed paths
     * - /data/codes/test/queryphp/database/seeds
     * warning no environment specified, defaulting to: development
     * using adapter mysql
     * using database queryphp_development_db
     *
     * == 20181109060739 App: migrating
     * == 20181109060739 App: migrated 0.0155s
     *
     * == 20181112023649 Role: migrating
     * == 20181112023649 Role: migrated 0.0160s
     *
     * == 20181112024140 User: migrating
     * == 20181112024140 User: migrated 0.0166s
     *
     * == 20181112024211 Permission: migrating
     * == 20181112024211 Permission: migrated 0.0225s
     *
     * == 20181112024241 UserRole: migrating
     * == 20181112024241 UserRole: migrated 0.0155s
     *
     * == 20181112024302 RolePermission: migrating
     * == 20181112024302 RolePermission: migrated 0.0206s
     *
     * == 20181112024416 Resource: migrating
     * == 20181112024416 Resource: migrated 0.0328s
     *
     * == 20181112024450 PermissionResource: migrating
     * == 20181112024450 PermissionResource: migrated 0.0305s
     *
     * == 20181203130724 Option: migrating
     * == 20181203130724 Option: migrated 0.0170s
     *
     * == 20181203144731 Test: migrating
     * == 20181203144731 Test: migrated 0.0133s
     *
     * All Done. Took 0.2273s
     * ```
     *
     * **测试数据库是否正常**
     *
     * ```
     * php leevel server <http://127.0.0.1:9527/api/entity>
     * ```
     *
     * 结果
     *
     * ",
     *     note="",
     *     lang="html",
     * )
     */
    public function doc2()
    {
        /*
        {
            count: 4,
            :trace: {
                ...
            }
        }
        */
    }

    /**
     * @api(
     *     title="标准后台前端端",
     *     description="后台 API 搭建好了，我们开始搭建前端了，前端基于 `Vue-cli 3` 和 `IView`，首先需要安装 `node` 才能够跑起来。
     *
     * 对于开发 QueryPHP 来说，你不需要会 `Vue` 或者 `JavaScript`，所以请放心不要有心里负担。
     *
     * **安装前端**
     *
     * 第一步安装前端,细节信息可以在 frontend/README.md 查看.
     *
     * ```
     * cd frontend
     * npm install -g cnpm --registry=https://registry.npm.taobao.org // Just once
     * cnpm install
     * ```
     *
     * 安装过程
     *
     * ```
     * ✔ All packages installed (1264 packages installed from npm registry, used 14s(network 13s), speed 221.08kB/s, json 1086(2.23MB), tarball 501.92kB)
     * ```
     *
     * **运行前端**
     *
     * 接着访问这个登陆地址.
     *
     * ```
     * npm run serve # npm run dev <http://127.0.0.1:9528/#/login>
     * ```
     *
     * 输入登陆用户名和密码,这个时候 QueryPHP 不再是一个冰冷的代码，而是有一个干净的带有基础权限系统的后台。
     *
     * ",
     *     note="",
     *     lang="html",
     * )
     */
    public function doc3()
    {
        /*
        user: admin
        password: 123456
        */
    }

    /**
     * @api(
     *     title="运行测试用例",
     *     description="QueryPHP 推崇通过编写测试用例来让代码变得可维护，所以这里需要本地开发跑通测试用例。
     * **首先创建一个数据库**
     *
     * 可以用 Navicat For Mysql 创建一个数据库 `queryphp_development_test`.
     *
     * ```
     * CREATE DATABASE IF NOT EXISTS queryphp_development_test DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
     * ```
     *
     * **修改 .env**
     *
     * ```
     * ...
     * // Database
     * DATABASE_DRIVER = mysql
     * DATABASE_HOST = 127.0.0.1
     * DATABASE_PORT = 3306
     * DATABASE_NAME = test
     * DATABASE_USER = root
     * DATABASE_PASSWORD =
     * ...
     *
     * 修改为
     *
     * ...
     * // Database
     * DATABASE_DRIVER = mysql
     * DATABASE_HOST = 127.0.0.1
     * DATABASE_PORT = 3306
     * DATABASE_NAME = queryphp_development_test
     * DATABASE_USER = root
     * DATABASE_PASSWORD = 123456
     * ...
     * ```
     *
     * **执行数据库迁移命令**
     *
     * ```
     * php leevel migrate:migrate -e testing
     * ```
     *
     * 安装过程
     *
     * ```
     * using config file ./phinx.php
     * using config parser php
     * using migration paths
     * - /data/codes/test/queryphp/database/migrations
     * using seed paths
     * - /data/codes/test/queryphp/database/seeds
     * using environment testing
     * using adapter mysql
     * using database queryphp_development_test
     *
     * == 20181109060739 App: migrating
     * == 20181109060739 App: migrated 0.0155s
     *
     * == 20181112023649 Role: migrating
     * == 20181112023649 Role: migrated 0.0160s
     *
     * == 20181112024140 User: migrating
     * == 20181112024140 User: migrated 0.0166s
     *
     * == 20181112024211 Permission: migrating
     * == 20181112024211 Permission: migrated 0.0225s
     *
     * == 20181112024241 UserRole: migrating
     * == 20181112024241 UserRole: migrated 0.0155s
     *
     * == 20181112024302 RolePermission: migrating
     * == 20181112024302 RolePermission: migrated 0.0206s
     *
     * == 20181112024416 Resource: migrating
     * == 20181112024416 Resource: migrated 0.0328s
     *
     * == 20181112024450 PermissionResource: migrating
     * == 20181112024450 PermissionResource: migrated 0.0305s
     *
     * == 20181203130724 Option: migrating
     * == 20181203130724 Option: migrated 0.0170s
     *
     * == 20181203144731 Test: migrating
     * == 20181203144731 Test: migrated 0.0133s
     *
     * All Done. Took 0.2273s
     * ```
     *
     * **运行测试用例**
     *
     * ```
     * php ./build/phpunit
     * ```
     *
     * 结果
     *
     * > 注意随着系统演进，测试用例会增加，输出结果就有所不同。
     *
     * ",
     *     note="",
     *     lang="html",
     * )
     */
    public function doc4()
    {
        /*
        PHPUnit Pretty Result Printer 0.26.2 by Codedungeon and contributors.
        PHPUnit 8.1.3 by Sebastian Bergmann and contributors.


        ==> Tests\Admin\Service\Resource\IndexTest       ✓  ✓
        ==> Tests\Example\ExampleTest                    ✓
        ==> Tests\Example\PHPUnitTest                    ✓
        ==> Common\Domain\Service\Search\IndexTest       ✓  ✓  ✓  ✓  ✓  ✓
        ==> Common\Infra\Helper\ArrayToFormTest          ✓  ✓  ✓  ✓  ✓  ✓
        ==> Common\Infra\Support\WorkflowServiceTest     ✓  ✓  ✓  ✓  ✓
        ==> Common\Infra\Support\WorkflowTest            ✓  ✓  ✓  ✓  ✓

        Time: 391 ms, Memory: 18.00 MB

        OK (26 tests, 43 assertions)
        */
    }

    /**
     * @api(
     *     title="统一团队代码风格",
     *     description="风格统一对保证我们系统一致性非常重要，我们做到开箱即用，支持 PHP 和 JavaScript。
     *
     * **使用 Git 钩子**
     *
     * ```
     * cp ./build/pre-commit.sh ./.git/hooks/pre-commit
     * chmod 777 ./.git/hooks/pre-commit
     * ```
     *
     * **测试自动化格式**
     *
     * `common/Test.php`
     *
     * ``` php
     * <?php
     *
     * declare(strict_types=1);
     *
     * namespace Common;
     *
     * class Test{
     *     public function demo($a=1, $b=4){
     *         echo 1;
     *     }
     * }
     * ```
     *
     * `frontend/src/hello.js`
     *
     * ```
     * function hello(a,b) {
     *     var c
     *         if(a>b) {
     *             c=a
     *         } else {
     *             c=b
     *         }
     *     console.log(c)
     * }
     * ```
     *
     * **Git 提交测试格式化**
     *
     * ```
     * git add .
     * git commit -m '测试格式化'
     * ```
     *
     * 运行过程
     * ",
     *     note="",
     *     lang="shell",
     * )
     */
    public function doc5()
    {
        /*
        Checking PHP Lint...
        No syntax errors detected in common/Test.php
        Running Code Sniffer...
        Loaded config default from ".php_cs.dist".
        Paths from configuration file have been overridden by paths provided as command arguments.
        1) common/Test.php Fixed all files in 0.009 seconds, 12.000 MB memory used
        The file has been automatically formatted.
        [13:04:00] Working directory changed to /data/codes/test/queryphp/frontend
        [13:04:00] Using gulpfile /data/codes/test/queryphp/frontend/gulpfile.js
        [13:04:00] Starting 'iview'...
        [13:04:00] Finished 'iview' after 413 μs
        frontend/src/hello.js 53ms
        [master 681d7e29] 测试格式化
        3 files changed, 32 insertions(+)
        mode change 100644 => 100755 build/composer
        create mode 100644 common/Test.php
        create mode 100644 frontend/src/hello.js
        */
    }

    /**
     * @api(
     *     title="格式化后的 PHP",
     *     description="代码干净漂亮了不少，不是吗。",
     *     level="###",
     * )
     */
    public function doc5_1()
    {
        /*
        <?php

        declare(strict_types=1);

        /*
         * This file is part of the your app package.
         *
         * The PHP Application For Code Poem For You.
         * (c) 2018-2099 http://yourdomian.com All rights reserved.
         *
         * For the full copyright and license information, please view the LICENSE
         * file that was distributed with this source code.
         *\/

        namespace Common;

        class Test
        {
            public function demo($a = 1, $b = 4)
            {
                echo 1;
            }
        }
        */
    }

    /**
     * @api(
     *     title="格式化后的 JavaScript",
     *     description="代码干净漂亮了不少，不是吗。",
     *     level="###",
     *     lang="javascript",
     * )
     */
    public function doc5_2()
    {
        /*
        function hello(a, b) {
            var c
            if (a > b) {
                c = a
            } else {
                c = b
            }
            console.log(c)
        }
        */
    }

    /**
     * @api(
     *     title="回滚测试提交",
     *     description="这些测试代码不需要提交到 Git 库，你可以回滚掉刚才测试的这些代码。",
     *     level="###",
     *     lang="shell",
     * )
     */
    public function doc5_3()
    {
        /*
        git log
        git reset --hard 931f283b0b8847e4a3f2ad86efb3c07cd7974c3b // 或者 git revert xxx
        HEAD is now at 931f283b Merge branch 'dev'
        */
    }

    /**
     * @api(
     *     title="克隆 hunzhiwange/framework 仓库将框架替换为开发版本",
     *     description="应用层框架全部搭建完毕，接下来我们将框架层代码替换为开发阶段的代码来进行日常框架迭代。
     *
     * **删除框架层**
     *
     * ```
     * rm -rf ./vendor/hunzhiwange/framework
     * ```
     *
     * **克隆框架层开发库**
     *
     * ```
     * $cd /data/codes/test
     * $git clone git@github.com:hunzhiwange/framework.git ./vendor/hunzhiwange/framework
     * cd ./vendor/hunzhiwange/framework
     * ```
     *
     * **Composer 安装**
     *
     * ```
     * composer install
     * ```
     *
     * > 如果你电脑没有安装 `composer`，那么已经为你下载一个版本。
     *
     * ```
     * sudo chmod 777 ./build/composer
     * ./build/composer install
     * ```
     *
     * 安装过程
     *
     * ```
     * Cloning into './vendor/hunzhiwange/framework'...
     * remote: Enumerating objects: 382, done.
     * remote: Counting objects: 100% (382/382), done.
     * remote: Compressing objects: 100% (218/218), done.
     * remote: Total 39304 (delta 196), reused 262 (delta 125), pack-reused 38922
     * Receiving objects: 100% (39304/39304), 14.49 MiB | 12.00 KiB/s, done.
     * Resolving deltas: 100% (27594/27594), done.
     * ```
     *
     * **测试是否安装成功**
     *
     * 从新访问首页，如果可以访问，那么恭喜你第一阶段即安装完毕。
     * ",
     *     note="",
     *     lang="shell",
     * )
     */
    public function doc6()
    {
        // php leevel server <Visite http://127.0.0.1:9527/>
    }

    /**
     * @api(
     *     title="运行框架核心测试用例",
     *     description="QueryPHP 底层框架拥有 3000 多例测试用例，这些测试用例需要被维护，所以这里需要本地开发跑通测试用例。
     * **首先创建一个数据库**
     *
     * 可以用 Navicat For Mysql 创建一个数据库 `test`.
     *
     * ```
     * CREATE DATABASE IF NOT EXISTS test DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
     * ```
     *
     * **复制一份配置文件并修改**
     *
     * ```
     * cp ./tests/config.php ./tests/config.local.php
     * ```
     *
     * 修改为
     *
     * ```php
     * <?php
     *
     * $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL'] = [
     *     'HOST'     => '127.0.0.1',
     *     'PORT'     => 3306,
     *     'NAME'     => 'test',
     *     'USER'     => 'root',
     *     'PASSWORD' => '123456',
     * ];
     *
     * $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS'] = [
     *     'HOST'     => '127.0.0.1',
     *     'PORT'     => 6380,
     *     'PASSWORD' => '123456',
     * ];
     *
     * $GLOBALS['LEEVEL_ENV']['SESSION']['REDIS'] = [
     *     'HOST'     => '127.0.0.1',
     *     'PORT'     => 6380,
     *     'PASSWORD' => '123456',
     * ];
     * ```
     *
     * **执行数据库迁移命令**
     *
     * ```
     * php vendor/bin/phinx migrate
     * ```
     *
     * 安装过程
     *
     * ```
     * Phinx by CakePHP - https://phinx.org. 0.9.2
     *
     * using config file ./phinx.php
     * using config parser php
     * using migration paths
     *  - /data/codes/test/queryphp/vendor/hunzhiwange/framework/tests/assert/database/migrations
     * using seed paths
     *  - /data/codes/test/queryphp/vendor/hunzhiwange/framework/tests/assert/database/seeds
     * warning no environment specified, defaulting to: development
     * using adapter mysql
     * using database test
     *
     *  == 20181010111946 User: migrating
     *  == 20181010111946 User: migrated 0.0076s
     *
     *  == 20181011111926 Post: migrating
     *  == 20181011111926 Post: migrated 0.0101s
     *
     *  == 20181011111937 Comment: migrating
     *  == 20181011111937 Comment: migrated 0.0106s
     *
     *  == 20181011151247 PostContent: migrating
     *  == 20181011151247 PostContent: migrated 0.0087s
     *
     *  == 20181011160957 Role: migrating
     *  == 20181011160957 Role: migrated 0.0078s
     *
     *  == 20181011161035 UserRole: migrating
     *  == 20181011161035 UserRole: migrated 0.0100s
     *
     *  == 20181031094608 CompositeId: migrating
     *  == 20181031094608 CompositeId: migrated 0.0094s
     *
     *  == 20181107044153 GuestBook: migrating
     *  == 20181107044153 GuestBook: migrated 0.0086s
     *
     *  == 20190424055915 TestUnique: migrating
     *  == 20190424055915 TestUnique: migrated 0.0133s
     *
     * All Done. Took 0.1179s
     * ```
     *
     * **运行测试用例**
     *
     * ```
     * php ./build/phpunit
     * ```
     *
     * 结果
     *
     * > 注意随着系统演进，测试用例会增加，输出结果就有所不同。
     *
     * ",
     *     note="",
     *     lang="html",
     * )
     */
    public function doc7()
    {
        /*
        PHPUnit 8.1.3 by Sebastian Bergmann and contributors.


        ==> Tests\Auth\HashTest                          ✓  ✓
        ==> Tests\Auth\ManagerTest                       ✓  ✓  ✓
        ==> Tests\Auth\Middleware\AuthTest               ✓  ✓
        ==> Tests\Auth\Provider\RegisterTest             ✓
        ==> Tests\Auth\SessionTest                       ✓  ✓

        ...

        ==> Tests\View\Compiler\CompilerWhileTest        ✓  ✓
        ==> Tests\View\HtmlTest                          ✓  ✓  ✓  ✓  ✓  ✓  ✓  ✓  ✓
        ==> Tests\View\ManagerTest                       ✓
        ==> Tests\View\PhpuiTest                         ✓  ✓  ✓  ✓  ✓  ✓  ✓  ✓
        ==> Tests\View\Provider\RegisterTest             ✓

        Time: 19.51 seconds, Memory: 93.19 MB

        Tests: 2978, Assertions: 10031, Failures: 0, Skipped: 6.
        */
    }

    /**
     * @api(
     *     title="统一框架核心团队代码风格",
     *     description="风格统一对保证我们系统一致性非常重要，我们做到开箱即用，核心库只包含 PHP 文件。
     *
     * **使用 Git 钩子**
     *
     * ```
     * cp ./build/pre-commit.sh ./.git/hooks/pre-commit
     * chmod 777 ./.git/hooks/pre-commit
     * ```
     *
     * **测试自动化格式**
     *
     * `tests/Name.php`
     *
     * ``` php
     * <?php
     *
     * declare(strict_types=1);
     *
     * namespace Test;
     *
     * class Name{
     *     public function demo($a=1, $b=4){
     *         echo 1;
     *     }
     * }
     * ```
     *
     * **Git 提交测试格式化**
     *
     * ```
     * git add .
     * git commit -m '测试格式化'
     * ```
     *
     * 运行过程
     * ",
     *     note="",
     *     lang="shell",
     * )
     */
    public function doc8()
    {
        /*
        Checking PHP Lint...
        No syntax errors detected in tests/Name.php
        Running Code Sniffer...
        Loaded config default from ".php_cs.dist".
        Paths from configuration file have been overridden by paths provided as command arguments.
        1) tests/Name.php Fixed all files in 0.009 seconds, 12.000 MB memory used
        The file has been automatically formatted.
        [master 20f2f845] 测试格式化
        2 files changed, 29 insertions(+)
        mode change 100644 => 100755 build/composer
        create mode 100644 tests/Name.php
        */
    }

    /**
     * @api(
     *     title="格式化后的 PHP",
     *     description="代码干净漂亮了不少，不是吗。
     *
     * 测试代码回滚请见上面的方法，谢谢。
     * ",
     *     level="###",
     * )
     */
    public function doc8_1()
    {
        /*
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
        *\/

        namespace Test;

        class Name
        {
            public function demo($a = 1, $b = 4)
            {
                echo 1;
            }
        }
        */
    }

    /**
     * @api(
     *     title="克隆 hunzhiwange/queryphp.com 仓库实现自动化文档搭建",
     *     description="QueryPHP 底层的文档基于单元测试加备注的方式来实现的，通过命名工具全部采用自动化生成 Markdown，这大幅度简化了文档的编写工作，同时保证了文档实时性。
     *
     * **官方文档采用 VuePress 读取 Markdown 来实现的**
     *
     * ```
     * $cd /data/codes/test
     * $git clone git@github.com:hunzhiwange/queryphp.com.git
     * ```
     *
     * **修改文档工具生成 Markdown 的路径**
     *
     * ```
     * $cd /data/codes/test/queryphp
     * $vim .env
     *
     * # 修改路径
     * FRAMEWORK_DOC_OUTPUTDIR = \"/data/codes/test/queryphp.com/docs/docs/\"
     * ```
     *
     * **生成文档**
     *
     * ```
     * $cd /data/codes/test/queryphp
     * $php leevel make:docwithin tests
     * ```
     *
     * 运行过程
     *
     * ```
     * Class Tests\Encryption\EncryptionTest was generate succeed.
     * Class Tests\Encryption\SafeTest was generate succeed.
     * Class Tests\Database\ManagerTest was generate succeed.
     * Class Tests\Database\Ddd\UnitOfWorkTest was generate succeed.
     * Class Tests\Database\Ddd\Create\CreateTest was generate succeed.
     * Class Tests\Database\Query\AggregateTest was generate succeed.
     * Class Tests\Validate\AssertTest was generate succeed.
     * Class Tests\Di\ContainerTest was generate succeed.
     * Class Tests\Docs\BecomeAQueryphpDeveloperDoc was generate succeed.
     * Class Tests\Support\FnTest was generate succeed.
     * Class Tests\Support\StrTest was generate succeed.
     * Class Tests\Support\ArrTest was generate succeed.
     * Class Tests\View\SummaryDoc was generate succeed.
     * Class Tests\View\Compiler\CompilerAssignTest was generate succeed.
     * Class Tests\View\Compiler\CompilerPhpTest was generate succeed.
     * Class Tests\View\Compiler\CompilerBreakTest was generate succeed.
     * Class Tests\View\Compiler\CompilerIncludeTest was generate succeed.
     * Class Tests\View\Compiler\CompilerTagselfTest was generate succeed.
     * Class Tests\View\Compiler\CompilerWhileTest was generate succeed.
     * Class Tests\View\Compiler\CompilerCssTest was generate succeed.
     * Class Tests\View\Compiler\CompilerForTest was generate succeed.
     * Class Tests\View\Compiler\CompilerVarTest was generate succeed.
     * Class Tests\View\Compiler\CompilerListTest was generate succeed.
     * Class Tests\Debug\DebugTest was generate succeed.
     * A total of 24 files generate succeed.
     * ```
     *
     * **修改文档菜单**
     *
     * ```
     * $vim docs/.vuepress/config.js
     * ```
     *
     * **运行本地文档网站**
     *
     * 访问地址 `localhost:8088` 即可。
     *
     * ```
     * $npm install -g yarn
     * $yarn add -D vuepress # or npm install -D vuepress
     * $yarn run dev # or npx vuepress dev docs
     * ```
     * ",
     * )
     */
    public function doc9()
    {
    }

    /**
     * @api(
     *     title="结尾",
     *     description="到这里为止，我们本地开发环境已经全部搭建完毕，可以愉快地开发了。",
     *     note="值得注意的是，我们通常在 `dev` 分支开发，开发完毕后 `merge` 到 `master` 分支完成开发。",
     * )
     */
    public function doc10()
    {
    }
}
