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
namespace Queryyetsimple\Support\Debug;

use RuntimeException;

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
     * @param string $tracepath
     * @param array $log
     * @return string
     */
    public static function trace(string $tracepath, array $log)
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

        if (! is_file($tracepath)) {
            throw new RuntimeException(sprintf('Trace file %s is not exits.', $tracepath));
        }

        ob_start();
        require_once $tracepath;
        $contents = ob_get_contents();
        ob_end_clean();
        
        return $contents;
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
