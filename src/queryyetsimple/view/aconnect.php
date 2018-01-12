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
use InvalidArgumentException;
use queryyetsimple\support\option;

/**
 * 模板处理抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
abstract class aconnect
{
    use option;

    /**
     * 变量值
     *
     * @var array
     */
    protected $arrVar = [];

    /**
     * 构造函数
     *
     * @param array $arrOption
     * @return void
     */
    public function __construct(array $arrOption = [])
    {
        $this->options($arrOption);
    }

    /**
     * 设置模板变量
     *
     * @param mixed $mixName
     * @param mixed $mixValue
     * @return void
     */
    public function setVar($mixName, $mixValue = null)
    {
        if (is_array($mixName)) {
            $this->arrVar = array_merge($this->arrVar, $mixName);
        } else {
            $this->arrVar[$mixName] = $mixValue;
        }
    }

    /**
     * 获取变量值
     *
     * @param string|null $sName
     * @return mixed
     */
    public function getVar(string $sName = null)
    {
        if (is_null($sName)) {
            return $this->arrVar;
        }
        return $this->arrVar[$sName] ?? null;
    }

    /**
     * 删除变量值
     *
     * @param mixed $mixName
     * @return $this
     */
    public function deleteVar($mixName)
    {
        $mixName = is_array($mixName) ? $mixName : func_get_args();
        foreach ($mixName as $strName) {
            if (isset($this->arrVar[$strName])) {
                unset($this->arrVar[$strName]);
            }
        }
        return $this;
    }

    /**
     * 清空变量值
     *
     * @param string|null $sName
     * @return $this
     */
    public function clearVar()
    {
        $this->arrVar = [];
        return $this;
    }

    /**
     * 分析展示的视图文件
     *
     * @param string $sFile 视图文件地址
     * @param string $strExt 后缀
     * @return string|void
     */
    protected function parseDisplayFile(string $sFile = null, string $strExt = '')
    {
        // 加载视图文件
        if (! is_file($sFile)) {
            $sFile = $this->parseFile($sFile, $strExt);
        }

        // 分析默认视图文件
        if (! is_file($sFile)) {
            $sFile = $this->parseDefaultFile($sFile);
        }

        if (! is_file($sFile)) {
            throw new InvalidArgumentException(sprintf('Template file %s does not exist.', $sFile));
        }

        return $sFile;
    }

    /**
     * 分析模板真实路径
     *
     * @param string $sTpl 文件地址
     * @param string $sExt 扩展名
     * @return string
     */
    protected function parseFile(string $sTpl = null, string $sExt = '')
    {
        $calHelp = function ($sContent) {
            return str_replace([
                ':',
                '+'
            ], [
                '->',
                '::'
            ], $sContent);
        };

        $sTpl = trim(str_replace('->', '.', $sTpl));

        // 完整路径或者变量
        if (pathinfo($sTpl, PATHINFO_EXTENSION) || strpos($sTpl, '$') === 0) {
            return $calHelp($sTpl);
        } elseif (strpos($sTpl, '(') !== false) { // 存在表达式
            return $calHelp($sTpl);
        } else {
            if (! $this->getOption('theme_path')) {
                throw new RuntimeException('Theme path must be set');
            }

            // 空取默认控制器和方法
            if ($sTpl == '') {
                $sTpl = $this->getOption('controller_name') . $this->getOption('controlleraction_depr') . $this->getOption('action_name');
            }

            if (strpos($sTpl, '@') !== false) { // 分析主题
                $arrArray = explode('@', $sTpl);
                $sTheme = array_shift($arrArray);
                $sTpl = array_shift($arrArray);
                unset($arrArray);
            }

            $sTpl = str_replace([
                '+',
                ':'
            ], $this->getOption('controlleraction_depr'), $sTpl);
            
            return dirname($this->getOption('theme_path')) . '/' . ($sTheme ?? $this->getOption('theme_name')) . '/' . $sTpl . ($sExt ?: $this->getOption('suffix'));
        }
    }

    /**
     * 匹配默认地址（文件不存在）
     *
     * @param string $sTpl 文件地址
     * @return string
     */
    protected function parseDefaultFile(string $sTpl = null)
    {
        if (is_file($sTpl)) {
            return $sTpl;
        }

        if (! $this->getOption('theme_path')) {
            throw new RuntimeException('Theme path must be set');
        }

        $sBakTpl = $sTpl;

        // 物理路径
        if (strpos($sTpl, ':') !== false || strpos($sTpl, '/') === 0 || strpos($sTpl, '\\') === 0) {
            $sTpl = str_replace(str_replace('\\', '/', $this->getOption('theme_path') . '/'), '', str_replace('\\', '/', ($sTpl)));
        }

        // 备用地址
        if ($this->getOption('theme_path_default') && is_file(($sTempTpl = $this->getOption('theme_path_default') . '/' . $sTpl))) {
            return $sTempTpl;
        }

        // default 主题
        if ($this->getOption('theme_name') != 'default' && is_file(($sTempTpl = dirname($this->getOption('theme_path')) . '/default/' . $sTpl))) {
            return $sTempTpl;
        }

        return $sBakTpl;
    }
}
