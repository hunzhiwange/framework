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

namespace Tests\Http\Fixtures;

use BadMethodCallException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Responses.
 *
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @see https://github.com/symfony/psr-http-message-bridge/blob/master/Tests/Fixtures/Response.php
 */
class Response extends Message implements ResponseInterface
{
    private $statusCode;

    public function __construct(string $version, array $headers, StreamInterface $body, int $statusCode = 200)
    {
        parent::__construct($version, $headers, $body);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        throw new BadMethodCallException('Not implemented.');
    }

    public function getReasonPhrase()
    {
        throw new BadMethodCallException('Not implemented.');
    }
}
