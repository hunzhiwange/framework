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
 * twig 模板处理类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.01.15
 *
 * @version 1.0
 */
class Twig extends Connect implements IConnect
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
     * @var callable
     */
    protected $parseResolver;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'development'           => false,
        'controller_name'       => 'index',
        'action_name'           => 'index',
        'controlleraction_depr' => '_',
        'theme_name'            => 'default',
        'theme_path'            => '',
        'theme_path_default'    => '',
        'suffix'                => '.twig',
    ];

    /**
     * 加载视图文件.
     *
     * @param string $file    视图文件地址
     * @param array  $vars
     * @param string $ext     后缀
     * @param bool   $display 是否显示
     *
     * @return string
     */
    public function display(?string $file = null, array $vars = [], ?string $ext = null, bool $display = true)
    {
        // 加载视图文件
        $file = $this->parseDisplayFile($file, $ext);

        // 变量赋值
        if ($vars) {
            $this->setVar($vars);
        }

        // 返回类型
        if (false === $display) {
            return $this->renderFile($file);
        }
        echo $this->renderFile($file);
    }

    /**
     * 设置 parse 解析回调.
     *
     * @param callable $parseResolver
     */
    public function setParseResolver(callable $parseResolver)
    {
        $this->parseResolver = $parseResolver;
    }

    /**
     * 解析 parse.
     *
     * @return \Leevel\View\IParser
     */
    public function resolverParser()
    {
        if (!$this->parseResolver) {
            throw new RuntimeException('Twig theme not set parse resolver');
        }

        return call_user_func($this->parseResolver);
    }

    /**
     * 获取分析器.
     *
     * @return \Leevel\View\IParser
     */
    public function parser()
    {
        if (null !== $this->parser) {
            return $this->parser;
        }

        return $this->parser = $this->resolverParser();
    }

    /**
     * 渲染模板
     *
     * @param string $file
     *
     * @return string
     */
    protected function renderFile(string $file)
    {
        $this->parser();

        $loader = $this->parser->getLoader();
        $loader->setPaths(dirname($file));
        $this->parser->setLoader($loader);

        $template = $this->parser->load(basename($file));

        return $template->render($this->vars);
    }
}
