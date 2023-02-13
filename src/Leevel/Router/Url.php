<?php

declare(strict_types=1);

namespace Leevel\Router;

use Leevel\Http\Request;

/**
 * URL 生成.
 */
class Url implements IUrl
{
    /**
     * URL 参数.
     */
    protected array $params = [];

    /**
     * 配置.
     */
    protected array $option = [
        'with_suffix' => false,
        'suffix' => '.html',
        'domain' => '',
    ];

    /**
     * 构造函数.
     */
    public function __construct(protected Request $request, array $option = [])
    {
        if ($option) {
            $this->option = array_merge($this->option, $option);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function make(string $url, array $params = [], string $subdomain = 'www', null|bool|string $suffix = null): string
    {
        $url = $this->makeUrl($url, $params, null !== $suffix ? $suffix : $this->option['with_suffix']);
        $url = $this->withEnter($url);

        return $this->WithDomain($url, $subdomain);
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function getDomain(): string
    {
        return $this->option['domain'];
    }

    /**
     * 自定义 URL.
     */
    protected function makeUrl(string $url, array $params, bool|string $suffix): string
    {
        $this->params = $params;

        if ('/' !== substr($url, 0, 1)) {
            $url = '/'.$url;
        }

        if (str_contains($url, '{')) {
            $url = (string) preg_replace_callback('/{(.+?)}/', function ($matches) {
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
            $url .= (str_contains($url, '?') ? '&' : '?').$queryUrl;
        }

        return $this->withSuffix($url, $suffix);
    }

    /**
     * 返回完整 URL 地址.
     */
    protected function withDomain(string $url, string $domain): string
    {
        if (!$this->option['domain'] || !$domain) {
            return $url;
        }

        return ($this->isSecure() ? 'https://' : 'http://').
            ('*' !== $domain ? $domain.'.' : '').
            $this->option['domain'].$url;
    }

    /**
     * 是否启用 https.
     */
    protected function isSecure(): bool
    {
        return $this->request->isSecure();
    }

    /**
     * URL 带后缀.
     */
    protected function withSuffix(string $url, bool|string $suffix): string
    {
        if ('/' === $url || str_starts_with($url, '/?')) {
            return $url;
        }

        $suffix = true === $suffix ? $this->option['suffix'] : $suffix;

        if (str_contains($url, '?')) {
            $url = str_replace('?', $suffix.'?', $url);
        } else {
            $url .= $suffix;
        }

        return $url;
    }

    /**
     * 带上入口文件.
     */
    protected function withEnter(string $url): string
    {
        $enter = $this->request->getEnter();
        $enter = '/' !== $enter ? $enter : '';

        return $enter.$url;
    }
}
