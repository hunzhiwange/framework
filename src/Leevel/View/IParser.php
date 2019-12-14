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

/**
 * iparser 接口.
 */
interface IParser
{
    /**
     * 注册视图编译器.
     *
     * @return \Leevel\View\IParser
     */
    public function registerCompilers(): self;

    /**
     * 注册视图分析器.
     *
     * @return \Leevel\View\IParser
     */
    public function registerParsers(): self;

    /**
     * 执行编译.
     *
     * @return string|void
     */
    public function doCompile(string $file, ?string $cachePath = null, bool $isContent = false);

    /**
     * code 编译编码，后还原
     */
    public static function revertEncode(string $content): string;

    /**
     * tagself 编译编码，后还原
     */
    public static function globalEncode(string $content): string;
}
