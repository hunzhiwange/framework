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
namespace queryyetsimple\session;

use SessionHandler;
use queryyetsimple\support\option;

/**
 * aconnect 驱动抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.06
 * @version 1.0
 */
abstract class aconnect
{
    use option;

    /**
     * 缓存仓储
     *
     * @var \queryyetsimple\cache\icache
     */
    protected $objCache;

    /**
     * 构造函数
     *
     * @param array $arrOption
     * @return void
     */
    public function __construct(array $arrOption = [])
    {
        $this->options($arrOption);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->objCache->close();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($strSessID)
    {
        return $this->objCache->get($this->getSessionName($strSessID));
    }

    /**
     * {@inheritdoc}
     */
    public function write($strSessID, $mixSessData)
    {
        $this->objCache->set($this->getSessionName($strSessID), $mixSessData);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($strSessID)
    {
        $this->objCache->delele($this->getSessionName($strSessID));
    }

    /**
     * {@inheritdoc}
     */
    public function gc($intMaxlifetime)
    {
        return true;
    }

    /**
     * 获取 session 名字
     *
     * @param string $strSessID
     * @return string
     */
    protected function getSessionName($strSessID)
    {
        return $this->arrOption ['prefix'] . $strSessID;
    }
}
