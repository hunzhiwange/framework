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

namespace Leevel\Auth;

use Leevel\Cache\ICache;
use Leevel\Database\Ddd\IEntity;
use Leevel\Encryption\IEncryption;
use Leevel\Support\Str;
use Leevel\Validate\IValidate;

/**
 * auth.token.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.07
 *
 * @version 1.0
 */
class Token extends Connect implements IConnect
{
    /**
     * 验证
     *
     * @var \Leevel\Validate\IValidate
     */
    protected $oCache;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity   $oUser
     * @param \Leevel\Encryption\IEncryption $oEncryption
     * @param \Leevel\Validate\IValidate     $oValidate
     * @param \Leevel\Cache\ICache           $oCache
     * @param array                          $arrOption
     */
    public function __construct(IEntity $oUser, IEncryption $oEncryption, IValidate $oValidate, ICache $oCache, array $arrOption = [])
    {
        $this->oCache = $oCache;

        parent::__construct($oUser, $oEncryption, $oValidate, $arrOption);
    }

    /**
     * 设置认证名字.
     *
     * @param \Leevel\Database\Ddd\IEntity $oUser
     */
    protected function setLoginTokenName($oUser)
    {
        $this->setTokenName(md5($oUser->{$this->getField('name')}.$oUser->{$this->getField('password')}.Str::randAlphaNum(5)));
    }

    /**
     * 数据持久化.
     *
     * @param string $strKey
     * @param string $mixValue
     * @param mixed  $mixExpire
     */
    protected function setPersistence($strKey, $mixValue, $mixExpire = null)
    {
        $this->oCache->set($strKey, $mixValue, [
            'expire' => $mixExpire,
        ]);
    }

    /**
     * 获取持久化数据.
     *
     * @param string $strKey
     *
     * @return mixed
     */
    protected function getPersistence($strKey)
    {
        return $this->oCache->get($strKey);
    }

    /**
     * 删除持久化数据.
     *
     * @param string $strKey
     */
    protected function deletePersistence($strKey)
    {
        $this->oCache->delele($strKey);
    }
}
