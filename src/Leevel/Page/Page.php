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

namespace Leevel\Page;

use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use RuntimeException;

/**
 * 分页处理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.14
 *
 * @version 1.0
 */
class Page extends Connect implements IPage, IJson, IArray, JsonSerializable
{
    /**
     * 构造函数.
     *
     * @param int   $perPage
     * @param int   $totalRecord
     * @param array $option
     */
    public function __construct(?int $perPage = null, ?int $totalRecord = null, array $option = [])
    {
        $this->perPage = $perPage;
        $this->totalRecord = $totalRecord;

        $this->option = array_merge($this->option, $option);
    }

    /**
     * 渲染分页.
     *
     * @param null|\Leevel\Page\IRender|string $render
     * @param array                            $option
     *
     * @return string
     */
    public function render($render = null, array $option = [])
    {
        $option = array_merge($this->option['render_option'], $option);

        if (null === $render || is_string($render)) {
            $render = $render ?: $this->getRender();
            $render = 'Leevel\Page\\'.ucfirst($render);
            $render = new $render($this);
        } elseif (!$render instanceof IRender) {
            throw new RuntimeException('Unsupported render type.');
        }

        $result = $render->render($option);

        $this->resolveUrl = null;

        return $result;
    }

    /**
     * 对象转数组.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'per_page'     => $this->getPerPage(),
            'current_page' => $this->getCurrentPage(),
            'total_page'   => $this->getTotalPage(),
            'total_record' => $this->getTotalRecord(),
            'total_macro'  => $this->isTotalMacro(),
            'from'         => $this->getFromRecord(),
            'to'           => $this->getToRecord(),
        ];
    }

    /**
     * 实现 JsonSerializable::jsonSerialize.
     *
     * @return bool
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * 对象转 JSON.
     *
     * @param int $option
     *
     * @return string
     */
    public function toJson($option = null)
    {
        if (null === $option) {
            $option = JSON_UNESCAPED_UNICODE;
        }

        return json_encode($this->jsonSerialize(), $option);
    }
}
