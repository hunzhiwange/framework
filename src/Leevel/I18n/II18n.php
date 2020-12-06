<?php

declare(strict_types=1);

namespace Leevel\I18n;

/**
 * II18n 接口.
 */
interface II18n
{
    /**
     * 获取语言 text.
     */
    public function gettext(string $text, ...$data): string;

    /**
     * 添加语言包.
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
