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
namespace queryyetsimple\auth;

use queryyetsimple\mvc\imodel;
use queryyetsimple\session\isession;
use queryyetsimple\validate\ivalidate;
use queryyetsimple\encryption\iencryption;

/**
 * auth.session
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
class session extends aconnect implements iconnect
{

    /**
     * session
     *
     * @var \queryyetsimple\session\isession
     */
    protected $oSession;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\mvc\imodel $oUser
     * @param \queryyetsimple\encryption\iencryption $oEncryption
     * @param \queryyetsimple\validate\ivalidate $oValidate
     * @param \queryyetsimple\session\isession $oSession
     * @param array $arrOption
     * @return void
     */
    public function __construct(imodel $oUser, iencryption $oEncryption, ivalidate $oValidate, isession $oSession, array $arrOption = [])
    {
        $this->oSession = $oSession;

        parent::__construct($oUser, $oEncryption, $oValidate, $arrOption);
    }

    /**
     * 设置认证名字
     *
     * @param \queryyetsimple\mvc\imodel $oUser
     * @return void
     */
    protected function setLoginTokenName($oUser)
    {
    }

    /**
     * 数据持久化
     *
     * @param string $strKey
     * @param string $mixValue
     * @param mixed $mixExpire
     * @return void
     */
    protected function setPersistence($strKey, $mixValue, $mixExpire = null)
    {
        $this->oSession->set($strKey, $mixValue, [
                'expire' => $mixExpire
        ]);
    }

    /**
     * 获取持久化数据
     *
     * @param string $strKey
     * @return mixed
     */
    protected function getPersistence($strKey)
    {
        return $this->oSession->get($strKey);
    }

    /**
     * 删除持久化数据
     *
     * @param string $strKey
     * @return void
     */
    protected function deletePersistence($strKey)
    {
        $this->oSession->delele($strKey);
    }
}
