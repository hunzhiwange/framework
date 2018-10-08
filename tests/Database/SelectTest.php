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

namespace Tests\Database;

use Leevel\Collection\Collection;
use PDO;
use stdClass;
use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * select test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.08
 *
 * @version 1.0
 */
class SelectTest extends TestCase
{
    use Query;

    protected function setUp()
    {
        $this->truncate('guestbook');
    }

    protected function tearDown()
    {
        $this->setUp();
    }

    public function testMaster()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test`",
    [],
    true,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                master(true)->

                findAll(true)
            )
        );
    }

    public function testMasterIsFalse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test`",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                master(false)->

                findAll(true)
            )
        );
    }

    public function testFetchArgs()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $result = $connect->table('guestbook')->

        fetchArgs(PDO::FETCH_BOTH)->

        where('id', 1)->

        findOne();

        $this->assertInternalType('array', $result);

        $this->assertSame('1', $result['id']);
        $this->assertSame('tom', $result['name']);
        $this->assertSame('I love movie.', $result['content']);
        $this->assertContains(date('Y-m-d'), $result['create_at']);
        $this->assertSame('1', $result[0]);
        $this->assertSame('tom', $result[1]);
        $this->assertSame('I love movie.', $result[2]);
        $this->assertContains(date('Y-m-d'), $result[3]);

        $this->truncate('guestbook');
    }

    public function testFetchArgsColumn()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; $n++) {
            $connect->table('guestbook')->

            insert($data);
        }

        $result = $connect->table('guestbook')->

        fetchArgs(PDO::FETCH_COLUMN, 0)->

        setColumns('name,content')->

        findAll();

        $json = <<<'eot'
[
    "tom",
    "tom",
    "tom",
    "tom",
    "tom",
    "tom"
]
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );

        $this->truncate('guestbook');
    }

    public function testFetchArgsClass()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $result = $connect->table('guestbook')->

        fetchArgs(PDO::FETCH_CLASS, FetchArgsClassDemo::class)->

        where('id', 1)->

        setColumns('name,content')->

        findOne();

        $json = <<<'eot'
{
    "name": "tom",
    "content": "I love movie."
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                (array) $result
            )
        );

        $this->assertInstanceof(FetchArgsClassDemo::class, $result);

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);

        $this->truncate('guestbook');
    }

    public function testFetchArgsClassWithArgs()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $result = $connect->table('guestbook')->

        fetchArgs(PDO::FETCH_CLASS, FetchArgsClassDemo2::class, ['foo', 'bar'])->

        where('id', 1)->

        setColumns('name,content')->

        findOne();

        $json = <<<'eot'
{
    "name": "tom",
    "content": "I love movie.",
    "arg1": "foo",
    "arg2": "bar"
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                (array) $result
            )
        );

        $this->assertInstanceof(FetchArgsClassDemo2::class, $result);

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);
        $this->assertSame('foo', $result->arg1);
        $this->assertSame('bar', $result->arg2);

        $this->truncate('guestbook');
    }

    public function testFetchArgsColumnGroup()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; $n++) {
            $connect->table('guestbook')->

            insert($data);
        }

        $data = ['name' => 'hello', 'content' => 'Test.'];

        for ($n = 0; $n <= 4; $n++) {
            $connect->table('guestbook')->

            insert($data);
        }

        $result = $connect->table('guestbook')->

        fetchArgs(PDO::FETCH_COLUMN | PDO::FETCH_GROUP, 0)->

        setColumns('name,content')->

        findAll();

        $json = <<<'eot'
{
    "tom": [
        "I love movie.",
        "I love movie.",
        "I love movie.",
        "I love movie.",
        "I love movie.",
        "I love movie."
    ],
    "hello": [
        "Test.",
        "Test.",
        "Test.",
        "Test.",
        "Test."
    ]
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );

        $this->truncate('guestbook');
    }

    public function testAsClass()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $result = $connect->table('guestbook')->

        asClass(AsClassDemo::class)->

        where('id', 1)->

        setColumns('name,content')->

        findOne();

        $json = <<<'eot'
{
    "name": "tom",
    "content": "I love movie."
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                (array) $result
            )
        );

        $this->assertInstanceof(AsClassDemo::class, $result);

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);

        $this->truncate('guestbook');
    }

    public function testAsCollectionAsDefault()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $result = $connect->table('guestbook')->

        asCollection()->

        where('id', 1)->

        setColumns('name,content')->

        findOne();

        $json = <<<'eot'
{
    "name": "tom",
    "content": "I love movie."
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result->toArray()
            )
        );

        $this->assertInstanceof(Collection::class, $result);

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);

        $this->truncate('guestbook');
    }

    public function testAsCollectionAsDefaultAndNotFound()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $result = $connect->table('guestbook')->

        asCollection()->

        where('id', 5)->

        setColumns('name,content')->

        findOne();

        $json = <<<'eot'
[
    "[]"
]
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                [(string) $result]
            )
        );

        $this->assertInstanceof(Collection::class, $result);

        $this->assertNull($result->name);
        $this->assertNull($result->content);

        $this->truncate('guestbook');
    }

    public function testAsCollectionAsDefaultFindAll()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; $n++) {
            $connect->table('guestbook')->

            insert($data);
        }

        $result = $connect->table('guestbook')->

        asCollection()->

        setColumns('name,content')->

        findAll();

        $json = <<<'eot'
