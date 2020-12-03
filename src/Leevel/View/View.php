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

use Exception;
use RuntimeException;
use Throwable;

/**
 * 视图抽象类.
 */
abstract class View implements IView
{
    /**
     * 变量值.
     */
    protected array $vars = [];

    /**
     * 配置.
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
     * {@inheritDoc}
     */
    public function setVar(array|string $name, mixed $value = null): void
    {
        if (is_array($name)) {
            $this->vars = array_merge($this->vars, $name);
        } else {
            $this->vars[$name] = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getVar(?string $name = null): mixed
    {
        if (null === $name) {
            return $this->vars;
        }

        return $this->vars[$name] ?? null;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
    protected function parseDisplayFile(string $file, ?string $ext = null): string
    {
        if (!$file) {
            throw new RuntimeException('Template file must be set.');
        }
        if (!is_file($file)) {
            $file = $this->parseFile($file, $ext);
        }
        if (!is_file($file)) {
            $e = sprintf('Template file `%s` does not exist.', $file);

            throw new RuntimeException($e);
        }

        return realpath(str_replace('//', '/', str_replace('\\', '/', $file)));
    }

    /**
     * 分析模板真实路径.
     *
     * @throws \Exception
     */
    protected function parseFile(string $file, ?string $ext = null): string
    {
        if (preg_match('/^{(.*)}$/', $file, $matches)) {
            if (empty($matches[1])) {
                throw new Exception('Template file must be set.');
            }

            try {
                return eval($code = 'return '.$matches[1].';');
            } catch (Throwable $e) {
                $message = sprintf('Eval [%s]: %s', $code, $e->getMessage());

                throw new Exception($message);
            }
        }

        return $this->getThemePath().'/'.$file.
            ($ext ?: $this->option['suffix']);
    }

    /**
     * 获取主题路径.
     *
     * @throws \RuntimeException
     */
    protected function getThemePath(): string
    {
        if (!$this->option['theme_path']) {
            throw new RuntimeException('Theme path must be set.');
        }

        return $this->option['theme_path'];
    }
}
