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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\support\observer;

use SplSubject;
use SplObserver;
use RuntimeException;

/**
 * 观察者角色 observer
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.23
 * @version 1.0
 */
abstract class observer implements SplObserver
{

    /**
     * 观察者目标角色 subject
     *
     * @var \SplSubject
     */
    protected $objSubject;

    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * (non-PHPdoc)
     *
     * @see SplObserver::update()
     */
    public function update(SplSubject $objSubject)
    {
        $strMethod = method_exists($this, 'handle') ? 'handle' : 'run';

        $arrArgs = func_get_args();
        array_shift($arrArgs);

        if (! is_callable([
            $this,
            $strMethod
        ])) {
            throw new RuntimeException(sprintf('Observer %s must has run method', get_class($this)));
        }

        $objSubject->container()->call([
            $this,
            $strMethod
        ], $arrArgs);

        unset($arrArgs);
    }
}
