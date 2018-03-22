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
namespace Queryyetsimple\Log;

use Queryyetsimple\Option\TClass;

/**
 * aconnect 驱动抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
abstract class Connect
{
    use TClass;

    /**
     * 构造函数
     *
     * @param array $option
     * @return void
     */
    public function __construct(array $option = [])
    {
        $this->options($option);
    }

    /**
     * 验证日志文件大小
     *
     * @param string $filepath
     * @return void
     */
    protected function checkSize($filepath)
    {
        $filedir = dirname($filepath);

        // 如果不是文件，则创建
        if (! is_file($filepath) && ! is_dir($filedir) && ! mkdir($filedir, 0777, true)) {
            throw new RuntimeException(sprintf('Unable to create log file：%s.', $filepath));
        }

        // 检测日志文件大小，超过配置大小则备份日志文件重新生成
        if (is_file($filepath) && floor($this->getOption('size')) <= filesize($filepath)) {
            rename($filepath, $filedir . '/' . date('Y-m-d H.i.s') . '_' . basename($filepath));
        }
    }

    /**
     * 获取日志路径
     *
     * @param string $level
     * @param string $filepath
     * @return string
     */
    protected function getPath($level = '')
    {
        // 不存在路径，则直接使用项目默认路径
        if (empty($filepath)) {
            if (! $this->getOption('path')) {
                throw new RuntimeException('Default path for log has not specified.');
            }
            $filepath = $this->getOption('path') . '/' . ($level ? $level . '/' : '') . date($this->getOption('name')) . ".log";
        }

        return $filepath;
    }
}
