<?php

declare(strict_types=1);

namespace Leevel\View;

/**
 * html 模板处理类.
 */
class Html extends View implements IView
{
    /**
     * 视图分析器.
     */
    protected ?Parser $parser = null;

    /**
     * 解析 parse.
     */
    protected ?\Closure $parseResolver = null;

    /**
     * 配置.
     */
    protected array $option = [
        'theme_path' => '',
        'suffix' => '.html',
        'cache_path' => '',
    ];

    /**
     * {@inheritDoc}
     */
    public function display(string $file, array $vars = [], ?string $ext = null): string
    {
        $this->setVar($vars);
        $file = $this->parseDisplayFile($file, $ext);
        $cachePath = $this->compileToCache($file);

        return $this->extractVarsAndIncludeFile($cachePath);
    }

    /**
     * 设置 parser 解析回调.
     */
    public function setParseResolver(\Closure $parseResolver): void
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
        $themePath = $this->getThemePath();
        $themePath = realpath($themePath) ?: $themePath;
        if (str_starts_with($file, $themePath)) {
            $file = substr($file, \strlen($themePath) + 1).'.php';
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
            throw new \RuntimeException('Theme cache path must be set.');
        }

        return $this->option['cache_path'];
    }

    /**
     * 模板编译到缓存.
     */
    protected function compileToCache(string $file): string
    {
        $cachePath = $this->parseCachePath($file);
        if ($this->isCacheExpired($file, $cachePath)) {
            $this->parser()->doCompile($file, $cachePath);
        }

        return $cachePath;
    }

    /**
     * 解析 parser.
     *
     * @throws \RuntimeException
     */
    protected function resolverParser(): Parser
    {
        if (!$this->parseResolver) {
            throw new \RuntimeException('Html theme not set parse resolver.');
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
