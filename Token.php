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
namespace Queryyetsimple\Auth;

use Queryyetsimple\{
    Mvc\IModel,
    Support\Str,
    Cache\ICache,
    Validate\IValidate,
    Encryption\IEncryption
};

/**
 * auth.token
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
class Token extends Connect implements IConnect
{

    /**
     * 验证
     *
     * @var \Queryyetsimple\Validate\IValidate
     */
    protected $oCache;

    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Mvc\IModel $oUser
     * @param \Queryyetsimple\Encryption\IEncryption $oEncryption
     * @param \Queryyetsimple\Validate\IValidate $oValidate
     * @param \Queryyetsimple\Cache\ICache $oCache
     * @param array $arrOption
     * @return void
     */
    public function __construct(IModel $oUser, IEncryption $oEncryption, IValidate $oValidate, ICache $oCache, array $arrOption = [])
    {
        $this->oCache = $oCache;

        parent::__construct($oUser, $oEncryption, $oValidate, $arrOption);
    }

    /**
     * 设置认证名字
     *
     * @param \Queryyetsimple\Mvc\IModel $oUser
     * @return void
     */
    protected function setLoginTokenName($oUser)
    {
        $this->setTokenName(md5($oUser->{$this->getField('name')} . $oUser->{$this->getField('password')} . Str::randAlphaNum(5)));
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
