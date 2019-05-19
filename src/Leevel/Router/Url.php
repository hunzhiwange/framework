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

namespace Leevel\Router;

use Leevel\Http\IRequest;

/**
 * url 生成.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.01.10
 *
 * @version 1.0
 */
class Url implements IUrl
{
    /**
     * HTTP 请求
     *
     * @var \Leevel\Http\IRequest
     */
    protected IRequest $request;

    /**
     * URL 参数.
     *
     * @var array
     */
    protected array $params = [];

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'with_suffix'  => false,
        'suffix'       => '.html',
        'domain'       => '',
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Http\IRequest $request
     * @param array                 $option
     */
    public function __construct(IRequest $request, array $option = [])
    {
        $this->request = $request;

        if ($option) {
            $this->option = array_merge($this->option, $option);
        }
    }

    /**
     * 生成路由地址
     *
     * @param string      $url
     * @param array       $params
     * @param string      $subdomain
     * @param bool|string $suffix
     *
     * @return string
     */
    public function make(string $url, array $params = [], string $subdomain = 'www', $suffix = null): string
    {
        $url = $this->makeUrl($url, $params, null !== $suffix ? $suffix : $this->option['with_suffix']);
        $url = $this->withEnter($url);
        $url = $this->WithDomain($url, $subdomain);

        return $url;
    }

    /**
     * 返回 HTTP 请求
     *
     * @return \Leevel\Http\IRequest
     */
    public function getRequest(): IRequest
    {
        return $this->request;
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value): IUrl
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 获取域名.
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->option['domain'];
    }

    /**
     * 自定义 URL.
     *
     * @param string      $url
     * @param array       $params
     * @param bool|string $suffix
     *
     * @return string
     */
    protected function makeUrl(string $url, array $params, $suffix): string
    {
        $this->params = $params;

        if ('/' !== substr($url, 0, 1)) {
            $url = '/'.$url;
        }

        if (false !== strpos($url, '{')) {
            $url = preg_replace_callback('/{(.+?)}/', function ($matches) {
                if (isset($this->params[$matches[1]])) {
                    $value = $this->params[$matches[1]];
                    unset($this->params[$matches[1]]);
                } else {
                    $value = $matches[0];
                }

                return $value;
            }, $url);
        }

        if ($this->params) {
            $queryUrl = http_build_query($this->params);
            $url .= (false !== strpos($url, '?') ? '&' : '?').$queryUrl;
        }

        $url = $this->withSuffix($url, $suffix);

        return $url;
    }

    /**
     * 返回完整 URL 地址
     *
     * @param string $url
     * @param string $domain
     *
     * @return string
     */
    protected function withDomain(string $url, string $domain): string
    {
        if (!$this->option['domain'] || !$domain) {
            return $url;
        }

        return ($this->isSecure() ? 'https://' : 'http://').
            ($domain && '*' !== $domain ? $domain.'.' : '').
            $this->option['domain'].$url;
    }

    /**
     * 是否启用 https.
     *
     * @return bool
     */
    protected function isSecure(): bool
    {
        return $this->request->isSecure();
    }

    /**
     * url 带后缀
     *
     * @param string      $url
     * @param bool|string $suffix
     *
     * @return string
     */
    protected function withSuffix(string $url, $suffix): string
    {
        if ('/' === $url || 0 === strpos($url, '/?')) {
            return $url;
        }

        $suffix = true === $suffix ? $this->option['suffix'] : $suffix;

        if (false !== strpos($url, '?')) {
            $url = str_replace('?', $suffix.'?', $url);
        } else {
            $url .= $suffix;
        }

        return $url;
    }

    /**
     * 带上入口文件.
     *
     * @param string $url
     *
     * @return string
     */
    protected function withEnter(string $url): string
    {
        $enter = $this->request->getEnter();
        $enter = '/' !== $enter ? $enter : '';

        return $enter.$url;
    }
}
