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
namespace Queryyetsimple\Page;

use Countable;
use ArrayAccess;
use JsonSerializable;
use Queryyetsimple\Support\{
    IJson,
    IArray
};

/**
 * 分页处理
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.14
 * @version 1.0
 */
class Page extends Connect implements IPage, IJson, IArray, Countable, ArrayAccess, JsonSerializable
{

    /**
     * 构造函数
     *
     * @param int $intPerPage
     * @param int $intTotalRecord
     * @param array $arrOption
     * @return void
     */
    public function __construct($intPerPage, $intTotalRecord = null, array $arrOption = [])
    {
        $this->intPerPage = $intPerPage;
        $this->intTotalRecord = $intTotalRecord;
        $this->options($arrOption);
    }

    /**
     * 渲染分页
     *
     * @param \Queryyetsimple\Page\IRender $objRender
     * @return string
     */
    public function render(IRender $objRender = null)
    {
        if (is_null($objRender)) {
            $objRender = 'Queryyetsimple\Page\\' . $this->getRender();
            $objRender = new $objRender($this);
        }
        return $objRender->render();
    }

    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'per_page' => $this->getPerPage(),
            'current_page' => $this->getCurrentPage(),
            'total_page' => $this->getTotalPage(),
            'total_record' => $this->getTotalRecord(),
            'total_macro' => $this->isTotalMacro(),
            'from' => $this->getFirstRecord(),
            'to' => $this->getLastRecord()
        ];
    }

    /**
     * 实现 JsonSerializable::jsonSerialize
     *
     * @return boolean
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * 对象转 JSON
     *
     * @param integer $option
     * @return string
     */
    public function toJson($option = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->jsonSerialize(), $option);
    }
}

if (! function_exists('__')) {
    /**
     * lang
     *
     * @param array $arr
     * @return string
     */
    function __(...$arr)
    {
        return count($arr) == 0 ? '' : (count($arr) > 1 ? sprintf(...$arr) : $arr[0]);
    }
}
