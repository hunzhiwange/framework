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

namespace Tests\Support;

use Tests\TestCase;

/**
 * functions test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.22
 *
 * @version 1.0
 */
class FunctionsTest extends TestCase
{
    /**
     * @dataProvider envProvider
     *
     * @param string $name
     * @param mixed  $value
     * @param mixed  $envValue
     */
    public function testEnv(string $name, $value, $envValue): void
    {
        $name = 'test_env_'.$name;
        putenv($name.'='.$value);
        $this->assertSame($envValue, env($name));
    }

    public function envProvider(): array
    {
        return [
            ['bar', 'true', true],
            ['bar', '(true)', true],
            ['bar', 'false', false],
            ['bar', '(false)', false],
            ['bar', 'empty', ''],
            ['bar', '(empty)', ''],
            ['bar', 'null', null],
            ['bar', '(null)', null],
            ['bar', '"hello"', 'hello'],
            ['bar', "'hello'", "'hello'"],
            ['bar', true, '1'],
            ['bar', false, ''],
            ['bar', 1, '1'],
            ['bar', '', ''],
        ];
    }

    public function testEnvFalse(): void
    {
        $this->assertSame('default message', env('not_found_env', 'default message'));
        $this->assertNull(env('not_found_env'));
    }
}
