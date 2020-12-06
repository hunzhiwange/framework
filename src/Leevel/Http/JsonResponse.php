<?php

declare(strict_types=1);

namespace Leevel\Http;

use function Leevel\Support\Arr\convert_json;
use Leevel\Support\Arr\convert_json;
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
    protected $encodingOptions = JSON_UNESCAPED_UNICODE;

    /**
     * {@inheritDoc}
     */
    public function setData($data = [])
    {
        $data = convert_json($data, $this->encodingOptions);

        return $this->setJson($data);
    }
}

// import fn.
class_exists(convert_json::class);
