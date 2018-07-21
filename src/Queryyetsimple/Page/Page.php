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
    public function __construct(int $perPage, ?int $totalRecord = null, array $option = [])
    {
        $this->perPage = $perPage;
        $this->totalRecord = $totalRecord;

        $this->option = array_merge($this->option, $option);
    }

    /**
     * 渲染分页.
     *
     * @param \Leevel\Page\IRender $render
     * @param array                $optoin
     *
     * @return string
     */
    public function render(IRender $render = null, array $option = [])
    {
        if (null === $render) {
            $render = 'Leevel\Page\\'.ucfirst($this->getRender());
            $render = new $render($this);
        }

        $result = $render->render();

        $this->clearResolve();

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
            'from'         => $this->getFirstRecord(),
            'to'           => $this->getLastRecord(),
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
    public function toJson($option = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->jsonSerialize(), $option);
    }
}
