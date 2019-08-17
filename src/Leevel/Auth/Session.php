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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Auth;

use Leevel\Session\ISession;

/**
 * auth.session.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.07
 *
 * @version 1.0
 */
class Session extends Auth implements IAuth
{
    /**
     * session.
     *
     * @var \Leevel\Session\ISession
     */
    protected ISession $session;

    /**
     * 构造函数.
     *
     * @param \Leevel\Session\ISession $session
     * @param array                    $option
     */
    public function __construct(ISession $session, array $option = [])
    {
        $this->session = $session;

        parent::__construct($option);
    }

    /**
     * 数据持久化.
     *
     * @param string $key
     * @param string $value
     * @param int    $expire
     */
    protected function setPersistence(string $key, string $value, int $expire = 0): void
    {
        $this->session->set($key, $value);
    }

    /**
     * 获取持久化数据.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getPersistence(string $key)
    {
        return $this->session->get($key);
    }

    /**
     * 删除持久化数据.
     *
     * @param string $key
     */
    protected function deletePersistence(string $key): void
    {
        $this->session->delete($key);
    }
}
