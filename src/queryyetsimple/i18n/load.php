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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\i18n;

use RuntimeException;
use queryyetsimple\support\helper;
use queryyetsimple\filesystem\fso;

/**
 * 语言包工具类导入类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.25
 * @version 1.0
 */
class load
{

    /**
     * 当前语言包
     *
     * @var string
     */
    protected $strI18n = 'zh-cn';

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
    protected $arrLoad;

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
     * 设置当前语言包
     *
     * @param string $strI18n
     * @return $this
     */
    public function setI18n($strI18n)
    {
        $this->strI18n = $strI18n;
        return $this;
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
     * 载入语言包数据
     *
     * @author 小牛
     * @since 2016.11.27
     * @return array
     */
    public function loadData()
    {
        if (! is_null($this->arrLoad)) {
            return $this->arrLoad;
        }

        $arrFiles = $this->findMoFile($this->parseDir($this->arrDir));
        $arrTexts = $this->parseMoData($arrFiles);
        $sCacheFile = $this->strCachePath;
        $sDir = dirname($sCacheFile);
        if (! is_dir($sDir)) {
            fso::createDirectory($sDir);
        }

        // 防止空数据无法写入
        $arrTexts['Query Yet Simple'] = 'Query Yet Simple';
        if (! file_put_contents($sCacheFile, '<?php return ' . var_export($arrTexts, true) . '; ?>')) {
            throw new RuntimeException(sprintf('Dir %s do not have permission.', $sDir));
        }
        file_put_contents($sCacheFile, '<?php /* ' . date('Y-m-d H:i:s') . ' */ ?>' . PHP_EOL . php_strip_whitespace($sCacheFile));

        return $this->arrLoad = $arrTexts;
    }

    /**
     * 分析目录中的 PHP 语言包包含的文件
     *
     * @param array $arrDir 文件地址
     * @author 小牛
     * @since 2016.11.27
     * @return array
     */
    public function findMoFile(array $arrDir)
    {
        $arrFiles = [];
        foreach ($arrDir as $sDir) {
            if (! is_dir($sDir)) {
                continue;
            }

            $arrFiles = array_merge($arrFiles, fso::lists($sDir, 'file', true, [], [
                'mo'
            ]));
        }

        return $arrFiles;
    }

    /**
     * 分析 mo 文件语言包数据
     *
     * @param array $arrFile 文件地址
     * @author 小牛
     * @since 2016.11.25
     * @return array
     */
    public function parseMoData(array $arrFile)
    {
        return (new mo())->readToArray($arrFile);
    }

    /**
     * 分析目录
     *
     * @param array $arrDir
     * @return array
     */
    protected function parseDir(array $arrDir)
    {
        $strI18n = $this->strI18n;
        return array_map(function ($strDir) use ($strI18n) {
            return $strDir . '/' . $strI18n;
        }, $arrDir);
    }
}
