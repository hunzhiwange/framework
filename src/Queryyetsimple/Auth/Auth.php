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
namespace queryyetsimple\auth;

/**
 * auth 仓储
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.11.08
 * @version 1.0
 */
class auth implements iauth
{

    /**
     * auth 连接对象
     *
     * @var \queryyetsimple\database\iconnect
     */
    protected $objConnect;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\auth\iconnect $objConnect
     * @return void
     */
    public function __construct(iconnect $objConnect)
    {
        $this->objConnect = $objConnect;
    }

    /**
     * call 
     *
     * @param string $sMethod
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $sMethod, array $arrArgs)
    {
        return $this->objConnect->$sMethod(...$arrArgs);
    }
}
