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

namespace Leevel\I18n;

/**
 * II18n 接口.
 */
interface II18n
{
    /**
     * 获取语言 text.
     *
     * @param array ...$data
     */
    public function __(string $text, ...$data): string;

    /**
     * 获取语言 text.
     *
     * @param array ...$data
     */
    public function gettext(string $text, ...$data): string;

    /**
     * 添加语言包.
     *
     * @param string $i18n 语言名字
     * @param array  $data 语言包数据
     */
    public function addtext(string $i18n, array $data = []): void;

    /**
     * 设置当前语言包上下文环境.
     */
    public function setI18n(string $i18n): void;

    /**
     * 获取当前语言包.
     */
    public function getI18n(): string;

    /**
     * 返回所有语言包.
     */
    public function all(): array;
}
