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

use Leevel\Database\Ddd\IEntity;
use Leevel\Encryption\IEncryption;
use Leevel\Session\ISession;
use Leevel\Validate\IValidate;

/**
 * auth.session.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.07
 *
 * @version 1.0
 */
class Session extends Connect implements IConnect
{
    /**
     * session.
     *
     * @var \Leevel\Session\ISession
     */
    protected $session;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity   $user
     * @param \Leevel\Encryption\IEncryption $encryption
     * @param \Leevel\Validate\IValidate     $validate
     * @param \Leevel\Session\ISession       $session
     * @param array                          $option
     */
    public function __construct(IEntity $user, IEncryption $encryption, IValidate $validate, ISession $session, array $option = [])
    {
        $this->session = $session;

        parent::__construct($user, $encryption, $validate, $option);
    }

    /**
     * 设置认证名字.
     *
     * @param \Leevel\Database\Ddd\IEntity $user
     */
    protected function setLoginTokenName($user)
    {
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
        $this->session->set($key, $value, [
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
        return $this->session->get($key);
    }

    /**
     * 删除持久化数据.
     *
     * @param string $key
     */
    protected function deletePersistence($key)
    {
        $this->session->delele($key);
    }
}
