<?php
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
namespace Queryyetsimple\Psr4;

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
class Psr4 implements IPsr4
{

    /**
     * Composer
     *
     * @var \Composer\Autoload\ClassLoader
     */
    protected $composer;

    /**
     * 沙盒路径
     *
     * @var string
     */
    protected $sandbox;

    /**
     * 系统命名空间
     *
     * @var string
     */
    protected $namespaces;

    /**
     * 短命名空间
     *
     * @var string
     */
    protected $shortNamespace;

    /**
     * 设置 composer
     *
     * @param \Composer\Autoload\ClassLoader $composer
     * @param string $sandbox
     * @param string $namespaces
     * @param string $shortNamespace
     * @return void
     */
    public function __construct(ClassLoader $composer, $sandbox, $namespaces, $shortNamespace)
    {
        $this->composer = $composer;
        $this->sandbox = $sandbox;
        $this->namespaces = $namespaces;
        $this->shortNamespace = $shortNamespace;
    }

    /**
     * 获取 composer
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function composer()
    {
        return $this->composer;
    }

    /**
     * 导入一个目录中命名空间结构
     *
     * @param string $namespaces 命名空间名字
     * @param string $package 命名空间路径
     * @param boolean $force 强制覆盖
     * @return void
     */
    public function import($namespaces, $package, $force = false)
    {
        if (! is_dir($package)) {
            return;
        }

        if ($force === false && ! is_null($this->namespaces($namespaces))) {
            return;
        }

        $packagePath = realpath($package);
        $this->composer()->setPsr4($namespaces . '\\', $packagePath);
    }

    /**
     * 获取命名空间路径
     *
     * @param string $namespaces
     * @return string|null
     */
    public function namespaces($namespaces)
    {
        $temp = explode('\\', $namespaces);
        $prefix = $this->composer()->getPrefixesPsr4();

        $path = $temp[0] . '\\';
        if (! isset($prefix[$path])) {
            return null;
        }

        $temp[0] = $prefix[$path][0];
        return implode('/', $temp);
    }

    /**
     * 根据命名空间取得文件路径
     *
     * @param string $classname
     * @return string
     */
    public function file($classname)
    {
        if (($namespaces = $this->namespaces($classname))) {
            return $namespaces . '.php';
        } else {
            return $classname . '.php';
        }
    }

    /**
     * 框架自动载入
     *
     * @param string $classname
     * @return void
     */
    public function autoload($classname)
    {
        if (strpos($classname, '\\') === false) {
            return;
        }

        $namespaces = [
            $this->namespaces,
            $this->shortNamespace
        ];

        foreach ($namespaces as $item) {
            if (strpos($classname, $item . '\\') === 0 && is_file(($sandboxPath = $this->sandbox . '/' . str_replace('\\', '/', $classname) . '.php'))) {
                return require_once $sandboxPath;
            }
        }

        if (strpos($classname, $this->shortNamespace . '\\') !== false) {
            $this->shortNamespaceMap($classname);
        }
    }

    /**
     * 框架命名空间自动关联
     *
     * @param string $classname
     * @return void
     */
    protected function shortNamespaceMap($classname)
    {
        $parentClass = str_replace($this->shortNamespace . '\\', $this->namespaces . '\\', $classname);

        if (class_exists($parentClass) || interface_exists($parentClass)) {
            $temp = explode('\\', $classname);
            $definedClass = array_pop($temp);
            $namespaces = implode('\\', $temp);

            $evals = sprintf('namespace %s; %s %s extends  \%s {}', $namespaces, class_exists($parentClass) ? 'class' : 'interface', $definedClass, $parentClass);

            eval($evals);
        }
    }
}
