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
namespace queryyetsimple\support;

use RuntimeException;
use Composer\Autoload\ClassLoader;

/**
 * psr4 自动载入规范
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.17
 * @version 1.0
 */
class psr4 implements ipsr4
{

    /**
     * Composer
     *
     * @var \Composer\Autoload\ClassLoader
     */
    protected $objComposer;

    /**
     * 沙盒路径
     *
     * @var string
     */
    protected $strSandboxPath;

    /**
     * 短命名空间
     *
     * @var string
     */
    protected $strShortNamespace;

    /**
     * 框架自定义命名空间
     *
     * @var string
     */
    const DEFAULT_NAMESPACE = 'queryyetsimple';

    /**
     * 设置 composer
     *
     * @param \Composer\Autoload\ClassLoader $objComposer
     * @param string $strSandboxPath
     * @param string $strShortNamespace
     * @return void
     */
    public function __construct(ClassLoader $objComposer, $strSandboxPath, $strShortNamespace)
    {
        $this->objComposer = $objComposer;
        $this->strSandboxPath = $strSandboxPath;
        $this->strShortNamespace = $strShortNamespace;
    }

    /**
     * 获取 composer
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function composer()
    {
        return $this->objComposer;
    }

    /**
     * 导入一个目录中命名空间结构
     *
     * @param string $sNamespace 命名空间名字
     * @param string $sPackage 命名空间路径
     * @return void
     */
    public function import($sNamespace, $sPackage)
    {
        if (! is_dir($sPackage)) {
            return;
        }
        $sPackagePath = realpath($sPackage);
        $this->composer()->setPsr4($sNamespace . '\\', $sPackagePath);
    }

    /**
     * 获取命名空间路径
     *
     * @param string $sNamespace
     * @return string|null
     */
    public function namespaces($sNamespace)
    {
        $arrNamespace = explode('\\', $sNamespace);
        $arrPrefix = $this->composer()->getPrefixesPsr4();

        if (! isset($arrPrefix[$arrNamespace[0] . '\\'])) {
            return null;
        }

        $arrNamespace[0] = $arrPrefix[$arrNamespace[0] . '\\'][0];
        return implode('/', $arrNamespace);
    }

    /**
     * 根据命名空间取得文件路径
     *
     * @param string $strFile
     * @return string
     */
    public function file($strFile)
    {
        if (($strNamespace = $this->namespaces($strFile))) {
            return $strNamespace . '.php';
        } else {
            return $strFile . '.php';
        }
    }

    /**
     * 框架自动载入
     *
     * @param string $strClass
     * @return void
     */
    public function autoload($strClass)
    {
        if (strpos($strClass, '\\') === false) {
            return;
        }

        foreach ([
            static::DEFAULT_NAMESPACE,
            $this->strShortNamespace
        ] as $strNamespace) {
            if (strpos($strClass, $strNamespace . '\\') === 0 && is_file(($strSandbox = $this->strSandboxPath . '/' . str_replace('\\', '/', $strClass) . '.php'))) {
                require_once $strSandbox;
                return;
            }
        }

        if (strpos($strClass, $this->strShortNamespace . '\\') !== false) {
            $this->shortNamespaceMap($strClass);
        }
    }

    /**
     * 框架命名空间自动关联
     *
     * @param string $strClass
     * @return void
     */
    protected function shortNamespaceMap($strClass)
    {
        $strTryMapClass = str_replace($this->strShortNamespace . '\\', static::DEFAULT_NAMESPACE . '\\', $strClass);
        
        if (class_exists($strTryMapClass) || interface_exists($strTryMapClass)) {
            $arrClass = explode('\\', $strClass);
            $strDefinedClass = array_pop($arrClass);
            $strNamespace = implode('\\', $arrClass);

            $strSandboxContent = sprintf('namespace %s; %s %s extends  \%s {}', $strNamespace, class_exists($strTryMapClass) ? 'class' : 'interface', $strDefinedClass, $strTryMapClass);

            eval($strSandboxContent);
        }
    }
}
