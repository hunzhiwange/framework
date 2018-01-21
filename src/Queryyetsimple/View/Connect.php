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
namespace Queryyetsimple\View;

use RuntimeException;
use InvalidArgumentException;
use Queryyetsimple\Support\Option;

/**
 * 模板处理抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
abstract class Connect
{
    use Option;

    /**
     * 变量值
     *
     * @var array
     */
    protected $vars = [];

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
     * @param mixed $name
     * @param mixed $value
     * @return void
     */
    public function setVar($name, $value = null)
    {
        if (is_array($name)) {
            $this->vars = array_merge($this->vars, $name);
        } else {
            $this->vars[$name] = $value;
        }
    }

    /**
     * 获取变量值
     *
     * @param string|null $name
     * @return mixed
     */
    public function getVar(string $name = null)
    {
        if (is_null($name)) {
            return $this->vars;
        }
        return $this->vars[$name] ?? null;
    }

    /**
     * 删除变量值
     *
     * @param mixed $name
     * @return $this
     */
    public function deleteVar($name)
    {
        $name = is_array($name) ? $name : func_get_args();
        foreach ($name as $item) {
            if (isset($this->vars[$item])) {
                unset($this->vars[$item]);
            }
        }
        return $this;
    }

    /**
     * 清空变量值
     *
     * @param string|null $name
     * @return $this
     */
    public function clearVar()
    {
        $this->vars = [];
        return $this;
    }

    /**
     * 分析展示的视图文件
     *
     * @param string $file 视图文件地址
     * @param string $ext 后缀
     * @return string|void
     */
    protected function parseDisplayFile(string $file = null, string $ext = '')
    {
        // 加载视图文件
        if (! is_file($file)) {
            $file = $this->parseFile($file, $ext);
        }

        // 分析默认视图文件
        if (! is_file($file)) {
            $file = $this->parseDefaultFile($file);
        }

        if (! is_file($file)) {
            throw new InvalidArgumentException(sprintf('Template file %s does not exist.', $file));
        }

        return $file;
    }

    /**
     * 分析模板真实路径
     *
     * @param string $tpl 文件地址
     * @param string $ext 扩展名
     * @return string
     */
    protected function parseFile(string $tpl = null, string $ext = '')
    {
        $tpl = trim(str_replace('->', '.', $tpl));

        // 完整路径或者变量
        if (pathinfo($tpl, PATHINFO_EXTENSION) || strpos($tpl, '$') === 0 || strpos($tpl, '(') !== false) {
            return $this->formatFile($tpl);
        } else {
            if (! $this->getOption('theme_path')) {
                throw new RuntimeException('Theme path must be set');
            }

            // 空取默认控制器和方法
            if ($tpl == '') {
                $tpl = $this->getOption('controller_name') . $this->getOption('controlleraction_depr') . $this->getOption('action_name');
            }

            if (strpos($tpl, '@') !== false) { // 分析主题
                $arr = explode('@', $tpl);
                $theme = array_shift($arr);
                $tpl = array_shift($arr);
            }

            $tpl = str_replace([
                '+',
                ':'
            ], $this->getOption('controlleraction_depr'), $tpl);

            return dirname($this->getOption('theme_path')) . '/' . ($theme ?? $this->getOption('theme_name')) . '/' . $tpl . ($ext ?: $this->getOption('suffix'));
        }
    }

    /**
     * 格式化文件名
     *
     * @param string $content
     * @return string
     */
    protected function formatFile(string $content)
    {
        return str_replace([
            ":",
            "+"
        ], [
            "->",
            "::"
        ], $content);
    }

    /**
     * 匹配默认地址（文件不存在）
     *
     * @param string $tpl 文件地址
     * @return string
     */
    protected function parseDefaultFile(string $tpl = null)
    {
        if (is_file($tpl)) {
            return $tpl;
        }

        if (! $this->getOption('theme_path')) {
            throw new RuntimeException('Theme path must be set');
        }

        $bak = $tpl;

        // 物理路径
        if (strpos($tpl, ':') !== false || strpos($tpl, '/') === 0 || strpos($tpl, '\\') === 0) {
            $tpl = str_replace(str_replace('\\', '/', $this->getOption('theme_path') . '/'), '', str_replace('\\', '/', ($tpl)));
        }

        // 备用地址
        if ($this->getOption('theme_path_default') && is_file(($tempTpl = $this->getOption('theme_path_default') . '/' . $tpl))) {
            return $tempTpl;
        }

        // default 主题
        if ($this->getOption('theme_name') != 'default' && is_file(($tempTpl = dirname($this->getOption('theme_path')) . '/default/' . $tpl))) {
            return $tempTpl;
        }

        return $bak;
    }
}
