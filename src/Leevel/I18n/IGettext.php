<?php

declare(strict_types=1);

namespace Leevel\I18n;

/**
 * 语言包数据读取接口.
 */
interface IGettext
{
    /**
     * 读取文件数据.
     */
    public function read(array $filenames): array;
}
