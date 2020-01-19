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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Auth;

use Leevel\Cache\ICache;
use Leevel\Http\Request;

/**
 * Auth token.
 */
class Token extends Auth implements IAuth
{
    /**
     * 验证.
     *
     * @var \Leevel\Cache\ICache
     */
    protected ICache $cache;

    /**
     * HTTP 请求.
     *
     * @var \Leevel\Http\Request
     */
    protected Request $request;

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'token'       => null,
        'input_token' => 'token',
    ];

    /**
     * 构造函数.
     */
    public function __construct(ICache $cache, Request $request, array $option = [])
    {
        $this->cache = $cache;
        $this->request = $request;
        parent::__construct($option);
        $this->option['token'] = $this->getTokenNameFromRequest();
    }

    /**
     * 数据持久化.
     */
    protected function setPersistence(string $key, string $value, int $expire = 0): void
    {
        $this->cache->set($key, $value, ['expire' => $expire]);
    }

    /**
     * 获取持久化数据.
     *
     * @return mixed
     */
    protected function getPersistence(string $key)
    {
        return $this->cache->get($key);
    }

    /**
     * 删除持久化数据.
     */
    protected function deletePersistence(string $key): void
    {
        $this->cache->delete($key);
    }

    /**
     * 从请求中获取 token.
     */
    protected function getTokenNameFromRequest(): string
    {
        $token = $this->request->query($this->option['input_token'], '');
        if (!$token) {
            $token = $this->request->input($this->option['input_token'], '');
        }

        return $token;
    }
}
