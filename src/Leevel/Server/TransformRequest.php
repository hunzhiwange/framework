<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Http\Request;
use Swoole\Http\Request as SwooleHttpRequest;

/**
 * 转换请求对象.
 */
class TransformRequest
{
    public function createRequest(SwooleHttpRequest $swooleRequest): Request
    {
        $request = new Request();
        $this->convertHeadersAndServers($swooleRequest);
        $this->convertRequest($swooleRequest, $request);

        return $request;
    }

    /**
     * 转换 headers 和 servers.
     */
    protected function convertHeadersAndServers(SwooleHttpRequest $swooleRequest): void
    {
        $servers = [];
        if ($swooleRequest->header) {
            $headers = [];
            foreach ($swooleRequest->header as $key => $value) {
                $key = strtoupper(str_replace('-', '_', $key));
                $headers[$key] = $value;
                $servers['HTTP_'.$key] = $value;
            }
            $swooleRequest->header = $headers;
        }

        $this->convertServers($swooleRequest, $servers);
    }

    /**
     * 转换 servers.
     */
    protected function convertServers(SwooleHttpRequest $swooleRequest, array $servers): void
    {
        if ($swooleRequest->server) {
            $swooleRequest->server = array_change_key_case(
                $swooleRequest->server,
                CASE_UPPER
            );
            $servers = array_merge($servers, $swooleRequest->server);
            $swooleRequest->server = $servers;
        } else {
            $swooleRequest->server = $servers ?: null;
        }
    }

    /**
     * 转换请求.
     */
    protected function convertRequest(SwooleHttpRequest $swooleRequest, Request $request): void
    {
        $propMap = [
            'header' => 'headers',
            'server' => 'server',
            'cookie' => 'cookies',
            'get' => 'query',
            'files' => 'files',
            'post' => 'request',
        ];
        foreach ($propMap as $swooleProp => $prop) {
            if ($swooleRequest->{$swooleProp}) {
                $request->{$prop}->replace($swooleRequest->{$swooleProp});
            }
        }
    }
}
