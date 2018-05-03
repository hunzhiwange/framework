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
namespace Leevel\Support\Debug;

/**
 * 调试
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.05
 * @version 1.0
 */
class Console
{

    /**
     * 记录调试信息
     * SQL 记录，加载文件等等
     *
     * @param array $log
     * @return string
     */
    public static function trace(array $log)
    {
        // swoole http server 可以调试
        if (PHP_SAPI == 'cli' && ! (isset($_SERVER['SERVER_SOFTWARE']) && $_SERVER['SERVER_SOFTWARE'] == 'swoole-http-server')) {
            return;
        }

        // ajax 不调试
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return;
        }

        $trace = static::normalizeLog($log);

        $include = get_included_files();
        $trace['LOADED.FILE' . ' (' . count($include) . ')'] = implode('\n', $include);

        return static::getOutputToConsole($trace);
    }

    /**
     * JSON 记录调试信息
     * SQL 记录，加载文件等等
     *
     * @param array $log
     * @return array
     */
    public static function jsonTrace(array $log)
    {
        $trace = static::normalizeLog($log);

        $include = get_included_files();
        $trace['LOADED.FILE' . ' (' . count($include) . ')'] = $include;

        return $trace;
    }

    /**
     * 返回输出到浏览器
     *
     * @param array $trace
     * @return string
     */
    protected static function getOutputToConsole(array $trace): string
    {
        $content = [];

        $content[] = '<script type="text/javascript">
console.log( \'%cThe PHP Framework For Code Poem As Free As Wind %c(http://www.queryphp.com)\', \'font-weight: bold;color: #06359a;\', \'color: #02d629;\' );';

        foreach ($trace as $key => $item) {
            if (is_string($key)) {
                $content[] = 'console.log(\'\');';
                
                $content[] = 'console.log(\'%c ' . $key . '\', \'color: blue; background: #045efc; color: #fff; padding: 8px 15px; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px;\');';

                $content[] = 'console.log(\'\');';
            }

            if ($item) {
                $content[] = 'console.log(\'%c' . $item . '\', \'color: gray;\');';
            }
        }

        $content[] = '</script>';

        return implode('', $content);
    }

    /**
     * 格式化日志信息
     *
     * @param array $log
     * @return array
     */
    protected static function normalizeLog(array $log) {
        $result = [];

        foreach ($log as $type => $item) {
            $result[strtoupper($type) . '.LOG' . ' (' . count($item) . ')'] = implode('\n', array_map(function ($item) {
                return static::formatMessage($item);
            }, $item));
        }

        return $result; 
    }

    /**
     * 格式化日志信息
     *
     * @param array $item
     * @return string
     */
    protected static function formatMessage($item)
    {
        return addslashes($item[0] . ' ' . json_encode($item[1], JSON_UNESCAPED_UNICODE));
    }
}
