<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\option;

use RuntimeException;
use queryyetsimple\support\helper;
use queryyetsimple\filesystem\fso;

/**
 * 配置工具类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.18
 * @version 1.0
 */
class load
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
    protected $arrLoad = [];

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
     * @param boolean $booInitApp
     * @param array $arrExtendData
     * @return array
     */
    public function loadData($booInitApp = false, $arrExtendData = [])
    {
        if (isset($this->arrLoad[$booInitApp])) {
            return $this->arrLoad[$booInitApp];
        }

        $arrData = $arrType = [];

        foreach ($this->arrDir as $sDir) {
            if (is_dir($sDir) && ($arrFile = fso::lists($sDir, 'file', false, [], [
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
            $arrData[$sType] = helper::arrayMergePlus($arrData[$sType]);
        }

        if ($arrExtendData) {
            foreach ($arrExtendData as $sType => $arrTemp) {
                if (isset($arrData[$sType])) {
                    $arrData[$sType] = helper::arrayMergePlus(array_merge($arrData[$sType], $arrTemp));
                } else {
                    $arrData[$sType] = $arrTemp;
                }
            }
        }

        if ($booInitApp) {
            $this->router($arrData, $this->arrDir);
        }

        $sCacheFile = $this->strCachePath;
        $sDir = dirname($sCacheFile);
        if (! is_dir($sDir)) {
            fso::createDirectory($sDir);
        }

        if (! file_put_contents($sCacheFile, '<?php return ' . var_export($arrData, true) . '; ?>')) {
            throw new RuntimeException(sprintf('Dir %s do not have permission.', $sDir));
        }
        file_put_contents($sCacheFile, '<?php /* ' . date('Y-m-d H:i:s') . ' */ ?>' . PHP_EOL . php_strip_whitespace($sCacheFile));

        return $this->arrLoad[$booInitApp] = $arrData;
    }

    /**
     * 路由配置
     *
     * @param array $arrData
     * @param array $arrOptionDir
     * @return void
     */
    protected function router(&$arrData, $arrOptionDir)
    {
        $arrData['app']['~routers~'] = [];

        foreach ($arrOptionDir as $sDir) {
            $sDir = dirname($sDir) . '/router';
            if (is_dir($sDir) && ($arrFile = fso::lists($sDir, 'file', false, [], [
                'php'
            ]))) {
                foreach ($arrFile as $strFile) {
                    $arrData['app']['~routers~'][] = $sDir . '/' . $strFile;
                }
            }
        }
    }
}
