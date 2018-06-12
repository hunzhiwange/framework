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

namespace Leevel\Console;

/**
 * 简单命令行参数解析.
 *
 * 典型例子
 * php cli.php app://for/bar user = name hello world  -- queue = default
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.09
 *
 * @version 1.0
 */
class Cli
{
    /**
     * 版本.
     *
     * @var string
     */
    const VERSION = '1.0';
    /**
     * 待解析参数.
     *
     * @var array
     */
    protected $argv = [];

    /**
     * 节点参数
     * like app://for/bar.
     *
     * @var string
     */
    protected $node;

    /**
     * 查询条件.
     *
     * @var array
     */
    protected $querys = [];

    /**
     * 配置参数.
     *
     * @var array
     */
    protected $options = [];

    /**
     * 构造函数.
     *
     * @param array $argv
     */
    public function __construct(array $argv = null)
    {
        if (null === $argv) {
            $argv = $GLOBALS['argv'] ?? [];
        }

        $this->argv = $argv;
    }

    /**
     * 解析命令行参数.
     */
    public function parse()
    {
        $argv = $this->argv;

        if ($argv) {
            array_shift($argv);
        }

        // 节点 app://controller/action
        if ($argv) {
            $this->node = array_shift($argv);
            $this->shortCommand();
        }

        if (!$argv) {
            return $this->data();
        }

        $argv = $this->normalizeArgv($argv);

        while (null !== ($token = array_shift($argv))) {
            if (0 === strpos($token, '--')) {
                $this->parseOption($token);
            } else {
                $this->parseQuery($token);
            }
        }

        return $this->data();
    }

    /**
     * 获取解析数据.
     *
     * @return array
     */
    public function data()
    {
        return [
            $this->node,
            $this->querys,
            $this->options,
        ];
    }

    /**
     * 获取参数节点.
     *
     * @return string
     */
    public function node()
    {
        return $this->node;
    }

    /**
     * 获取查询参数.
     *
     * @return array
     */
    public function querys()
    {
        return $this->querys;
    }

    /**
     * 获取配置参数.
     *
     * @return array
     */
    public function options()
    {
        return $this->options;
    }

    /**
     * 短命令.
     */
    protected function shortCommand()
    {
        if ('-h' === $this->node) {
            echo <<<'eot'
Usage: php cli.php app://for/bar user=name hello world --option=default

-h This help
-v Version number
eot;
            exit();
        }
        if ('-v' === $this->node) {
            echo 'QueryPHP Console Cli '.static::VERSION;
            exit();
        }
    }

    /**
     * 解析配置参数.
     *
     * @param string $token
     */
    protected function parseOption($token)
    {
        $token = substr($token, 2);

        if (false !== strpos($token, '=')) {
            list($token, $value) = explode('=', $token);
            $this->addOption($token, $value);
        } else {
            $this->addOption($token, $token);
        }
    }

    /**
     * 添加配置匹配数据.
     *
     * @param string $name
     * @param mixed  $value
     */
    protected function addOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * 解析查询参数.
     *
     * @param string $token
     */
    protected function parseQuery($token)
    {
        if (false !== strpos($token, '=')) {
            list($token, $value) = explode('=', $token);
            $this->addQuery($token, $value);
        } else {
            $this->addQuery($token, $token);
        }
    }

    /**
     * 添加查询匹配数据.
     *
     * @param string $name
     * @param mixed  $value
     */
    protected function addQuery($name, $value)
    {
        $this->querys[$name] = $value;
    }

    /**
     * 格式化参数.
     *
     * @param array $argv
     *
     * @return array
     */
    protected function normalizeArgv(array $argv)
    {
        $result = [];
        $special = ['=', '--'];

        foreach ($argv as $key => $token) {
            if (!in_array($token, $special, true) && isset($argv[$key + 1]) && '=' !== $argv[$key + 1]) {
                $token .= ' ';
            }

            $result[] = $token;
        }

        $result = explode(' ', implode('', $result));

        return $result;
    }
}
