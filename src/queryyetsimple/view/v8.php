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
namespace queryyetsimple\view;

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
class v8 extends aconnect implements iconnect
{

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
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
    protected  $objV8js;

    /**
     * 自定义错误
     * 
     * @var callable
     */
    protected $calErrorHandler;

    /**
     * 构造函数
     *
     * @param array $arrOption
     * @return void
     */
    public function __construct(array $arrOption = [])
    {
        parent::__construct($arrOption);

        $this->objV8js = new V8Js('$');

        foreach(['base', 'ddd', 'html', 'load', 'module'] as $strInit) {
            $this->{'init' . ucwords($strInit)}();
        }
    }

    /**
     * 返回 V8js
     *
     * @return \V8js
     */
    public function getV8js(array $arrOption = [])
    {
        return $this->objV8js;
    }

    /**
     * 加载视图文件
     *
     * @param string $sFile 视图文件地址
     * @param boolean $bDisplay 是否显示
     * @param string $strExt 后缀
     * @param string $sTargetCache 主模板缓存路径
     * @param string $sMd5 源文件地址 md5 标记
     * @return string
     */
    public function display(string $sFile = null, bool $bDisplay = true, string $strExt = '', string $sTargetCache = '', string $sMd5 = '')
    {
        // 加载视图文件
        $sFile = $this->parseDisplayFile($sFile, $strExt);

        // 传递变量
        foreach($this->arrVar as $strKey => $mixValue) {
            $this->objV8js->$strKey = $mixValue;
        }

        $strSource = file_get_contents($sFile);

        // 返回类型
        if ($bDisplay === false) {
            return $this->select($strSource);
        } else {
            $this->execute($strSource);
        }
    }
    
    /**
     * 执行 js 并返回输入文本
     *
     * @param string $strJs
     * @return string
     */
    public function select(string $strJs) {
        try {
            ob_start();
            $this->objV8js->executeString($strJs);
            return ob_get_clean();
        } catch (V8JsException $oE) {
            if ($this->calErrorHandler) {
                call_user_func($this->calErrorHandler, $oE);
            } else {
                throw $oE;
            }
        }
    }

    /**
     * 执行 js
     *
     * @param string $strJs
     * @return mixed
     */
    public function execute(string $strJs) {
        try {
            return $this->objV8js->executeString($strJs);
        } catch (V8JsException $oE) {
            if ($this->calErrorHandler) {
                call_user_func($this->calErrorHandler, $oE);
            } else {
                throw $oE;
            }
        }  
    }

    /**
     * 自定义异常
     *
     * @param callable $calErrorHandler
     * @return $this
     */
    public function setErrorHandler(callable $calErrorHandler) {
        $this->calErrorHandler = $calErrorHandler;
        return $this;
    }

    /**
     * initBase
     * 
     * @return void
     */
    protected function initBase() {
        $strConsole = <<<'EOT'
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
                    $.$ddd(arguments[0]);
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
        $this->execute($strConsole);

        unset($strConsole);  
    }

    /**
     * initDdd
     *
     * @return void
     */
    public function initDdd()
    {
        $this->objV8js->{'$ddd'} = function($strMessage) {
            ddd($strMessage);
        };

        $this->execute('this.ddd = this.$ddd = $.$ddd;');   
    }

    /**
     * initHtml
     *
     * @return void
     */
    public function initHtml()
    {
        $this->objV8js->{'$html'} = function($strPath, $strExt = '.html') {
            $sFile = $this->parseDisplayFile($strPath, $strExt);
            return file_get_contents($sFile);
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
        $this->objV8js->{'$load'} = function($strPackage) {
            $strPackage .= 'Package';

            if (! method_exists($this, $strPackage)) {
                throw new RuntimeException('Package is not preset, we just support vue and art.');
            }

            $this->$strPackage();
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
        $this->objV8js->setModuleNormaliser(function($strBase, $strModule) {
            try {
                $strModule = $this->parseDisplayFile($strModule);
            } catch (Exception $oE) {
                $strModule = $this->parseDisplayFile($strModule.'/index');
            }
            
            return ['', $strModule];
        });

        $this->objV8js->setModuleLoader(function($strModule) {
            return file_get_contents($strModule);
        });
    }

    /**
     * 初始化 vue
     * 
     * @return void
     */
    protected function vuePackage() {
        $strVue = $this->getOption('vue_path');
        $strRenderer = $this->getOption('vue_renderer');

        if (! is_file($strVue)) {
            throw new RuntimeException(sprintf('Vue path %s is not exits, please use npm install.', $strVue));
        }

        if (! is_file($strRenderer)) {
            throw new RuntimeException(sprintf('Vue renderer %s is not exits, please use npm install.', $strRenderer));
        }

        $this->execute('delete this.window; this.global = { process: { env: { VUE_ENV: "server", NODE_ENV: "production" } } };');

        $this->execute(file_get_contents($strVue));

        $this->execute(file_get_contents($strRenderer));
    }

    /**
     * 初始化 art
     * 
     * @return void
     */
    protected function artPackage() {
        $strArt = $this->getOption('art_path');

        if (! is_file($strArt)) {
            throw new RuntimeException(sprintf('Art path %s is not exits, please use npm install.', $strArt));
        }

        $this->execute('this.window = null;');
        $this->execute(file_get_contents($strArt));
        $this->execute('delete this.window;');
    }
}
