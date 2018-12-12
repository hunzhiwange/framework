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

namespace Tests\Option;

use Leevel\Option\ComposerOption;
use Tests\TestCase;

/**
 * composerOption test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.17
 *
 * @version 1.0
 */
class ComposerOptionTest extends TestCase
{
    public function testBaseUse()
    {
        $options = ($composerOption = new ComposerOption(__DIR__.'/app1'))->loadData();

        $data = <<<'eot'
{
    "providers": [
        "Tests\\Option\\Providers\\Foo",
        "Tests\\Option\\Providers\\Bar",
        "Demo\\Provider\\Register",
        "Common\\Infra\\Provider\\Event",
        "Common\\Infra\\Provider\\Router"
    ],
    "ignores": [
        "Leevel\\Notexits\\Provider\\Register"
    ],
    "commands": [
        "Tests\\Option\\Commands\\Test",
        "Tests\\Option\\Commands\\Console",
        "Demo\\Demo\\Console",
        "Common\\App\\Console"
    ],
    "options": {
        "demo": "option\/extend\/test.php"
    },
    "i18ns": [
        "i18n\/extend"
    ],
    "metas": {
        "foo": "bar"
    }
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $options
            )
        );

        $this->assertSame(
            $data,
            $this->varJson(
                $composerOption->loadData()
            )
        );
    }

    public function testComposerNotFound()
    {
        $options = ($composerOption = new ComposerOption(__DIR__.'/app4'))->loadData();

        $data = <<<'eot'
{
    "providers": [
        "Tests\\Option\\Providers\\Foo",
        "Tests\\Option\\Providers\\Bar",
        "Demo\\Provider\\Register"
    ],
    "ignores": [],
    "commands": [
        "Tests\\Option\\Commands\\Test",
        "Tests\\Option\\Commands\\Console",
        "Demo\\Demo\\Console"
    ],
    "options": [],
    "i18ns": [],
    "metas": []
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $options
            )
        );
    }
}
