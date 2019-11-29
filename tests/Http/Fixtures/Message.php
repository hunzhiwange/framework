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
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Message.
 *
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @see https://github.com/symfony/psr-http-message-bridge/blob/master/Tests/Fixtures/Message.php
 */
class Message implements MessageInterface
{
    private $version = '1.1';
    private $headers = [];
    private $body;

    public function __construct($version, array $headers, StreamInterface $body)
    {
        $this->version = $version;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getProtocolVersion()
    {
        return $this->version;
    }

    public function withProtocolVersion($version)
    {
        throw new BadMethodCallException('Not implemented.');
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    public function getHeader($name)
    {
        return $this->hasHeader($name) ? $this->headers[$name] : [];
    }

    public function getHeaderLine($name)
    {
        return $this->hasHeader($name) ? implode(',', $this->headers[$name]) : '';
    }

    public function withHeader($name, $value)
    {
        $this->headers[$name] = (array) $value;

        return $this;
    }

    public function withAddedHeader($name, $value)
    {
        throw new BadMethodCallException('Not implemented.');
    }

    public function withoutHeader($name)
    {
        unset($this->headers[$name]);

        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        throw new BadMethodCallException('Not implemented.');
    }
}
