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

namespace Tests\Database\Ddd\Relation;

use Exception;
use Leevel\Database\Ddd\Relation\HasOne;
use Leevel\Database\Ddd\Relation\Relation;
use Tests\Database\DatabaseTestCase as TestCase;

class RelationTest extends TestCase
{
    public function testWithoutRelationCondition(): void
    {
        $relation = Relation::withoutRelationCondition(function (): Relation {
            return new class() extends HasOne {
                public function __construct()
                {
                }
            };
        });

        $this->assertInstanceof(Relation::class, $relation);
    }

    public function testWithoutRelationConditionException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'error'
        );

        $relation = Relation::withoutRelationCondition(function (): Relation {
            return new class() extends HasOne {
                public function __construct()
                {
                    throw new Exception('error');
                }
            };
        });
    }
}
