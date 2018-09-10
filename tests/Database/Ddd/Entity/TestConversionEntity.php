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
 * TestConversionEntity.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.02
 *
 * @version 1.0
 */
class TestConversionEntity extends Entity
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
            'name'              => 'id', // database
            'type'              => 'int', // database
            'length'            => 11, // database
            'primary_key'       => true, // database
            'auto_increment'    => true, // database
            'default'           => null, // database
        ],
        'int1' => [
            // int = integer
            'name'              => 'int1',
            'type'              => 'varchar',
            'length'            => 45,
            'primary_key'       => false,
            'auto_increment'    => false,
            'default'           => null,
            'conversion'        => 'int',
        ],
        'integer1' => [
            'name'           => 'integer1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'integer',
        ],
        'real1' => [
            // real = float = double
            'name'           => 'real1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'real',
        ],
        'float1' => [
            'name'           => 'float1',
            'type'           => 'int',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'float',
        ],
        'double1' => [
            'name'           => 'double1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'double',
        ],
        'string1' => [
            // string = str
            'name'           => 'string1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'string',
        ],
        'str1' => [
            'name'           => 'str1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'str',
        ],
        'bool1' => [
            // bool = boolean
            'name'           => 'bool1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'bool',
        ],
        'boolean1' => [
            'name'           => 'boolean1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'boolean',
        ],
        'obj1' => [
            // obj = object
            'name'           => 'obj1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'obj',
        ],
        'obj2' => [
            'name'           => 'obj2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'obj',
        ],
        'object1' => [
            'name'           => 'object1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'object',
        ],
        'object2' => [
            'name'           => 'object2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'object',
        ],
        'arr1' => [
            // arr = array = json
            'name'           => 'arr1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'arr',
        ],
        'arr2' => [
            'name'           => 'arr2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'arr',
        ],
        'array1' => [
            'name'           => 'array1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'array',
        ],
        'array2' => [
            'name'           => 'array2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'array',
        ],
        'json1' => [
            'name'           => 'json1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'json',
        ],
        'json2' => [
            'name'           => 'json2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'json',
        ],
        'coll1' => [
            'name'           => 'coll1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'collection',
        ],
        'coll2' => [
            'name'           => 'coll2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'collection',
        ],
        'collection1' => [
            'name'           => 'collection1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'collection',
        ],
        'collection2' => [
            'name'           => 'collection2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'collection',
        ],
        'date1' => [
            'name'           => 'date1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'date',
        ],
        'date2' => [
            'name'           => 'date2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'date',
        ],
        'date3' => [
            'name'           => 'date3',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'date',
        ],
        'date4' => [
            'name'           => 'date4',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'date',
        ],
        'date5' => [
            'name'           => 'date5',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'date',
        ],
        'datetime1' => [
            'name'           => 'datetime1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'datetime',
        ],
        'datetime2' => [
            'name'           => 'datetime2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'datetime',
        ],
        'datetime3' => [
            'name'           => 'datetime3',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'datetime',
        ],
        'datetime4' => [
            'name'           => 'datetime4',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'datetime',
        ],
        'datetime5' => [
            'name'           => 'datetime5',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'datetime',
        ],
        'time1' => [
            'name'           => 'time1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'time',
        ],
        'time2' => [
            'name'           => 'time2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'time',
        ],
        'time3' => [
            'name'           => 'time3',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'time',
        ],
        'time4' => [
            'name'           => 'time4',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'time',
        ],
        'time5' => [
            'name'           => 'time',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'time',
        ],
        'timestamp1' => [
            'name'           => 'timestamp1',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'timestamp',
        ],
        'timestamp2' => [
            'name'           => 'timestamp2',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'timestamp',
        ],
        'timestamp3' => [
            'name'           => 'timestamp3',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'timestamp',
        ],
        'timestamp4' => [
            'name'           => 'timestamp4',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'timestamp',
        ],
        'timestamp5' => [
            'name'           => 'timestamp5',
            'type'           => 'varchar',
            'length'         => 225,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
            'conversion'     => 'timestamp',
        ],
    ];
    protected $id;

    protected $int1;

    protected $integer1;

    protected $real1;

    protected $float1;

    protected $double1;

    protected $string1;

    protected $str1;

    protected $bool1;

    protected $boolean1;

    protected $obj1;

    protected $obj2;

    protected $object1;

    protected $object2;

    protected $arr1;

    protected $arr2;

    protected $array1;

    protected $array2;

    protected $json1;

    protected $json2;

    protected $coll1;

    protected $coll2;

    protected $collection1;

    protected $collection2;

    protected $date1;

    protected $date2;

    protected $date3;

    protected $date4;

    protected $date5;

    protected $datetime1;

    protected $datetime2;

    protected $datetime3;

    protected $datetime4;

    protected $datetime5;

    protected $time1;

    protected $time2;

    protected $time3;

    protected $time4;

    protected $time5;

    protected $timestamp1;

    protected $timestamp2;

    protected $timestamp3;

    protected $timestamp4;

    protected $timestamp5;
}
