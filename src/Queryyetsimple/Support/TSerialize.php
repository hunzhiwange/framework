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

namespace Leevel\Support;

use ReflectionClass;
use ReflectionProperty;

/**
 * 对象序列化.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.04
 *
 * @version 1.0
 */
trait TSerialize
{
    /**
     * 序列化模型.
     */
    public function __sleep()
    {
        $arrProps = (new ReflectionClass($this))->getProperties();

        foreach ($arrProps as $oProp) {
            $oProp->setValue($this, $this->setAndReturnSerializeProp($this->getPropertySource($oProp), $oProp->getName()));
        }

        return $this->setAndReturnSerializeFilter(array_map(function ($oProp) {
            return $oProp->getName();
        }, $arrProps));
    }

    /**
     * 反序列化模型.
     */
    public function __wakeup()
    {
        foreach ((new ReflectionClass($this))->getProperties() as $oProp) {
            $oProp->setValue($this, $this->getSerializeProp($this->getPropertySource($oProp), $oProp->getName()));
        }
    }

    /**
     * 设置序列化的属性.
     *
     * @param array $arrProp
     *
     * @return array
     */
    protected function setAndReturnSerializeFilter($arrProp)
    {
        if (($strMethod = 'setSerializeFilterProp') && method_exists($this, $strMethod)) {
            $this->{$strMethod}($arrProp);
        }

        return $arrProp;
    }

    /**
     * 设置序列化的值并返回.
     *
     * @param mixed  $mixValue
     * @param string $strName
     *
     * @return mixed
     */
    protected function setAndReturnSerializeProp($mixValue, $strName)
    {
        if (($strMethod = 'setAndReturnSerializeProp'.ucwords($strName)) && method_exists($this, $strMethod)) {
            return $this->{$strMethod}($mixValue);
        }

        return $mixValue;
    }

    /**
     * 返回序列化的值
     *
     * @param mixed  $mixValue
     * @param string $strName
     *
     * @return mixed
     */
    protected function getSerializeProp($mixValue, $strName)
    {
        if (($strMethod = 'getSerializeProp'.ucwords($strName)) && method_exists($this, $strMethod)) {
            return $this->{$strMethod}($mixValue);
        }

        return $mixValue;
    }

    /**
     * 取得属性值
     *
     * @param \ReflectionProperty $oProp
     *
     * @return mixed
     */
    protected function getPropertySource(ReflectionProperty $oProp)
    {
        $oProp->setAccessible(true);

        return $oProp->getValue($this);
    }
}
