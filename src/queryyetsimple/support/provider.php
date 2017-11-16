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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\support;

use Closure;
use BadMethodCallException;

/**
 * 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
abstract class provider
{
    
    /**
     * 是否延迟载入
     *
     * @var boolean
     */
    public static $booDefer = false;
    
    /**
     * IOC 容器
     *
     * @var \queryyetsimple\support\icontainer
     */
    protected $objContainer;
    
    /**
     * 创建一个服务容器提供者实例
     *
     * @param \queryyetsimple\support\icontainer $objContainer
     * @return void
     */
    public function __construct(icontainer $objContainer)
    {
        $this->objContainer = $objContainer;
        
        if (! static::isDeferred()) {
            $this->registerAlias();
        }
    }
    
    /**
     * 注册服务
     *
     * @return void
     */
    abstract public function register();
    
    /**
     * 注册服务别名
     *
     * @return void
     */
    public function registerAlias()
    {
        if (! static::isDeferred() && static::providers()) {
            $this->alias(static::providers());
        }
    }
    
    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [];
    }
    
    /**
     * 是否延迟载入
     *
     * @return boolean
     */
    public static function isDeferred()
    {
        return static::$booDefer;
    }
    
    /**
     * 返回 IOC 容器
     *
     * @return \queryyetsimple\support\icontainer
     */
    public function container()
    {
        return $this->objContainer;
    }
    
    /**
     * 注册到容器
     *
     * @param mixed $mixFactoryName
     * @param mixed $mixFactory
     * @param boolean $booShare
     * @return $this
     */
    public function bind($mixFactoryName, $mixFactory = null, $booShare = false)
    {
        $this->objContainer->bind($mixFactoryName, $mixFactory, $booShare);
        return $this;
    }
    
    /**
     * 注册为实例
     *
     * @param mixed $mixFactoryName
     * @param mixed $mixFactory
     * @return $this
     */
    public function instance($mixFactoryName, $mixFactory = null)
    {
        $this->objContainer->instance($mixFactoryName, $mixFactory);
        return $this;
    }
    
    /**
     * 注册单一实例
     *
     * @param scalar|array $mixFactoryName
     * @param mixed $mixFactory
     * @return $this
     */
    public function singleton($mixFactoryName, $mixFactory = null)
    {
        $this->objContainer->singleton($mixFactoryName, $mixFactory);
        return $this;
    }
    
    /**
     * 创建共享的闭包
     *
     * @param \Closure $objClosure
     * @return \Closure
     */
    public function share(Closure $objClosure)
    {
        return $this->objContainer->share($objClosure);
    }
    
    /**
     * 设置别名
     *
     * @param array|string $mixAlias
     * @param string|null|array $mixValue
     * @return $this
     */
    public function alias($mixAlias, $mixValue = null)
    {
        $this->objContainer->alias($mixAlias, $mixValue);
        return $this;
    }
    
    /**
     * 添加语言包目录
     *
     * @param mixed $mixDir
     * @return void
     */
    protected function loadI18nDir($mixDir)
    {
        if (! is_array($mixDir)) {
            $mixDir = ( array ) $mixDir;
        }
        $this->objContainer['i18n.load']->addDir($mixDir);
    }
    
    /**
     * 添加命令包命名空间
     *
     * @param mixed $mixNamespace
     * @return void
     */
    protected function loadCommandNamespace($mixNamespace)
    {
        if (! $this->objContainer->console()) {
            return;
        }
        
        $arrNamespace = [];
        
        if (! is_array($mixNamespace)) {
            $mixNamespace = ( array ) $mixNamespace;
        }
        
        foreach ($mixNamespace as $strNamespace) {
            $arrNamespace[$strNamespace] = $this->objContainer['psr4']->namespaces($strNamespace);
        }
        
        $this->objContainer['console.load']->addNamespace($arrNamespace);
    }
    
    /**
     * 缺省方法
     *
     * @param 方法名 $sMethod
     * @param 参数 $arrArgs
     * @return mixed
     */
    public function __call($sMethod, $arrArgs)
    {
        if ($sMethod == 'bootstrap') {
            return;
        }
        
        throw new BadMethodCallException(sprintf('Method %s is not exits.', $sMethod));
    }
}
