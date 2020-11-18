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

use Closure;
use RuntimeException;

/**
 * html 模板处理类.
 */
class Html extends View implements IView
{
    /**
     * 视图分析器.
     *
     * @var \Leevel\View\Parser
     */
    protected ?Parser $parser = null;

    /**
     * 解析 parse.
     *
     * @var null|\Closure
     */
    protected ?Closure $parseResolver = null;

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'theme_path'            => '',
        'suffix'                => '.html',
        'cache_path'            => '',
    ];

    /**
     * 加载视图文件.
     */
    public function display(string $file, array $vars = [], ?string $ext = null): string
    {
        $file = $this->parseDisplayFile($file, $ext);
        if ($vars) {
            $this->setVar($vars);
        }
        if (is_array($this->vars) && !empty($this->vars)) {
            extract($this->vars, EXTR_PREFIX_SAME, '_');
        }

        $cachePath = $this->parseCachePath($file);
        if ($this->isCacheExpired($file, $cachePath)) {
            $this->parser()->doCompile($file, $cachePath);
        }

        ob_start();
        include $cachePath;
        $result = ob_get_contents() ?: '';
        ob_end_clean();

        return $result;
    }

    /**
     * 设置 parser 解析回调.
     */
    public function setParseResolver(Closure $parseResolver): void
    {
        $this->parseResolver = $parseResolver;
    }

    /**
     * 获取 HTML 编译路径.
     *
     * @throws \RuntimeException
     */
    public function parseCachePath(string $file): string
    {
        $themePath = realpath($this->getThemePath());
        if (0 === strpos($file, $themePath)) {
            $file = substr($file, strlen($themePath) + 1);
        } else {
            $fileExtension = '.'.pathinfo($file, PATHINFO_EXTENSION);
            $file = ':hash/'.basename($file, $fileExtension).'.'.md5($file).'.php';
        }

        return $this->getCachePath().'/'.$file;
    }

    /**
     * 获取缓存路径.
     *
     * @throws \RuntimeException
     */
    protected function getCachePath(): string
    {
        if (!$this->option['cache_path']) {
            $e = 'Theme cache path must be set.';

            throw new RuntimeException($e);
        }

        return $this->option['cache_path'];
    }

    /**
     * 解析 parser.
     *
     * @throws \RuntimeException
     */
    protected function resolverParser(): Parser
    {
        if (!$this->parseResolver) {
            $e = 'Html theme not set parse resolver.';

            throw new RuntimeException($e);
        }

        $parseResolver = $this->parseResolver;

        return $parseResolver();
    }

    /**
     * 获取分析器.
     */
    protected function parser(): Parser
    {
        if (null !== $this->parser) {
            return $this->parser;
        }

        return $this->parser = $this->resolverParser();
    }

    /**
     * 判断缓存是否过期.
     */
    protected function isCacheExpired(string $file, string $cachePath): bool
    {
        if (!is_file($cachePath)) {
            return true;
        }

        clearstatcache();

        return filemtime($file) >= filemtime($cachePath);
    }
}
