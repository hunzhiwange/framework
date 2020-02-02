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

namespace Tests\Database\Ddd\Entity\Relation;

use Exception;
use Leevel\Database\Ddd\Entity;

class PostForReplace extends Entity
{
    const TABLE = 'post';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY           => true,
        ],
        'title'     => [],
        'user_id'   => [],
        'summary'   => [],
        'create_at' => [],
        'delete_at' => [
            self::CREATE_FILL => 0,
        ],
    ];

    const DELETE_AT = 'delete_at';

    private array $data = [];

    private static $connect;

    public function setter(string $prop, $value): self
    {
        $this->data[$this->realProp($prop)] = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->data[$this->realProp($prop)] ?? null;
    }

    public static function withConnect($connect): void
    {
        static::$connect = $connect;
    }

    public static function connect()
    {
        return static::$connect;
    }

    protected function updateReal(?array $fill = null): self
    {
        throw new Exception('Update error');
    }
}
