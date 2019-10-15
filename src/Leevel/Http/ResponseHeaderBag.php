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

namespace Leevel\Http;

/**
 * response header bag.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.27
 *
 * @version 1.0
 */
class ResponseHeaderBag extends HeaderBag
{
    /**
     * 下载附件.
     *
     * @var string
     */
    const DISPOSITION_ATTACHMENT = 'attachment';

    /**
     * 文件直接读取.
     *
     * @var string
     */
    const DISPOSITION_INLINE = 'inline';

    /**
     * Cookie.
     *
     * @var \Leevel\Http\Cookie
     */
    protected $cookie;

    /**
     * 构造函数.
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
        $this->cookie = new Cookie();
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return $this->cookie->{$method}(...$args);
    }

    /**
     * 设置 COOKIE 别名.
     *
     * @param string            $name
     * @param null|array|string $value
     * @param array             $option
     */
    public function cookie(string $name, $value = null, array $option = [])
    {
        $this->setCookie($name, $value, $option);
    }

    /**
     * 设置 COOKIE.
     *
     * @param string            $name
     * @param null|array|string $value
     * @param array             $option
     */
    public function setCookie(string $name, $value = null, array $option = []): void
    {
        $this->cookie->set($name, $value, $option);
    }

    /**
     * 批量设置 COOKIE.
     *
     * @param array $cookies
     * @param array $option
     */
    public function withCookies(array $cookies, array $option = []): void
    {
        foreach ($cookies as $key => $value) {
            $this->setCookie($key, $value, $option);
        }
    }

    /**
     * 获取 COOKIE.
     *
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookie->all();
    }
}
