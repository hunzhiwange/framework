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
array (
  'providers' => 
  array (
    0 => 'Leevel\\Auth\\Provider\\Register',
    1 => 'Leevel\\Cache\\Provider\\Register',
    2 => 'Leevel\\Cookie\\Provider\\Register',
    3 => 'Leevel\\Database\\Provider\\Register',
    4 => 'Leevel\\Encryption\\Provider\\Register',
    5 => 'Leevel\\Filesystem\\Provider\\Register',
    6 => 'Leevel\\Mail\\Provider\\Register',
    7 => 'Leevel\\Mvc\\Provider\\Register',
    8 => 'Leevel\\Page\\Provider\\Register',
    9 => 'Leevel\\Queue\\Provider\\Register',
    10 => 'Leevel\\Session\\Provider\\Register',
    11 => 'Leevel\\Protocol\\Provider\\Register',
    12 => 'Leevel\\Throttler\\Provider\\Register',
    13 => 'Leevel\\Validate\\Provider\\Register',
    14 => 'Leevel\\View\\Provider\\Register',
    15 => 'Demo\\Provider\\Register',
    16 => 'Common\\Infra\\Provider\\Event',
    17 => 'Common\\Infra\\Provider\\Router',
  ),
  'ignores' => 
  array (
    0 => 'Leevel\\Notexits\\Provider\\Register',
  ),
  'commands' => 
  array (
    0 => 'Leevel\\Database\\Console',
    1 => 'Leevel\\I18n\\Console',
    2 => 'Leevel\\Mvc\\Console',
    3 => 'Leevel\\Queue\\Console',
    4 => 'Leevel\\Router\\Console',
    5 => 'Leevel\\Protocol\\Console',
    6 => 'Leevel\\Option\\Console',
    7 => 'Demo\\Demo\\Console',
    8 => 'Common\\App\\Console',
  ),
  'options' => 
  array (
    'demo' => 'option/extend/test.php',
  ),
  'i18ns' => 
  array (
    0 => 'i18n/extend',
  ),
  'metas' => 
  array (
    'foo' => 'bar',
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $options
            )
        );

        $this->assertSame(
            $data,
            $this->varExport(
                $composerOption->loadData()
            )
        );
    }

    public function testComposerNotFound()
    {
        $options = ($composerOption = new ComposerOption(__DIR__.'/app4'))->loadData();

        $data = <<<'eot'
array (
  'providers' => 
  array (
    0 => 'Leevel\\Auth\\Provider\\Register',
    1 => 'Leevel\\Cache\\Provider\\Register',
    2 => 'Leevel\\Cookie\\Provider\\Register',
    3 => 'Leevel\\Database\\Provider\\Register',
    4 => 'Leevel\\Encryption\\Provider\\Register',
    5 => 'Leevel\\Filesystem\\Provider\\Register',
    6 => 'Leevel\\Mail\\Provider\\Register',
    7 => 'Leevel\\Mvc\\Provider\\Register',
    8 => 'Leevel\\Page\\Provider\\Register',
    9 => 'Leevel\\Queue\\Provider\\Register',
    10 => 'Leevel\\Session\\Provider\\Register',
    11 => 'Leevel\\Protocol\\Provider\\Register',
    12 => 'Leevel\\Throttler\\Provider\\Register',
    13 => 'Leevel\\Validate\\Provider\\Register',
    14 => 'Leevel\\View\\Provider\\Register',
    15 => 'Demo\\Provider\\Register',
  ),
  'ignores' => 
  array (
  ),
  'commands' => 
  array (
    0 => 'Leevel\\Database\\Console',
    1 => 'Leevel\\I18n\\Console',
    2 => 'Leevel\\Mvc\\Console',
    3 => 'Leevel\\Queue\\Console',
    4 => 'Leevel\\Router\\Console',
    5 => 'Leevel\\Protocol\\Console',
    6 => 'Leevel\\Option\\Console',
    7 => 'Demo\\Demo\\Console',
  ),
  'options' => 
  array (
  ),
  'i18ns' => 
  array (
  ),
  'metas' => 
  array (
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $options
            )
        );
    }
}
