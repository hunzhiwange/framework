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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router;

use Leevel\Http\ServerBag;
use Tests\TestCase;

/**
 * ServerBag test
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.14
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 * @coversNothing
 */
class ServerBagTest extends TestCase
{
    public function testShouldExtractHeadersFromServerArray()
    {
        $server = [
            'SOME_SERVER_VARIABLE'  => 'value',
            'SOME_SERVER_VARIABLE2' => 'value',
            'ROOT'                  => 'value',
            'HTTP_CONTENT_TYPE'     => 'text/html',
            'HTTP_CONTENT_LENGTH'   => '0',
            'HTTP_ETAG'             => 'asdf',
        ];

        $bag = new ServerBag($server);

        $this->assertSame([
            'CONTENT_TYPE'   => 'text/html',
            'CONTENT_LENGTH' => '0',
            'ETAG'           => 'asdf',
        ], $bag->getHeaders());
    }
}
