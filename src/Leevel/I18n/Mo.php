<?php

declare(strict_types=1);

namespace Leevel\I18n;

use Gettext\Translations;

/**
 * 解析 mo 文件.
 */
class Mo implements IGettext
{
    /**
     * {@inheritDoc}
     */
    public function read(array $filenames): array
    {
        $translations = new Translations();
        foreach ($filenames as $val) {
            $translations->addFromMoFile($val);
        }

        $result = json_decode($translations->toJsonString(), true);
        $result = $result['messages'][''] ?? [];
        $result = array_map(function ($item) {
            return $item[0];
        }, $result);

        return $result;
    }
}
