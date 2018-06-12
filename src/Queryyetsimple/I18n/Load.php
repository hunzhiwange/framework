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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\I18n;

use RuntimeException;

/**
 * 语言包工具类导入类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.25
 *
 * @version 1.0
 */
class Load
{
    /**
     * 当前语言包.
     *
     * @var string
     */
    protected $i18n = 'zh-CN';

    /**
     * 载入路径.
     *
     * @var array
     */
    protected $dirs = [];

    /**
     * 已经载入数据.
     *
     * @var array
     */
    protected $loaded;

    /**
     * 构造函数.
     *
     * @param array $dirs
     */
    public function __construct(array $dirs = [])
    {
        $this->dirs = $dirs;
    }

    /**
     * 设置当前语言包.
     *
     * @param string $i18n
     *
     * @return $this
     */
    public function setI18n($i18n)
    {
        $this->i18n = $i18n;

        return $this;
    }

    /**
     * 添加目录.
     *
     * @param array $dirs
     *
     * @return $this
     */
    public function addDir(array $dirs)
    {
        $this->dirs = array_unique(array_merge($this->dirs, $dirs));

        return $this;
    }

    /**
     * 载入语言包数据.
     *
     * @author 小牛
     *
     * @since 2016.11.27
     *
     * @return array
     */
    public function loadData()
    {
        if (null !== $this->loaded) {
            return $this->loaded;
        }

        $files = $this->findMoFile($this->parseDir($this->dirs));
        $texts = $this->parseMoData($files);

        $texts['Query Yet Simple'] = '左手代码 右手年华 不忘初心 简单快乐';

        return $this->loaded = $texts;
    }

    /**
     * 分析目录中的 PHP 语言包包含的文件.
     *
     * @param array $dirs 文件地址
     *
     * @author 小牛
     *
     * @since 2016.11.27
     *
     * @return array
     */
    protected function findMoFile(array $dirs): array
    {
        $files = [];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                throw new RuntimeException('I18n load dir is not exits.');
            }

            $files = array_merge($files, $this->getMoFiles($dir));
        }

        return $files;
    }

    /**
     * 获取目录中的 MO 文件.
     *
     * @param string $dir
     *
     * @return array
     */
    protected function getMoFiles(string $dir): array
    {
        return glob($dir.'/*.mo');
    }

    /**
     * 分析 mo 文件语言包数据.
     *
     * @param array $files 文件地址
     *
     * @author 小牛
     *
     * @since 2016.11.25
     *
     * @return array
     */
    protected function parseMoData(array $files): array
    {
        return (new mo())->readToArray($files);
    }

    /**
     * 分析目录.
     *
     * @param array $dirs
     *
     * @return array
     */
    protected function parseDir(array $dirs): array
    {
        $i18n = $this->i18n;

        return array_map(function ($dir) use ($i18n) {
            return $dir.'/'.$i18n;
        }, $dirs);
    }
}
