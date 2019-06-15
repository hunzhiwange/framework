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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\View;

use Closure;
use RuntimeException;

/**
 * html 模板处理类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.18
 *
 * @version 1.0
 */
class Html extends View implements IView
{
    /**
     * 视图分析器.
     *
     * @var \Leevel\View\IParser
     */
    protected $parser;

    /**
     * 解析 parse.
     *
     * @var \Closure
     */
    protected $parseResolver;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'theme_path'            => '',
        'suffix'                => '.html',
        'cache_path'            => '',
    ];

    /**
     * 加载视图文件.
     *
     * @param string      $file    视图文件地址
     * @param array       $vars
     * @param null|string $ext     后缀
     * @param bool        $display 是否显示
     *
     * @return string|void
     */
    public function display(string $file, array $vars = [], ?string $ext = null, bool $display = true)
    {
        // 加载视图文件
        $file = $this->parseDisplayFile($file, $ext);

        // 变量赋值
        if ($vars) {
            $this->setVar($vars);
        }

        if (is_array($this->vars) && !empty($this->vars)) {
            extract($this->vars, EXTR_PREFIX_SAME, 'q_');
        }

        $cachepath = $this->getCachePath($file); // 编译文件路径

        if ($this->isCacheExpired($file, $cachepath)) { // 重新编译
            $this->parser()->doCompile($file, $cachepath);
        }

        // 返回类型
        if (false === $display) {
            ob_start();
            include $cachepath;
            $result = ob_get_contents();
            ob_end_clean();

            return $result;
        }

        include $cachepath;
    }

    /**
     * 设置 parser 解析回调.
     *
     * @param \Closure $parseResolver
     */
    public function setParseResolver(Closure $parseResolver): void
    {
        $this->parseResolver = $parseResolver;
    }

    /**
     * 获取编译路径.
     *
     * @param string $file
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function getCachePath(string $file): string
    {
        if (!$this->option['cache_path']) {
            $e = 'Theme cache path must be set.';

            throw new RuntimeException($e);
        }

        $file = str_replace('//', '/', str_replace('\\', '/', $file));

        $file = basename($file, '.'.pathinfo($file, PATHINFO_EXTENSION)).'.'.md5($file).'.php';

        return $this->option['cache_path'].'/'.$file;
    }

    /**
     * 解析 parser.
     *
     * @throws \RuntimeException
     *
     * @return \Leevel\View\IParser
     */
    protected function resolverParser(): IParser
    {
        if (!$this->parseResolver) {
            $e = 'Html theme not set parse resolver.';

            throw new RuntimeException($e);
        }

        return call_user_func($this->parseResolver);
    }

    /**
     * 获取分析器.
     *
     * @return \Leevel\View\IParser
     */
    protected function parser(): IParser
    {
        if (null !== $this->parser) {
            return $this->parser;
        }

        return $this->parser = $this->resolverParser();
    }

    /**
     * 判断缓存是否过期
     *
     * @param string $file
     * @param string $cachepath
     *
     * @return bool
     */
    protected function isCacheExpired(string $file, string $cachepath): bool
    {
        if (!is_file($cachepath)) {
            return true;
        }

        if (filemtime($file) >= filemtime($cachepath)) {
            return true;
        }

        return false;
    }
}
