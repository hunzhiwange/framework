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
     *
     * @var int
     */
    protected $encodingOptions = JSON_UNESCAPED_UNICODE;

    /**
     * {@inheritdoc}
     */
    public function setData($data = [])
    {
        $data = convert_json($data, $this->encodingOptions);

        return $this->setJson($data);
    }
}

// import fn.
class_exists(convert_json::class);
