<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\option;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

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
class tool
{

    /**
     * 合并配置文件数据
     *
     * @param array $arrOptionDir
     * @param string $sOptionCache
     * @param array $arrExtendData
     * @param boolean $booInitApp
     * @return array
     */
    public static function saveToCache($arrOptionDir, $sOptionCache, $arrExtendData = [], $booInitApp = false)
    {
        $arrData = $arrType = [ ];

        foreach ($arrOptionDir as $sDir) {
            if (is_dir($sDir) && ($arrFile = fso::lists($sDir, 'file'))) {
                foreach ($arrFile as $strFile) {
                    if (fso::getExtension($strFile, 2) == 'php') {
                        $strType = substr($strFile, 0, - 4);
                        $arrType [] = $strType;

                        if (! isset($arrData [$strType])) {
                            $arrData [$strType] = [ ];
                        }
                        if (is_array($arrFoo = include $sDir . '/' . $strFile)) {
                            $arrData [$strType] = array_merge($arrData [$strType], $arrFoo);
                        }
                    }
                }
            }
        }

        foreach ($arrType as $sType) {
            $arrData [$sType] = helper::arrayMergePlus($arrData [$sType]);
        }

        if ($arrExtendData) {
            foreach ($arrExtendData as $sType => $arrTemp) {
                if (isset($arrData [$sType])) {
                    $arrData [$sType] = helper::arrayMergePlus(array_merge($arrData [$sType], $arrTemp));
                } else {
                    $arrData [$sType] = $arrTemp;
                }
            }
        }

        if ($booInitApp) {
            self::router($arrData, $arrOptionDir);
        }

        if (! is_dir(dirname($sOptionCache))) {
            fso::createDirectory(dirname($sOptionCache));
        }

        if (! file_put_contents($sOptionCache, '<?php return ' . var_export($arrData, true) . '; ?>')) {
            throw new RuntimeException(sprintf('Dir %s do not have permission.', $this->optioncache_path));
        }
        file_put_contents($sOptionCache, '<?php /* ' . date('Y-m-d H:i:s') . ' */ ?>' . PHP_EOL . php_strip_whitespace($sOptionCache));

        return $arrData;
    }

    /**
     * 路由配置
     *
     * @param array $arrData
     * @param array $arrOptionDir
     * @return void
     */
    protected static function router(&$arrData, $arrOptionDir)
    {
        $arrData ['app'] ['~routers~'] = [ ];

        foreach ($arrOptionDir as $sDir) {
            $sDir = dirname($sDir) . '/router';
            if (is_dir($sDir) && ($arrFile = fso::lists($sDir, 'file'))) {
                foreach ($arrFile as $strFile) {
                    if (fso::getExtension($strFile, 2) == 'php') {
                        $arrData ['app'] ['~routers~'] [] = $sDir . '/' . $strFile;
                    }
                }
            }
        }
    }
}
