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

use queryyetsimple\mvc\imodel;
use queryyetsimple\cache\icache;
use queryyetsimple\support\string;
use queryyetsimple\validate\ivalidate;
use queryyetsimple\encryption\iencryption;

/**
 * auth.token
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
class token extends aconnect implements iconnect
{

    /**
     * 验证
     *
     * @var \queryyetsimple\validate\ivalidate
     */
    protected $oCache;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\mvc\imodel $oUser
     * @param \queryyetsimple\encryption\iencryption $oEncryption
     * @param \queryyetsimple\validate\ivalidate $oValidate
     * @param \queryyetsimple\cache\icache $oCache
     * @param array $arrOption
     * @return void
     */
    public function __construct(imodel $oUser, iencryption $oEncryption, ivalidate $oValidate, icache $oCache, array $arrOption = [])
    {
        $this->oCache = $oCache;

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
        $this->setTokenName(md5($oUser->{$this->getField('name')} . $oUser->{$this->getField('password')} . string::randAlphaNum(5)));
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
        $this->oCache->set($strKey, $mixValue, [
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
        return $this->oCache->get($strKey);
    }

    /**
     * 删除持久化数据
     *
     * @param string $strKey
     * @return void
     */
    protected function deletePersistence($strKey)
    {
        $this->oCache->delele($strKey);
    }
}
