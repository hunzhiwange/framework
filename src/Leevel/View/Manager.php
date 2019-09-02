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

namespace Leevel\View;

use Leevel\Manager\Manager as Managers;

/**
 * view 入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.01.10
 *
 * @version 1.0
 *
 * @method static display(string $file, array $vars = [], ?string $ext = null, bool $display = true) 加载视图文件.
 * @method static void setParseResolver(\Closure $parseResolver)                                     设置 parser 解析回调.
 * @method static string getCachePath(string $file)                                                  获取编译路径.
 * @method static \Leevel\View\IView setOption(string $name, $value)                                 设置配置.
 * @method static void setVar($name, $value = null)                                                  设置模板变量.
 * @method static getVar(?string $name = null)                                                       获取变量值.
 * @method static void deleteVar(array $name)                                                        删除变量值.
 * @method static void clearVar()                                                                    清空变量值.
 */
class Manager extends Managers
{
    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    protected function normalizeOptionNamespace(): string
    {
        return 'view';
    }

    /**
     * 创建 html 模板驱动.
     *
     * @param array $options
     *
     * @return \Leevel\View\Html
     */
    protected function makeConnectHtml(array $options = []): Html
    {
        $options = $this->normalizeConnectOption('html', $options);
        $options = array_merge(
            $options, $this->viewOptionCommon()
        );
        $container = $this->container;
        $html = new Html($options);

        $html->setParseResolver(function () use ($container) {
            return $container['view.parser'];
        });

        return $html;
    }

    /**
     * 创建 phpui 模板驱动.
     *
     * @param array $options
     *
     * @return \Leevel\View\Phpui
     */
    protected function makeConnectPhpui(array $options = []): Phpui
    {
        $options = $this->normalizeConnectOption('phpui', $options);
        $options = array_merge(
            $options, $this->viewOptionCommon()
        );

        return new Phpui($options);
    }

    /**
     * 视图公共配置.
     *
     * @return array
     */
    protected function viewOptionCommon(): array
    {
        return [
            'theme_path' => $this->container->make('app')->themesPath(),
            'cache_path' => $this->container->make('app')->runtimePath('theme'),
        ];
    }
}
