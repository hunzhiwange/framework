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
     * 序列化模型实体.
     */
    public function __sleep()
    {
        $props = (new ReflectionClass($this))->getProperties();

        foreach ($props as $prop) {
            $prop->setValue(
                $this,
                $this->setAndReturnSerializeProp(
                    $this->getPropertySource($prop),
                    $prop->getName()
                )
            );
        }

        return $this->setAndReturnSerializeFilter(array_map(function ($prop) {
            return $prop->getName();
        }, $props));
    }

    /**
     * 反序列化模型实体.
     */
    public function __wakeup()
    {
        foreach ((new ReflectionClass($this))->getProperties() as $prop) {
            $prop->setValue(
                $this,
                $this->getSerializeProp(
                    $this->getPropertySource($prop),
                    $prop->getName()
                )
            );
        }
    }

    /**
     * 设置序列化的属性.
     *
     * @param array $prop
     *
     * @return array
     */
    protected function setAndReturnSerializeFilter(array $prop)
    {
        if (($method = 'setSerializeFilterProp') &&
            method_exists($this, $method)) {
            $this->{$method}($prop);
        }

        return $prop;
    }

    /**
     * 设置序列化的值并返回.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @return mixed
     */
    protected function setAndReturnSerializeProp($value, $name)
    {
        if (($method = 'setAndReturnSerializeProp'.ucwords($name)) &&
            method_exists($this, $method)) {
            return $this->{$method}($value);
        }

        return $value;
    }

    /**
     * 返回序列化的值
     *
     * @param mixed  $value
     * @param string $name
     *
     * @return mixed
     */
    protected function getSerializeProp($value, $name)
    {
        if (($method = 'getSerializeProp'.ucwords($name)) &&
            method_exists($this, $method)) {
            return $this->{$method}($value);
        }

        return $value;
    }

    /**
     * 取得属性值
     *
     * @param \ReflectionProperty $prop
     *
     * @return mixed
     */
    protected function getPropertySource(ReflectionProperty $prop)
    {
        $prop->setAccessible(true);

        return $prop->getValue($this);
    }
}
