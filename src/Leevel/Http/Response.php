<?php

declare(strict_types=1);

namespace Leevel\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * HTTP 响应.
 */
class Response extends SymfonyResponse
{
    use BaseResponse;
}
