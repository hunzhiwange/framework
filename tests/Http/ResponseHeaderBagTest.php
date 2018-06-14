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

use Leevel\Http\ResponseHeaderBag;
use Tests\TestCase;

/**
 * ResponseHeaderBag test
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
class ResponseHeaderBagTest extends TestCase
{
    public function testAll()
    {
        $headers = [
            'fOo'              => 'BAR',
            'ETag'             => 'xyzzy',
            'Content-MD5'      => 'Q2hlY2sgSW50ZWdyaXR5IQ==',
            'P3P'              => 'CP="CAO PSA OUR"',
            'WWW-Authenticate' => 'Basic realm="WallyWorld"',
            'X-UA-Compatible'  => 'IE=edge,chrome=1',
            'X-XSS-Protection' => '1; mode=block',
        ];

        $bag = new ResponseHeaderBag($headers);

        $all = $bag->all();

        foreach (array_keys($headers) as $headerName) {
            $this->assertArrayHasKey(strtolower($headerName), $all, '->all() gets all input keys in strtolower case');
        }
    }
}
