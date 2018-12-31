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

namespace Leevel\Http;

/**
 * header bag.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.25
 *
 * @version 1.0
 */
class HeaderBag extends Bag
{
    /**
     * 构造函数.
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $this->add($elements);
    }

    /**
     * 格式化 header 字符串.
     *
     * @return string
     */
    public function __toString()
    {
        if (!$headers = $this->all()) {
            return '';
        }

        ksort($headers);

        $content = '';

        foreach ($headers as $name => $value) {
            $name = ucwords($name, '-');
            $content .= $name.': '.$value."\r\n";
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $elements = [])
    {
        $this->elements = [];

        $this->add($elements);
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $elements = [])
    {
        foreach ($elements as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize($key)
    {
        $key = str_replace('_', '-', strtolower($key));

        return $key;
    }
}
