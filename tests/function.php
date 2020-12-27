<?php

declare(strict_types=1);

/**
 * 国际化组件模拟.
 */
class I18nMock
{
    /**
     * lang.
     */
    public function gettext(string $text, ...$data): string
    {
        return sprintf($text, ...$data);
    }
}

/**
 * PDO 参数值变更替换
 * 
 * - PHP 8.1 后 \PDO::PARAM_INT,PDO::PARAM_BOOL,PDO::PARAM_STR 等常量发生变化
 */
function sql_pdo_param_compatible(string $sql): string
{
    if (\PHP_VERSION_ID < 80100) {
        return $sql;
    }

    return str_replace(
        ['param_type=2','param_type=1','param_type=5'], 
        ['param_type=3', 'param_type=2', 'param_type=1'],
    $sql);
}
