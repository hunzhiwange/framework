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

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;

/**
 * TestReadonlyUpdateEntity.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.01
 *
 * @version 1.0
 */
class TestReadonlyUpdateEntity extends Entity
{
    const TABLE = 'test';

    /**
     * 存在复合主键.
     *
     * @var array
     */
    const PRIMARY_KEY = [
        'id',
    ];

    const AUTO_INCREMENT = 'id';

    const STRUCT = [
        'id' => [
            'name'           => 'id', // database
            'type'           => 'int', // database
            'length'         => 11, // database
            'primary_key'    => true, // database
            'auto_increment' => true, // database
            'default'        => null, // database
        ],
        'name' => [
            'name'           => 'name',
            'type'           => 'varchar',
            'length'         => 45,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'readonly'       => true,
        ],
        'description' => [
            'name'           => 'description',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
        ],
    ];
    protected $id;

    protected $name;

    protected $description;
}
