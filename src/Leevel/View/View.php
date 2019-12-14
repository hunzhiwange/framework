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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\View;

use RuntimeException;

/**
 * 模板处理抽象类.
 */
abstract class View
{
    /**
     * 变量值.
     *
     * @var array
     */
    protected array $vars = [];

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [];

    /**
     * 构造函数.
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
    }

    /**
     * 设置配置.
     *
     * @param mixed $value
     *
     * @return \Leevel\View\IView
     */
    public function setOption(string $name, $value): IView
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 设置模板变量.
     *
     * @param array|string $name
     * @param null|mixed   $value
     */
    public function setVar($name, $value = null): void
    {
        if (is_array($name)) {
            $this->vars = array_merge($this->vars, $name);
        } else {
            $this->vars[$name] = $value;
        }
    }

    /**
     * 获取变量值.
     *
     * @return mixed
     */
    public function getVar(?string $name = null)
    {
        if (null === $name) {
            return $this->vars;
        }

        return $this->vars[$name] ?? null;
    }

    /**
     * 删除变量值.
     */
    public function deleteVar(array $name): void
    {
        foreach ($name as $item) {
            if (isset($this->vars[$item])) {
                unset($this->vars[$item]);
            }
        }
    }

    /**
     * 清空变量值.
     */
    public function clearVar(): void
    {
        $this->vars = [];
    }

    /**
     * 分析展示的视图文件.
     *
     * @throws \RuntimeException
     */
    protected function parseDisplayFile(?string $file = null, ?string $ext = null): string
    {
        if (!is_file($file)) {
            $file = $this->parseFile($file, $ext);
        }

        if (!is_file($file)) {
            $e = sprintf('Template file %s does not exist.', $file);

            throw new RuntimeException($e);
        }

        return $file;
    }

    /**
     * 分析模板真实路径.
     *
     * @throws \RuntimeException
     */
    protected function parseFile(?string $tpl = null, ?string $ext = null): string
    {
        $tpl = trim(str_replace('->', '.', $tpl));

        // 完整路径或者变量以及表达式路径
        if (pathinfo($tpl, PATHINFO_EXTENSION) ||
            0 === strpos($tpl, '$') || false !== strpos($tpl, '(')) {
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
     */
    protected function formatFile(string $content): string
    {
        return str_replace([':', '+'], ['->', '::'], $content);
    }
}