[
    {
        "name": "tom",
        "content": "I love movie."
    },
    {
        "name": "tom",
        "content": "I love movie."
    },
    {
        "name": "tom",
        "content": "I love movie."
    },
    {
        "name": "tom",
        "content": "I love movie."
    },
    {
        "name": "tom",
        "content": "I love movie."
    },
    {
        "name": "tom",
        "content": "I love movie."
    }
]
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result->toArray()
            )
        );

        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame(6, count($result));

        $n = 0;

        foreach ($result as $key => $value) {
            $this->assertSame($key, $n);
            $this->assertInstanceof(stdClass::class, $value);
            $this->assertSame('tom', $value->name);
            $this->assertSame('I love movie.', $value->content);

            $n++;
        }

        $this->truncate('guestbook');
    }

    public function testAsCollectionAsClassFindAll()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; $n++) {
            $connect->table('guestbook')->

            insert($data);
        }

        $result = $connect->table('guestbook')->

        asCollection()->

        asClass(AsClassDemo::class)->

        setColumns('name,content')->

        findAll();

        $json = <<<'eot'
[
    {
        "name": "tom",
        "content": "I love movie."
    },
    {
        "name": "tom",
        "content": "I love movie."
    },
    {
        "name": "tom",
        "content": "I love movie."
    },
    {
        "name": "tom",
        "content": "I love movie."
    },
    {
        "name": "tom",
        "content": "I love movie."
    },
    {
        "name": "tom",
        "content": "I love movie."
    }
]
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result->toArray()
            )
        );

        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame(6, count($result));

        $n = 0;

        foreach ($result as $key => $value) {
            $this->assertSame($key, $n);
            $this->assertInstanceof(AsClassDemo::class, $value);
            $this->assertSame('tom', $value->name);
            $this->assertSame('I love movie.', $value->content);

            $n++;
        }

        $this->truncate('guestbook');
    }

    public function testValue()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $name = $connect->table('guestbook')->

        where('id', 1)->

        value('name');

        $content = $connect->table('guestbook')->

        where('id', 1)->

        pull('content');

        $this->assertSame('tom', $name);
        $this->assertSame('I love movie.', $content);

        $this->truncate('guestbook');
    }

    public function testList()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $result = $connect->table('guestbook')->

        where('id', 1)->

        list('name');

        $json = <<<'eot'
[
    "tom"
]
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );

        $this->truncate('guestbook');
    }

    public function testList2()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $result = $connect->table('guestbook')->

        where('id', 1)->

        list('content', 'name');

        $json = <<<'eot'
{
    "tom": "I love movie."
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );

        $this->truncate('guestbook');
    }

    public function testList3()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $result = $connect->table('guestbook')->

        where('id', 1)->

        list('content,name');

        $json = <<<'eot'
{
    "tom": "I love movie."
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );

        $this->truncate('guestbook');
    }

    public function testList4()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $connect->
        table('guestbook')->
        insert($data));

        $result = $connect->table('guestbook')->

        where('id', 1)->

        list(['content'], 'name');

        $json = <<<'eot'
{
    "tom": "I love movie."
}
eot;

        $this->assertSame(
            $json,
            $this->varJson(
                $result
            )
        );

        $this->truncate('guestbook');
    }

    public function testChunk()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; $n++) {
            $connect->table('guestbook')->

            insert($data);
        }

        $n = 1;

        $result = $connect->table('guestbook')->

        chunk(2, function ($result, $page) use (&$n) {
            $this->assertInstanceof(stdClass::class, $result[0]);
            $this->assertSame($n * 2 - 1, (int) $result[0]->id);
            $this->assertSame('tom', $result[0]->name);
            $this->assertSame('I love movie.', $result[0]->content);
            $this->assertContains(date('Y-m-d'), $result[0]->create_at);

            $this->assertInstanceof(stdClass::class, $result[1]);
            $this->assertSame($n * 2, (int) $result[1]->id);
            $this->assertSame('tom', $result[1]->name);
            $this->assertSame('I love movie.', $result[1]->content);
            $this->assertContains(date('Y-m-d'), $result[1]->create_at);

            $this->assertSame(2, count($result));
            $this->assertSame($n, $page);

            $n++;
        });

        $this->truncate('guestbook');
    }

    public function testEach()
    {
        $connect = $this->createConnectTest();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; $n++) {
            $connect->table('guestbook')->

            insert($data);
        }

        $n = $p = 1;

        $result = $connect->table('guestbook')->

        each(2, function ($value, $key, $page) use (&$n, &$p) {
            $this->assertInstanceof(stdClass::class, $value);
            $this->assertSame($n, (int) $value->id);
            $this->assertSame('tom', $value->name);
            $this->assertSame('I love movie.', $value->content);
            $this->assertContains(date('Y-m-d'), $value->create_at);
            $this->assertSame(($n + 1) % 2, $key);
            $this->assertSame($p, $page);

            if (1 === ($n + 1) % 2) {
                $p++;
            }

            $n++;
        });

        $this->truncate('guestbook');
    }
}

class FetchArgsClassDemo
{
    public $name;
    public $content;
}

class FetchArgsClassDemo2
{
    public $name;
    public $content;
    public $arg1;
    public $arg2;

    public function __construct(string $arg1, string $arg2)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }
}

class AsClassDemo
{
    public $name;
    public $content;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->content = $data['content'];
    }
}
