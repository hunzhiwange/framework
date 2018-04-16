<?php
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
namespace Leevel\Option;

use RuntimeException;
use Leevel\{
    Support\Arr,
    Filesystem\Fso
};

/**
 * 配置工具类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.18
 * @version 1.0
 */
class Load
{

    /**
     * 缓存路径
     *
     * @var string
     */
    protected $strCachePath;

    /**
     * 载入路径
     *
     * @var array
     */
    protected $arrDir = [];

    /**
     * 已经载入数据
     *
     * @var array
     */
    protected $loaded = [];

    /**
     * 构造函数
     *
     * @param array $arrDir
     * @return void
     */
    public function __construct(array $arrDir = [])
    {
        $this->arrDir = $arrDir;
    }

    /**
     * 设置缓存路径
     *
     * @param string $strCachePath
     * @return $this
     */
    public function setCachePath($strCachePath)
    {
        $this->strCachePath = $strCachePath;
        return $this;
    }

    /**
     * 设置目录
     *
     * @param array $arrDir
     * @return $this
     */
    public function setDir(array $arrDir)
    {
        $this->arrDir = $arrDir;
        return $this;
    }

    /**
     * 添加目录
     *
     * @param array $arrDir
     * @return $this
     */
    public function addDir(array $arrDir)
    {
        $this->arrDir = array_unique(array_merge($this->arrDir, $arrDir));
        return $this;
    }

    /**
     * 载入配置数据
     *
     * @param array $arrExtendData
     * @return array
     */
    public function loadData($arrExtendData = [])
    {
        if ($this->loaded) {
            return $this->loaded;
        }

        $arrData = $arrType = [];

        foreach ($this->arrDir as $sDir) {
            if (is_dir($sDir) && ($arrFile = Fso::lists($sDir, 'file', false, [], [
                'php'
            ]))) {
                foreach ($arrFile as $strFile) {
                    $strType = substr($strFile, 0, - 4);
                    $arrType[] = $strType;

                    if (! isset($arrData[$strType])) {
                        $arrData[$strType] = [];
                    }
                    if (is_array($arrFoo = include $sDir . '/' . $strFile)) {
                        $arrData[$strType] = array_merge($arrData[$strType], $arrFoo);
                    }
                }
            }
        }

        foreach ($arrType as $sType) {
            $arrData[$sType] = Arr::merge($arrData[$sType]);
        }

        if ($arrExtendData) {
            foreach ($arrExtendData as $sType => $arrTemp) {
                if (isset($arrData[$sType])) {
                    $arrData[$sType] = Arr::merge(array_merge($arrData[$sType], $arrTemp));
                } else {
                    $arrData[$sType] = $arrTemp;
                }
            }
        }

        $sCacheFile = $this->strCachePath;
        $sDir = dirname($sCacheFile);
        if (! is_dir($sDir)) {
            mkdir($sDir, 0777, true);
        }

        if (! file_put_contents($sCacheFile, '<?' . 'php /* ' . date('Y-m-d H:i:s') . ' */ ?' . '>' . 
            PHP_EOL . '<?' . 'php return ' . var_export($arrData, true) . '; ?' . '>')) {
            throw new RuntimeException(sprintf('Dir %s do not have permission.', $sDir));
        }

        chmod($sCacheFile, 0777);

        return $this->loaded = $arrData;
    }
}
