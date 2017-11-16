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
namespace queryyetsimple\cache;

use InvalidArgumentException;
use queryyetsimple\support\option;
use queryyetsimple\filesystem\fso;

/**
 * 文件缓存
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class file extends acache implements iconnect
{
    use option;

    /**
     * 缓存文件头部
     *
     * @var string
     */
    const HEADER = '<?php die( %s ); ?>';

    /**
     * 缓存文件头部长度
     *
     * @var int
     */
    const HEADER_LENGTH = 43;

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'nocache_force' => '~@nocache_force',
        'time_preset' => [],
        'prefix' => '~@',
        'expire' => 86400,
        'path' => '',
        'serialize' => true
    ];

    /**
     * 获取缓存
     *
     * @param string $sCacheName
     * @param mixed $mixDefault
     * @param array $arrOption
     * @return mixed
     */
    public function get($sCacheName, $mixDefault = false, array $arrOption = [])
    {
        if ($this->checkForce()) {
            return $mixDefault;
        }

        $arrOption = $this->getOptions($arrOption);
        $sCachePath = $this->getCachePath($sCacheName, $arrOption);

        // 清理文件状态缓存 http://php.net/manual/zh/function.clearstatcache.php
        clearstatcache();

        if (! is_file($sCachePath)) {
            return false;
        }

        $hFp = fopen($sCachePath, 'rb');
        if (! $hFp) {
            return false;
        }
        flock($hFp, LOCK_SH);

        // 头部的 43 个字节存储了安全代码
        $nLen = filesize($sCachePath);
        fread($hFp, static::HEADER_LENGTH);
        $nLen -= static::HEADER_LENGTH;

        do {
            // 检查缓存是否已经过期
            if ($this->isExpired($sCacheName, $arrOption)) {
                $strData = false;
                break;
            }

            if ($nLen > 0) {
                $strData = fread($hFp, $nLen);
            } else {
                $strData = false;
            }
        } while (false);

        flock($hFp, LOCK_UN);
        fclose($hFp);

        if ($strData === false) {
            return false;
        }

        // 解码
        if ($arrOption['serialize']) {
            $strData = unserialize($strData);
        }

        return $strData;
    }

    /**
     * 设置缓存
     *
     * @param string $sCacheName
     * @param mixed $mixData
     * @param array $arrOption
     * @return void
     */
    public function set($sCacheName, $mixData, array $arrOption = [])
    {
        $arrOption = $this->getOptions($arrOption);
        if ($arrOption['serialize']) {
            $mixData = serialize($mixData);
        }
        $mixData = sprintf(static::HEADER, '/* ' . date('Y-m-d H:i:s') . '  */') . $mixData;

        $sCachePath = $this->getCachePath($sCacheName, $arrOption);
        $this->writeData($sCachePath, $mixData);
    }

    /**
     * 清除缓存
     *
     * @param string $sCacheName
     * @param array $arrOption
     * @return void
     */
    public function delele($sCacheName, array $arrOption = [])
    {
        $arrOption = $this->getOptions($arrOption);
        $sCachePath = $this->getCachePath($sCacheName, $arrOption);
        if ($this->exist($sCacheName, $arrOption)) {
            @unlink($sCachePath);
        }
    }

    /**
     * 验证缓存是否过期
     *
     * @param string $sCacheName
     * @param array $arrOption
     * @return boolean
     */
    protected function isExpired($sCacheName, $arrOption)
    {
        $sFilePath = $this->getCachePath($sCacheName, $arrOption);
        if (! is_file($sFilePath)) {
            return true;
        }
        $arrOption['expire'] = $this->cacheTime($sCacheName, $arrOption['expire']);
        return ( int ) $arrOption['expire'] > 0 && filemtime($sFilePath) + ( int ) $arrOption['expire'] < time();
    }

    /**
     * 获取缓存路径
     *
     * @param string $sCacheName
     * @param array $arrOption
     * @return string
     */
    protected function getCachePath($sCacheName, $arrOption)
    {
        if (! $arrOption['path']) {
            throw new InvalidArgumentException('Cache path is not allowed empty.');
        }

        if (! is_dir($arrOption['path'])) {
            fso::createDirectory($arrOption['path']);
        }
        return $arrOption['path'] . '/' . $this->getCacheName($sCacheName, $arrOption['prefix']) . '.php';
    }

    /**
     * 写入缓存数据
     *
     * @param string $sFileName
     * @param string $sData
     * @return void
     */
    protected function writeData($sFileName, $sData)
    {
        ! is_dir(dirname($sFileName)) && fso::createDirectory(dirname($sFileName));
        file_put_contents($sFileName, $sData, LOCK_EX);
    }

    /**
     * 验证缓存是否存在
     *
     * @param string $sCacheName
     * @param array $arrOption
     * @return boolean
     */
    protected function exist($sCacheName, $arrOption)
    {
        return is_file($this->getCachePath($sCacheName, $arrOption));
    }

    /**
     * 获取缓存名字
     * 去掉特殊缓存名字字符
     *
     * @param string $sCacheName
     * @param string $strPrefix
     * @return string
     */
    protected function getCacheName($sCacheName, $strPrefix = '')
    {
        return str_replace([
            '?',
            '*',
            ':',
            '"',
            '<',
            '>',
            '\\',
            '/',
            '|'
        ], '.', parent::getCacheName($sCacheName, $strPrefix));
    }
}
