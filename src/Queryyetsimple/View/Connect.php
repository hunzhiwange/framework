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

namespace Leevel\View;

use RuntimeException;

/**
 * 模板处理抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.18
 *
 * @version 1.0
 */
abstract class Connect
{
    /**
     * 变量值
     *
     * @var array
     */
    protected $vars = [];

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [];

    /**
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value)
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 设置模板变量.
     *
     * @param mixed $name
     * @param mixed $value
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
     * @param null|string $name
     *
     * @return mixed
     */
    public function getVar(string $name = null)
    {
        if (null === $name) {
            return $this->vars;
        }

        return $this->vars[$name] ?? null;
    }

    /**
     * 删除变量值
     *
     * @param mixed $name
     *
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
     * @param null|string $name
     *
     * @return $this
     */
    public function clearVar()
    {
        $this->vars = [];

        return $this;
    }

    /**
     * 分析展示的视图文件.
     *
     * @param string $file 视图文件地址
     * @param string $ext  后缀
     *
     * @return string|void
     */
    protected function parseDisplayFile(?string $file = null, ?string $ext = '')
    {
        if (!is_file($file)) {
            $file = $this->parseFile($file, $ext);
        }

        if (!is_file($file)) {
            throw new RuntimeException(
                sprintf('Template file %s does not exist.', $file)
            );
        }

        return $file;
    }

    /**
     * 分析模板真实路径.
     *
     * @param string $tpl 文件地址
     * @param string $ext 扩展名
     *
     * @return string
     */
    protected function parseFile(?string $tpl = null, ?string $ext = '')
    {
        $tpl = trim(str_replace('->', '.', $tpl));

        // 完整路径或者变量以及表达式路径
        if (pathinfo($tpl, PATHINFO_EXTENSION) ||
            0 === strpos($tpl, '$') ||
            false !== strpos($tpl, '(')) {
            return $this->formatFile($tpl);
        }

        if (!$this->option['theme_path']) {
            throw new RuntimeException('Theme path must be set.');
        }

        return $this->option['theme_path'].'/'.$tpl.
            ($ext ?: $this->option['suffix']);
    }

    /**
     * 格式化文件名.
     *
     * @param string $content
     *
     * @return string
     */
    protected function formatFile(string $content)
    {
        return str_replace([
            ':',
            '+',
        ], [
            '->',
            '::',
        ], $content);
    }
}
