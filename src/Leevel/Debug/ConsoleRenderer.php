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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Debug;

/**
 * Console 渲染.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.20
 *
 * @version 1.0
 */
class ConsoleRenderer
{
    /**
     * debug 管理.
     *
     * @var \Leevel\Debug\Debug
     */
    protected Debug $debugBar;

    /**
     * 构造函数.
     *
     * @param \Leevel\Debug\Debug $debugBar
     */
    public function __construct(Debug $debugBar)
    {
        $this->debugBar = $debugBar;
    }

    /**
     * 渲染数据.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->console($this->debugBar->getData());
    }

    /**
     * 返回输出到浏览器.
     *
     * @param array $data
     *
     * @return string
     */
    protected function console(array $data): string
    {
        $content = [];
        $content[] = '<script type="text/javascript">
console.log( \'%cThe PHP Framework For Code Poem As Free As Wind %c(http://www.queryphp.com)\', \'font-weight: bold;color: #06359a;\', \'color: #02d629;\' );';

        foreach ($data as $key => $item) {
            if (is_string($key)) {
                $content[] = 'console.log(\'\');';
                $content[] = 'console.log(\'%c '.$key.'\', \'color: blue; background: #045efc; color: #fff; padding: 8px 15px; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px;\');';
                $content[] = 'console.log(\'\');';
            }

            if ($item) {
                $content[] = 'console.log('.json_encode($item, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).');';
            }
        }

        $content[] = '</script>';

        return implode('', $content);
    }
}
