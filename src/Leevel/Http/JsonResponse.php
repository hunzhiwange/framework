<?php

declare(strict_types=1);

namespace Leevel\Http;

use Leevel\Support\Arr\ConvertJson;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;

/**
 * JSON 响应请求.
 */
class JsonResponse extends SymfonyJsonResponse
{
    use BaseResponse;

    /**
     * 默认 JSON 编码配置.
     */
    protected $encodingConfigs = JSON_UNESCAPED_UNICODE; /** @phpstan-ignore-line */

    /**
     * {@inheritDoc}
     */
    public function setData(mixed $data = []): static
    {
        $data = ConvertJson::handle($data, $this->encodingConfigs);

        return $this->setJson($data);
    }
}
