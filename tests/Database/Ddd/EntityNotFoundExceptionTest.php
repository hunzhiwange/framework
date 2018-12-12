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

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\EntityNotFoundException;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Guestbook;

/**
 * EntityNotFoundExceptionTest.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.31
 *
 * @version 1.0
 */
class EntityNotFoundExceptionTest extends TestCase
{
    public function testBaseUse()
    {
        $e = new EntityNotFoundException();
        $e->setEntity(Guestbook::class);

        $this->assertSame(Guestbook::class, $e->entity());
        $this->assertSame('Entity `Tests\\Database\\Ddd\\Entity\\Guestbook` was not found.', $e->getMessage());
    }
}
