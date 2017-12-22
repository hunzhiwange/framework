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
namespace queryyetsimple\page;

use Countable;
use ArrayAccess;
use JsonSerializable;
use queryyetsimple\support\ijson;
use queryyetsimple\support\iarray;

/**
 * 分页处理
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.14
 * @version 1.0
 */
class page extends apage implements ipage, Countable, ArrayAccess, JsonSerializable, ijson, iarray
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
     * @param \queryyetsimple\page\irender $objRender
     * @return string
     */
    public function render(irender $objRender = null)
    {
        if (is_null($objRender)) {
            $objRender = 'queryyetsimple\page\\' . $this->getRender();
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
            'total_infinity' => $this->isTotalInfinity(),
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
     * @param integer $intOption
     * @return string
     */
    public function toJson($intOption = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->jsonSerialize(), $intOption);
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
