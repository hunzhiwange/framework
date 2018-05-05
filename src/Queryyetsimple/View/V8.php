<?php declare(strict_types=1);
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

use V8Js;
use Exception;
use V8JsException;
use RuntimeException;

/**
 * v8 模板处理类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.01.10
 * @version 1.0
 */
class V8 extends Connect implements IConnect
{

    /**
     * 配置
     *
     * @var array
     */
    protected $option = [
        'controller_name' => 'index',
        'action_name' => 'index',
        'controlleraction_depr' => '_',
        'theme_name' => 'default',
        'theme_path' => '',
        'theme_path_default' => '',
        'suffix' => '.js',

        // node_modules/vue/dist/vue.js
        'vue_path' => '',

        // node_modules/vue-server-renderer/basic.js
        'vue_renderer' => '',

        // node_modules/art-template/lib/template-web.js
        'art_path' => ''
    ];

    /**
     * v8js
     * 
     * @var \V8Js
     */
    protected  $v8js;

    /**
     * 自定义错误
     * 
     * @var callable
     */
    protected $errorHandler;

    /**
     * 构造函数
     *
     * @param array $option
     * @link http://php.net/manual/zh/book.v8js.php
     * @return void
     */
    public function __construct(array $option = [])
    {
        if (! extension_loaded('v8js')) {
            throw new RuntimeException('Please install php v8js extension.');
        }

        parent::__construct($option);

        $this->v8js = new V8Js('$');

        foreach(['base', 'dd', 'html', 'load', 'module'] as $item) {
            $this->{'init' . ucwords($item)}();
        }
    }

    /**
     * 返回 V8js
     *
     * @return \V8js
     */
    public function getV8js(array $option = [])
    {
        return $this->v8js;
    }

    /**
     * 加载视图文件
     *
     * @param string $file 视图文件地址
     * @param array $vars
     * @param string $ext 后缀
     * @param boolean $display 是否显示
     * @return string
     */
    public function display(?string $file = null, array $vars = [], ?string $ext = null, bool $display = true)
    {
        // 加载视图文件
        $file = $this->parseDisplayFile($file, $ext);

        // 传递变量
        if ($vars) {
            $this->setVar($vars);
        }

        foreach($this->vars as $key => $value) {
            $this->v8js->$key = $value;
        }

        $source = file_get_contents($file);

        // 返回类型
        if ($display === false) {
            return $this->select($source);
        } else {
            $this->execute($source);
        }
    }
    
    /**
     * 执行 js 并返回输入文本
     *
     * @param string $js
     * @return string
     */
    public function select(string $js) {
        try {
            ob_start();
            $this->v8js->executeString($js);
            return ob_get_clean();
        } catch (V8JsException $e) {
            if ($this->errorHandler) {
                call_user_func($this->errorHandler, $e);
            } else {
                throw $e;
            }
        }
    }

    /**
     * 执行 js
     *
     * @param string $js
     * @return mixed
     */
    public function execute(string $js) {
        try {
            return $this->v8js->executeString($js);
        } catch (V8JsException $e) {
            if ($this->errorHandler) {
                call_user_func($this->errorHandler, $e);
            } else {
                throw $e;
            }
        }  
    }

    /**
     * 自定义异常
     *
     * @param callable $errorHandler
     * @return $this
     */
    public function setErrorHandler(callable $errorHandler) {
        $this->errorHandler = $errorHandler;
        return $this;
    }

    /**
     * initBase
     * 
     * @return void
     */
    protected function initBase() {
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
     * initDd
     *
     * @return void
     */
    public function initDd()
    {
        $this->v8js->{'$dd'} = function($message) {
            dd($message);
        };

        $this->execute('this.dd = this.$dd = $.$dd;');   
    }

    /**
     * initHtml
     *
     * @return void
     */
    public function initHtml()
    {
        $this->v8js->{'$html'} = function($path, $ext = '.html') {
            $file = $this->parseDisplayFile($path, $ext);
            return file_get_contents($file);
        };

        $this->execute('this.html = this.$html = $.$html;');   
    }

    /**
     * initLoad
     *
     * @return void
     */
    public function initLoad()
    {
        $this->v8js->{'$load'} = function($package) {
            $package .= 'Package';

            if (! method_exists($this, $package)) {
                throw new RuntimeException('Package is not preset, we just support vue and art.');
            }

            $this->$package();
        };

        $this->execute('this.load = this.$load = $.$load;');   
    }

    /**
     * initModule
     *
     * @return void
     */
    public function initModule()
    {
        $this->v8js->setModuleNormaliser(function($base, $module) {
            try {
                $module = $this->parseDisplayFile($module);
            } catch (Exception $e) {
                $module = $this->parseDisplayFile($module.'/index');
            }
            
            return ['', $module];
        });

        $this->v8js->setModuleLoader(function($module) {
            return file_get_contents($module);
        });
    }

    /**
     * 初始化 vue
     * 
     * @return void
     */
    protected function vuePackage() {
        $vue = $this->getOption('vue_path');
        $renderer = $this->getOption('vue_renderer');

        if (! is_file($vue)) {
            throw new RuntimeException(sprintf('Vue path %s is not exits, please use npm install.', $vue));
        }

        if (! is_file($renderer)) {
            throw new RuntimeException(sprintf('Vue renderer %s is not exits, please use npm install.', $renderer));
        }

        $this->execute('delete this.window; this.global = { process: { env: { VUE_ENV: "server", NODE_ENV: "production" } } };');

        $this->execute(file_get_contents($vue));

        $this->execute(file_get_contents($renderer));
    }

    /**
     * 初始化 art
     * 
     * @return void
     */
    protected function artPackage() {
        $art = $this->getOption('art_path');

        if (! is_file($art)) {
            throw new RuntimeException(sprintf('Art path %s is not exits, please use npm install.', $art));
        }

        $this->execute('this.window = null;');
        $this->execute(file_get_contents($art));
        $this->execute('delete this.window;');
    }
}
