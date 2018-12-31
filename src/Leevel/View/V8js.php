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

use RuntimeException;
use V8Js as V8Jss;

/**
 * v8js 模板处理类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.01.10
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class V8js extends Connect implements IConnect
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'theme_path'            => '',
        'suffix'                => '.js',

        // node_modules/vue/dist/vue.js
        'vue_path' => 'node_modules/vue/dist/vue.js',

        // node_modules/vue-server-renderer/basic.js
        'vue_renderer' => 'node_modules/vue-server-renderer/basic.js',

        // node_modules/art-template/lib/template-web.js
        'art_path' => 'node_modules/art-template/lib/template-web.js',
    ];

    /**
     * v8js.
     *
     * @var \V8Js
     */
    protected $v8js;

    /**
     * 构造函数.
     *
     * @param array $option
     *
     * @see http://php.net/manual/zh/book.v8js.php
     */
    public function __construct(array $option = [])
    {
        if (!extension_loaded('v8js')) {
            throw new RuntimeException('Please install php v8js extension.');
        }

        parent::__construct($option);

        $this->v8js = new V8Jss('$');

        foreach (['base', 'dd', 'dump', 'echo', 'html', 'load', 'module'] as $item) {
            $this->{'init'.ucwords($item)}();
        }
    }

    /**
     * 返回 V8js.
     *
     * @return \V8js
     */
    public function getV8js(): V8Jss
    {
        return $this->v8js;
    }

    /**
     * 加载视图文件.
     *
     * @param string $file    视图文件地址
     * @param array  $vars
     * @param string $ext     后缀
     * @param bool   $display 是否显示
     *
     * @return string|void
     */
    public function display(string $file, array $vars = [], ?string $ext = null, bool $display = true)
    {
        // 加载视图文件
        $file = $this->parseDisplayFile($file, $ext);

        // 传递变量
        if ($vars) {
            $this->setVar($vars);
        }

        foreach ($this->vars as $key => $value) {
            $this->v8js->{$key} = $value;
        }

        $source = file_get_contents($file);

        // 返回类型
        if (false === $display) {
            return $this->select($source);
        }

        return $this->execute($source);
    }

    /**
     * 执行 js 并返回输入文本.
     *
     * @param string $js
     *
     * @return string
     */
    public function select(string $js): string
    {
        ob_start();

        $this->v8js->executeString($js);

        return ob_get_clean();
    }

    /**
     * 执行 js.
     *
     * @param string $js
     *
     * @return mixed
     */
    public function execute(string $js)
    {
        return $this->v8js->executeString($js);
    }

    /**
     * initDd.
     * 调试会导致 cli 中断.
     *
     * @codeCoverageIgnore
     */
    public function initDd(): void
    {
        $this->v8js->{'$dd'} = function ($message) {
            dd($message);
        };

        $this->execute('this.dd = this.$dd = $.$dd;');
    }

    /**
     * initDump.
     */
    public function initDump(): void
    {
        $this->v8js->{'$dump'} = function ($message) {
            var_dump($message);
        };

        $this->execute('this.dump = this.$dump = $.$dump;');
    }

    /**
     * initEcho.
     */
    public function initEcho(): void
    {
        $this->v8js->{'$echo'} = function ($message) {
            echo $message;
        };

        $this->execute('this.echo = this.$echo = $.$echo;');
    }

    /**
     * initHtml.
     */
    public function initHtml(): void
    {
        $this->v8js->{'$html'} = function ($path, $ext = '.html') {
            $file = $this->parseDisplayFile($path, $ext);

            return file_get_contents($file);
        };

        $this->execute('this.html = this.$html = $.$html;');
    }

    /**
     * initLoad.
     */
    public function initLoad(): void
    {
        $this->v8js->{'$load'} = function ($package) {
            $package .= 'Package';

            if (!method_exists($this, $package)) {
                throw new RuntimeException(
                    'Package is not preset, we just support vue and art.'
                );
            }

            $this->{$package}();
        };

        $this->execute('this.load = this.$load = $.$load;');
    }

    /**
     * initModule.
     */
    public function initModule(): void
    {
        $this->v8js->setModuleNormaliser(function ($base, $module) {
            try {
                $module = $this->parseDisplayFile($module);
            } catch (RuntimeException $e) {
                $module = $this->parseDisplayFile($module.'/index');
            }

            return ['', $module];
        });

        $this->v8js->setModuleLoader(function ($module) {
            return file_get_contents($module);
        });
    }

    /**
     * initBase.
     */
    protected function initBase(): void
    {
        $console = <<<'EOT'
/*!
 * console.js v0.2.0 (https://github.com/yanhaijing/console.js)
 * Copyright 2013 yanhaijing. All Rights Reserved
 * Licensed under MIT (https://github.com/yanhaijing/console.js/blob/master/MIT-LICENSE.txt)
 */
;(function(g) {
    'use strict';
    var _console = g.console || {};
    var methods = ['assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'exception', 'error', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'profile', 'profileEnd', 'table', 'time', 'timeEnd', 'timeStamp', 'trace', 'warn'];

    var console = {version: '0.2.0'};
    var key;
    for(var i = 0, len = methods.length; i < len; i++) {
        key = methods[i];
        console[key] = function (key) {
            var method = key;
            return function () {
                if (method == 'log') {
                    $.$dd(arguments[0]);
                } else {
                    print(arguments[0]);
                    print('<br />');
                }
            };           
        }(key);
    }
    
    g.console = console;
}(this));
EOT;
        $this->execute($console);

        unset($console);
    }

    /**
     * 初始化 vue.
     */
    protected function vuePackage(): void
    {
        $vue = $this->option['vue_path'];
        $renderer = $this->option['vue_renderer'];

        if (!is_file($vue)) {
            throw new RuntimeException(
                sprintf('Vue path %s is not exits, please use npm install.', $vue)
            );
        }

        if (!is_file($renderer)) {
            throw new RuntimeException(
                sprintf('Vue renderer %s is not exits, please use npm install.', $renderer)
            );
        }

        $this->execute(
            'delete this.window; this.global = { process: { env: { VUE_ENV: "server", NODE_ENV: "production" } } };'
        );

        $this->execute(file_get_contents($vue));

        $this->execute(file_get_contents($renderer));
    }

    /**
     * 初始化 art.
     */
    protected function artPackage(): void
    {
        $art = $this->option['art_path'];

        if (!is_file($art)) {
            throw new RuntimeException(
                sprintf('Art path %s is not exits, please use npm install.', $art)
            );
        }

        $this->execute('this.window = null;');
        $this->execute(file_get_contents($art));
        $this->execute('delete this.window;');
    }
}
