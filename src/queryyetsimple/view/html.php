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
namespace queryyetsimple\view;

use RuntimeException;

/**
 * html 模板处理类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
class html extends aconnect implements iconnect
{

    /**
     * 视图分析器
     *
     * @var \queryyetsimple\view\iparser
     */
    protected $objParse;

    /**
     * 解析 parse
     *
     * @var callable
     */
    protected static $calParseResolver;

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'development' => false,
        'controller_name' => 'index',
        'action_name' => 'index',
        'controlleraction_depr' => '_',
        'theme_name' => 'default',
        'theme_path' => '',
        'theme_path_default' => '',
        'suffix' => '.html',
        'theme_cache_path' => '',
        'cache_children' => false
    ];

    /**
     * 加载视图文件
     *
     * @param string $sFile 视图文件地址
     * @param boolean $bDisplay 是否显示
     * @param string $strExt 后缀
     * @return string
     */
    public function display(string $sFile = null, bool $bDisplay = true, string $strExt = '')
    {
        // 加载视图文件
        $sFile = $this->parseDisplayFile($sFile, $strExt);

        // 变量赋值
        if (is_array($this->arrVar) && ! empty($this->arrVar)) {
            extract($this->arrVar, EXTR_PREFIX_SAME, 'q_');
        }

        $sCachePath = $this->getCachePath($sFile); // 编译文件路径
        
        if ($this->isCacheExpired($sFile, $sCachePath)) { // 重新编译
            $this->parser()->doCombile($sFile, $sCachePath);
        }

        // 返回类型
        if ($bDisplay === false) {
            ob_start();
            include $sCachePath;
            $sReturn = ob_get_contents();
            ob_end_clean();

            return $sReturn;
        } else {
            include $sCachePath;
        }
    }

    /**
     * 设置 parse 解析回调
     *
     * @param callable $calParseResolver
     * @return void
     */
    public static function setParseResolver(callable $calParseResolver)
    {
        static::$calParseResolver = $calParseResolver;
    }

    /**
     * 解析 parse
     *
     * @return \queryyetsimple\view\iparser
     */
    public function resolverParse()
    {
        if (! static::$calParseResolver) {
            throw new RuntimeException('Theme not set parse resolver');
        }
        return call_user_func(static::$calParseResolver);
    }

    /**
     * 获取分析器
     *
     * @return \queryyetsimple\view\iparser
     */
    public function parser()
    {
        if (! is_null($this->objParse)) {
            return $this->objParse;
        }
        return $this->objParse = $this->resolverParse();
    }

    /**
     * 获取编译路径
     *
     * @param string $sFile
     * @return string
     */
    protected function getCachePath(string $sFile)
    {
        if (! $this->getOption('theme_cache_path')) {
            throw new RuntimeException('Theme cache path must be set');
        }

        // 统一斜线
        $sFile = str_replace('//', '/', str_replace('\\', '/', $sFile));

        // 统一缓存文件
        $sFile = basename($sFile, '.' . pathinfo($sFile, PATHINFO_EXTENSION)) . '.' . md5($sFile) . '.php';

        // 返回真实路径
        return $this->getOption('theme_cache_path') . '/' . $sFile;
    }

    /**
     * 判断缓存是否过期
     *
     * @param string $sFile
     * @param string $sCachePath
     * @return boolean
     */
    protected function isCacheExpired(string $sFile, string $sCachePath)
    {
        // 开启调试
        if ($this->getOption('development')) {
            return true;
        }

        // 缓存文件不存在过期
        if (! is_file($sCachePath)) {
            return true;
        }

        // 编译过期时间为 <= 0 表示永不过期
        if ($this->getOption('cache_lifetime') <= 0) {
            return false;
        }

        // 缓存时间到期
        if (filemtime($sCachePath) + intval($this->getOption('cache_lifetime')) < time()) {
            return true;
        }

        // 文件有更新
        if (filemtime($sFile) >= filemtime($sCachePath)) {
            return true;
        }

        return false;
    }
}
