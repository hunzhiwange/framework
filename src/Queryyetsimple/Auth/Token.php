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
     * @var \Leevel\Cache\ICache
     */
    protected $cache;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity   $user
     * @param \Leevel\Encryption\IEncryption $encryption
     * @param \Leevel\Validate\IValidate     $validate
     * @param \Leevel\Cache\ICache           $cache
     * @param array                          $option
     */
    public function __construct(IEntity $user, IEncryption $encryption, IValidate $validate, ICache $cache, array $option = [])
    {
        $this->cache = $cache;

        parent::__construct($user, $encryption, $validate, $option);
    }

    /**
     * 设置认证名字.
     *
     * @param \Leevel\Database\Ddd\IEntity $user
     */
    protected function setLoginTokenName($user)
    {
        $this->setTokenName(
            md5(
                $user->{$this->getField('name')}.
                $user->{$this->getField('password')}.
                Str::randAlphaNum(5)
            )
        );
    }

    /**
     * 数据持久化.
     *
     * @param string $key
     * @param string $value
     * @param mixed  $expire
     */
    protected function setPersistence($key, $value, $expire = null)
    {
        $this->cache->set($key, $value, [
            'expire' => $expire,
        ]);
    }

    /**
     * 获取持久化数据.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getPersistence($key)
    {
        return $this->cache->get($key);
    }

    /**
     * 删除持久化数据.
     *
     * @param string $key
     */
    protected function deletePersistence($key)
    {
        $this->cache->delele($key);
    }
}
