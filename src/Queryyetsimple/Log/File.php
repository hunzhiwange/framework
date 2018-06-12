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

namespace Leevel\Log;

/**
 * log.file.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.05
 *
 * @version 1.0
 */
class File extends Connect implements IConnect
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'name' => 'Y-m-d H',
        'size' => 2097152,
        'path' => '',
    ];

    /**
     * 日志写入接口.
     *
     * @param array $datas
     */
    public function save(array $datas)
    {
        // 保存日志
        $this->checkSize($filepath = $this->getPath($datas[0][0]));

        // 记录到系统
        foreach ($datas as $item) {
            error_log(
                $this->formatMessage($item[1], $item[2]).PHP_EOL,
                3,
                $filepath
            );
        }
    }

    /**
     * 格式化日志信息.
     *
     * @param string $message  应该被记录的错误信息
     * @param array  $contexts
     *
     * @return string
     */
    protected function formatMessage($message, array $contexts = [])
    {
        return $message.' '.
            json_encode($contexts, JSON_UNESCAPED_UNICODE);
    }
}
