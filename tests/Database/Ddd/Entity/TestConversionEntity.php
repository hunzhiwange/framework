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

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            'readonly' => true,
        ],
        'int1' => [
            // int = integer
            'conversion'        => 'int',
        ],
        'integer1' => [
            'conversion'     => 'integer',
        ],
        'real1' => [
            // real = float = double
            'conversion'     => 'real',
        ],
        'float1' => [
            'conversion'     => 'float',
        ],
        'double1' => [
            'conversion'     => 'double',
        ],
        'string1' => [
            // string = str
            'conversion'     => 'string',
        ],
        'str1' => [
            'conversion'     => 'str',
        ],
        'bool1' => [
            // bool = boolean
            'conversion'     => 'bool',
        ],
        'boolean1' => [
            'conversion'     => 'boolean',
        ],
        'obj1' => [
            // obj = object
            'conversion'     => 'obj',
        ],
        'obj2' => [
            'conversion'     => 'obj',
        ],
        'object1' => [
            'conversion'     => 'object',
        ],
        'object2' => [
            'conversion'     => 'object',
        ],
        'arr1' => [
            // arr = array = json
            'conversion'     => 'arr',
        ],
        'arr2' => [
            'conversion'     => 'arr',
        ],
        'array1' => [
            'conversion'     => 'array',
        ],
        'array2' => [
            'conversion'     => 'array',
        ],
        'json1' => [
            'conversion'     => 'json',
        ],
        'json2' => [
            'conversion'     => 'json',
        ],
        'coll1' => [
            'conversion'     => 'collection',
        ],
        'coll2' => [
            'conversion'     => 'collection',
        ],
        'collection1' => [
            'conversion'     => 'collection',
        ],
        'collection2' => [
            'conversion'     => 'collection',
        ],
        'date1' => [
            'conversion'     => 'date',
        ],
        'date2' => [
            'conversion'     => 'date',
        ],
        'date3' => [
            'conversion'     => 'date',
        ],
        'date4' => [
            'conversion'     => 'date',
        ],
        'date5' => [
            'conversion'     => 'date',
        ],
        'datetime1' => [
            'conversion'     => 'datetime',
        ],
        'datetime2' => [
            'conversion'     => 'datetime',
        ],
        'datetime3' => [
            'conversion'     => 'datetime',
        ],
        'datetime4' => [
            'conversion'     => 'datetime',
        ],
        'datetime5' => [
            'conversion'     => 'datetime',
        ],
        'time1' => [
            'conversion'     => 'time',
        ],
        'time2' => [
            'conversion'     => 'time',
        ],
        'time3' => [
            'conversion'     => 'time',
        ],
        'time4' => [
            'conversion'     => 'time',
        ],
        'time5' => [
            'conversion'     => 'time',
        ],
        'timestamp1' => [
            'conversion'     => 'timestamp',
        ],
        'timestamp2' => [
            'conversion'     => 'timestamp',
        ],
        'timestamp3' => [
            'conversion'     => 'timestamp',
        ],
        'timestamp4' => [
            'conversion'     => 'timestamp',
        ],
        'timestamp5' => [
            'conversion'     => 'timestamp',
        ],
    ];

    private $id;

    private $int1;

    private $integer1;

    private $real1;

    private $float1;

    private $double1;

    private $string1;

    private $str1;

    private $bool1;

    private $boolean1;

    private $obj1;

    private $obj2;

    private $object1;

    private $object2;

    private $arr1;

    private $arr2;

    private $array1;

    private $array2;

    private $json1;

    private $json2;

    private $coll1;

    private $coll2;

    private $collection1;

    private $collection2;

    private $date1;

    private $date2;

    private $date3;

    private $date4;

    private $date5;

    private $datetime1;

    private $datetime2;

    private $datetime3;

    private $datetime4;

    private $datetime5;

    private $time1;

    private $time2;

    private $time3;

    private $time4;

    private $time5;

    private $timestamp1;

    private $timestamp2;

    private $timestamp3;

    private $timestamp4;

    private $timestamp5;

    public function setter(string $prop, $value): void
    {
        $this->{$prop} = $value;
    }

    public function getter(string $prop)
    {
        return $this->{$prop};
    }
}
