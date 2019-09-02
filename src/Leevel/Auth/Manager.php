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

use Leevel\Manager\Manager as Managers;

/**
 * manager 入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.07
 *
 * @version 1.0
 *
 * @method static bool isLogin()                              用户是否已经登录.
 * @method static array getLogin()                            获取登录信息.
 * @method static void login(array $data, int $loginTime = 0) 登录写入数据.
 * @method static void logout()                               登出.
 * @method static void setTokenName(string $tokenName)        设置认证名字.
 * @method static string getTokenName()                       取得认证名字.
 */
class Manager extends Managers
{
    /**
     * 返回默认驱动.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        $option = $this->getContainerOption('default');

        return $this->getContainerOption($option.'_default');
    }

    /**
     * 设置默认驱动.
     *
     * @param string $name
     */
    public function setDefaultDriver(string $name): void
    {
        $option = $this->getContainerOption('default');

        $this->setContainerOption($option.'_default', $name);
    }

    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    protected function normalizeOptionNamespace(): string
    {
        return 'auth';
    }

    /**
     * 创建 session 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Auth\Session
     */
    protected function makeConnectSession(array $options = []): Session
    {
        $options = array_merge(
            $this->normalizeConnectOption('session', $options)
        );

        return new Session($this->container['session'], $options);
    }

    /**
     * 创建 token 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Auth\Token
     */
    protected function makeConnectToken(array $options = []): Token
    {
        $options = array_merge(
            $this->normalizeConnectOption('token', $options)
        );

        return new Token($this->container['cache'], $this->container['request'], $options);
    }
}
