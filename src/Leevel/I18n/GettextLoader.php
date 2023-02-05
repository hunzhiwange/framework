<?php

declare(strict_types=1);

namespace Leevel\I18n;

use Gettext\Loader\MoLoader;
use Gettext\Loader\PoLoader;
use Gettext\Translations;

/**
 * 解析语言文件.
 */
class GettextLoader
{
    /**
     * 读取 PO 文件数据.
     */
    public function loadPoFile(array $fileNames): array
    {
        return $this->loadGettextFile($fileNames, function (): PoLoader {
            return new PoLoader();
        });
    }

    /**
     * 读取 Mo 文件数据.
     */
    public function loadMoFile(array $fileNames): array
    {
        return $this->loadGettextFile($fileNames, function (): MoLoader {
            return new MoLoader();
        });
    }

    /**
     * 读取语言文件数据.
     */
    protected function loadGettextFile(array $fileNames, \Closure $loaderResolver): array
    {
        $translations = Translations::create();
        foreach ($fileNames as $file) {
            $loader = $loaderResolver();
            $translations = $translations->mergeWith($loader->loadFile($file));
        }

        $result = [];
        foreach ($translations->toArray()['translations'] as $each) {
            $result[$each['original']] = $each['translation'];
        }

        return array_filter($result, fn (string $v) => '' !== $v);
    }
}
