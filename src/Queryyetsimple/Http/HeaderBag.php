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
namespace Queryyetsimple\Http;

/**
 * header bag
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.25
 * @version 1.0
 */
class HeaderBag extends Bag
{

    /**
     * 构造函数
     * 
     * @param array $elements
     * @return void 
     */
    public function __construct(array $elements = [])
    {
        $this->add($elements);
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
