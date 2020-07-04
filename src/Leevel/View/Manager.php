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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\View;

use Leevel\Manager\Manager as Managers;

/**
 * 视图管理器.
 *
 * @method static string display(string $file, array $vars = [], ?string $ext = null) 加载视图文件.
 * @method static void setParseResolver(\Closure $parseResolver)                      设置 parser 解析回调.
 * @method static string getCachePath(string $file)                                   获取编译路径.
 * @method static void setVar($name, $value = null)                                   设置模板变量.
 * @method static getVar(?string $name = null)                                        获取变量值.
 * @method static void deleteVar(array $name)                                         删除变量值.
 * @method static void clearVar()                                                     清空变量值.
 */
class Manager extends Managers
{
    /**
     * {@inheritdoc}
     */
    public function normalizeConnectOption(string $connect): array
    {
        return array_merge(
            $this->getViewOptionCommon(),
            parent::normalizeConnectOption($connect),
        );
    }

    /**
     * 取得配置命名空间.
     */
    protected function getOptionNamespace(): string
    {
        return 'view';
    }

    /**
     * 创建 html 模板驱动.
     *
     * @return \Leevel\View\Html
     */
    protected function makeConnectHtml(string $connect): Html
    {
        $html = new Html($this->normalizeConnectOption($connect));
        $html->setParseResolver(function (): Parser {
            return $this->container['view.parser'];
        });

        return $html;
    }

    /**
     * 创建 phpui 模板驱动.
     *
     * @return \Leevel\View\Phpui
     */
    protected function makeConnectPhpui(string $connect): Phpui
    {
        return new Phpui($this->normalizeConnectOption($connect));
    }

    /**
     * 视图公共配置.
     */
    protected function getViewOptionCommon(): array
    {
        return [
            'theme_path' => $this->container->make('app')->themesPath(),
            'cache_path' => $this->container->make('app')->runtimePath('theme'),
        ];
    }
}
