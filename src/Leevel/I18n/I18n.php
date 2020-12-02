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
 * 国际化组件.
 */
class I18n implements II18n
{
    /**
     * 当前语言上下文.
    */
    protected string $i18n;

    /**
     * 语言数据.
     */
    protected array $text = [];

    /**
     * 构造函数.
     */
    public function __construct(string $i18n)
    {
        $this->i18n = $i18n;
        $this->text[$i18n] = [];
    }

    /**
     * {@inheritdoc}
     */
    public function gettext(string $text, ...$data): string
    {
        $value = $this->text[$this->i18n][$text] ?? $text;

        if ($data) {
            return sprintf($value, ...$data);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function addtext(string $i18n, array $data = []): void
    {
        if (array_key_exists($i18n, $this->text)) {
            $this->text[$i18n] = array_merge($this->text[$i18n], $data);
        } else {
            $this->text[$i18n] = $data;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setI18n(string $i18n): void
    {
        $this->i18n = $i18n;
    }

    /**
     * {@inheritdoc}
     */
    public function getI18n(): string
    {
        return $this->i18n;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->text;
    }
}
