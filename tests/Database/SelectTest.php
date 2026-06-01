<?php

declare(strict_types=1);

namespace Tests\Database;

use Leevel\Cache\File;
use Leevel\Cache\ICache;
use Leevel\Database\Condition;
use Leevel\Database\Page;
use Leevel\Di\Container;
use Leevel\Filesystem\Helper\DeleteDirectory;
use Leevel\Kernel\Utils\Api;
use Leevel\Page\Page as BasePage;
use Leevel\Support\Collection;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Guestbook;

#[Api([
    'zh-CN:title' => '数据库查询',
    'path' => 'database/select',
])]
final class SelectTest extends TestCase
{
    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();

        $path = \dirname(__DIR__).'/databaseCacheManager';
        if (is_dir($path)) {
            DeleteDirectory::handle($path);
        }
    }

    #[Api([
        'zh-CN:title' => 'master 设置是否查询主服务器',
    ])]
    public function testMaster(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test`",
                [],
                true
            ]
            eot;

        self::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test')
                    ->master(true)
                    ->findAll(),
                $connect
            )
        );
    }

    public function testMasterIsFalse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test`",
                [],
                false
            ]
            eot;

        self::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test')
                    ->master(false)
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'asSome 设置以某种包装返会结果',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Database\AsSomeDemo**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\AsSomeDemo::class)]}
```
EOT,
    ])]
    public function testAsSome(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $result = $connect
            ->table('guest_book')
            ->asSome(static fn (mixed ...$args): AsSomeDemo => new AsSomeDemo(...$args))
            ->where('id', 1)
            ->setColumns('name,content')
            ->findOne()
        ;

        $json = <<<'eot'
            {
                "name": "tom",
                "content": "I love movie."
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                (array) $result
            )
        );

        $this->assertInstanceof(AsSomeDemo::class, $result);

        self::assertSame('tom', $result->name);
        self::assertSame('I love movie.', $result->content);
    }

    public function testAsCollectionAsDefault(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $result = $connect
            ->table('guest_book')
            ->asCollection()
            ->where('id', 1)
            ->setColumns('name,content')
            ->findOne()
        ;

        $json = <<<'eot'
            {
                "name": "tom",
                "content": "I love movie."
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                json_decode(json_encode($result), true)
            )
        );

        $this->assertInstanceof(\stdClass::class, $result);
        self::assertSame('tom', $result->name);
        self::assertSame('I love movie.', $result->content);
    }

    public function testAsCollectionAsDefaultAndNotFound(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $result = $connect
            ->table('guest_book')
            ->asCollection()
            ->where('id', 5)
            ->setColumns('name,content')
            ->findOne()
        ;

        $json = <<<'eot'
            []
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );

        self::assertIsArray($result);
    }

    #[Api([
        'zh-CN:title' => 'asCollection 设置是否以集合返回',
    ])]
    public function testAsCollectionAsDefaultFindAll(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $result = $connect
            ->table('guest_book')
            ->asCollection()
            ->setColumns('name,content')
            ->findAll()
        ;

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

        self::assertSame(
            $json,
            $this->varJson(
                $result->toArray()
            )
        );

        $this->assertInstanceof(Collection::class, $result);
        self::assertCount(6, $result);

        $n = 0;
        foreach ($result as $key => $value) {
            self::assertSame($key, $n);
            $this->assertInstanceof(\stdClass::class, $value);
            self::assertSame('tom', $value->name);
            self::assertSame('I love movie.', $value->content);
            ++$n;
        }
    }

    public function testAsCollectionAsSomeFindAll(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $result = $connect
            ->table('guest_book')
            ->asCollection()
            ->asSome(static fn (mixed ...$args): AsSomeDemo => new AsSomeDemo(...$args))
            ->setColumns('name,content')
            ->findAll()
        ;

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

        self::assertSame(
            $json,
            $this->varJson(
                $result->toArray()
            )
        );

        $this->assertInstanceof(Collection::class, $result);
        self::assertCount(6, $result);

        $n = 0;

        foreach ($result as $key => $value) {
            self::assertSame($key, $n);
            $this->assertInstanceof(AsSomeDemo::class, $value);
            self::assertSame('tom', $value->name);
            self::assertSame('I love movie.', $value->content);

            ++$n;
        }
    }

    public function testAsCollectionAsSomeFindAll2(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $result = $connect
            ->table('guest_book')
            ->asCollection()
            ->asSome(static fn (mixed ...$args): Guestbook => new Guestbook(...$args))
            ->setColumns('name,content')
            ->findAll()
        ;

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

        self::assertSame(
            $json,
            $this->varJson(
                $result->toArray()
            )
        );

        $this->assertInstanceof(Collection::class, $result);
        self::assertCount(6, $result);

        $n = 0;

        foreach ($result as $key => $value) {
            self::assertSame($key, $n);
            $this->assertInstanceof(Guestbook::class, $value);
            self::assertSame('tom', $value->name);
            self::assertSame('I love movie.', $value->content);

            ++$n;
        }
    }

    public function testAsCollectionAsSomeFindAll3(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $result = $connect
            ->table('guest_book')
            ->asCollection(true, [AsSomeDemo::class])
            ->asSome(static fn (mixed ...$args): AsSomeDemo => new AsSomeDemo(...$args))
            ->setColumns('name,content')
            ->findAll()
        ;

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

        self::assertSame(
            $json,
            $this->varJson(
                $result->toArray()
            )
        );

        $this->assertInstanceof(Collection::class, $result);
        self::assertCount(6, $result);

        $n = 0;

        foreach ($result as $key => $value) {
            self::assertSame($key, $n);
            $this->assertInstanceof(AsSomeDemo::class, $value);
            self::assertSame('tom', $value->name);
            self::assertSame('I love movie.', $value->content);

            ++$n;
        }
    }

    public function testAsCollectionAsSomeFindAll4(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid collection value types.'
        );

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $connect
            ->table('guest_book')
            ->asCollection(true, [true])
            ->setColumns('name,content')
            ->findAll()
        ;
    }

    public function testAsCollectionAsSomeFindAll5(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid collection value types.'
        );

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $connect
            ->table('guest_book')
            ->asCollection(true, [''])
            ->setColumns('name,content')
            ->findAll()
        ;
    }

    public function testAsCollectionAsSomeFindAll6(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid collection value types.'
        );

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $connect
            ->table('guest_book')
            ->asCollection(true, [1])
            ->setColumns('name,content')
            ->findAll()
        ;
    }

    public function testAsCollectionAsSomeFindAll7(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid collection value types.'
        );

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $connect
            ->table('guest_book')
            ->asCollection(true, ['NotFoundClass'])
            ->setColumns('name,content')
            ->findAll()
        ;
    }

    #[Api([
        'zh-CN:title' => 'asArray 设置返会结果为数组',
    ])]
    public function testAsArray(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $result = $connect
            ->table('guest_book')
            ->asArray()
            ->where('id', 1)
            ->setColumns('name,content')
            ->findOne()
        ;

        $json = <<<'eot'
            {
                "name": "tom",
                "content": "I love movie."
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );

        self::assertIsArray($result);
        self::assertSame('tom', $result['name']);
        self::assertSame('I love movie.', $result['content']);
    }

    #[Api([
        'zh-CN:title' => 'asArray 设置返会结果为数组支持闭包处理',
    ])]
    public function testAsArrayWithClosure(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $result = $connect
            ->table('guest_book')
            ->asArray(static function (array $value): array {
                $value['hello'] = 'world';

                return $value;
            })
            ->where('id', 1)
            ->setColumns('name,content')
            ->findOne()
        ;

        $json = <<<'eot'
            {
                "name": "tom",
                "content": "I love movie.",
                "hello": "world"
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );

        self::assertIsArray($result);
        self::assertSame('tom', $result['name']);
        self::assertSame('I love movie.', $result['content']);
    }

    #[Api([
        'zh-CN:title' => 'value 返回一个字段的值',
    ])]
    public function testValue(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $name = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->value('name')
        ;

        $content = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->value('content')
        ;

        self::assertSame('tom', $name);
        self::assertSame('I love movie.', $content);
    }

    #[Api([
        'zh-CN:title' => 'list 返回一列数据',
    ])]
    public function testList(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $result = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->findList('name')
        ;

        $json = <<<'eot'
            [
                "tom"
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'list 返回一列数据支持 2 个字段',
    ])]
    public function testList2(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $result = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->findList('content', 'name')
        ;

        $json = <<<'eot'
            {
                "tom": "I love movie."
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'list 返回一列数据支持英文逗号分隔字段',
    ])]
    public function testList3(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $result = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->findList('content,name')
        ;

        $json = <<<'eot'
            {
                "tom": "I love movie."
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    public function testList4(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert($data),
        );

        $result = $connect
            ->table('guest_book')
            ->where('id', 1)
            ->findList(['content'], 'name')
        ;

        $json = <<<'eot'
            {
                "tom": "I love movie."
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'chunk 数据分块处理',
    ])]
    public function testChunk(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $n = 1;
        $connect
            ->table('guest_book')
            ->chunk(2, function ($result, $page) use (&$n): void {
                $this->assertInstanceof(\stdClass::class, $result[0]);
                $this->assertSame($n * 2 - 1, (int) $result[0]->id);
                $this->assertSame('tom', $result[0]->name);
                $this->assertSame('I love movie.', $result[0]->content);
                $this->assertStringContainsString(date('Y-m'), $result[0]->create_at);

                $this->assertInstanceof(\stdClass::class, $result[1]);
                $this->assertSame($n * 2, (int) $result[1]->id);
                $this->assertSame('tom', $result[1]->name);
                $this->assertSame('I love movie.', $result[1]->content);
                $this->assertStringContainsString(date('Y-m'), $result[1]->create_at);

                $this->assertCount(2, $result);
                $this->assertSame($n, $page);

                ++$n;
            })
        ;
    }

    #[Api([
        'zh-CN:title' => 'chunk 数据分块处理支持返回 false 中断',
    ])]
    public function testChunkWhenReturnFalseAndBreak(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $n = 1;
        $connect
            ->table('guest_book')
            ->chunk(2, function ($result, $page) use (&$n) {
                $this->assertInstanceof(\stdClass::class, $result[0]);
                $this->assertSame($n * 2 - 1, (int) $result[0]->id);
                $this->assertSame('tom', $result[0]->name);
                $this->assertSame('I love movie.', $result[0]->content);
                $this->assertStringContainsString(date('Y-m'), $result[0]->create_at);

                $this->assertInstanceof(\stdClass::class, $result[1]);
                $this->assertSame($n * 2, (int) $result[1]->id);
                $this->assertSame('tom', $result[1]->name);
                $this->assertSame('I love movie.', $result[1]->content);
                $this->assertStringContainsString(date('Y-m'), $result[1]->create_at);

                $this->assertCount(2, $result);
                $this->assertSame($n, $page);

                // It will break.
                if (2 === $n) {
                    return false;
                }

                ++$n;
            })
        ;
    }

    #[Api([
        'zh-CN:title' => 'each 数据分块处理依次回调',
    ])]
    public function testEach(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $n = $p = 1;
        $connect
            ->table('guest_book')
            ->each(2, function ($value, $key, $page) use (&$n, &$p): void {
                $this->assertInstanceof(\stdClass::class, $value);
                $this->assertSame($n, (int) $value->id);
                $this->assertSame('tom', $value->name);
                $this->assertSame('I love movie.', $value->content);
                $this->assertStringContainsString(date('Y-m'), $value->create_at);
                $this->assertSame(($n + 1) % 2, $key);
                $this->assertSame($p, $page);

                if (1 === ($n + 1) % 2) {
                    ++$p;
                }

                ++$n;
            })
        ;

        self::assertSame(7, $n);
    }

    #[Api([
        'zh-CN:title' => 'each 数据分块处理依次回调支持返回 false 中断',
    ])]
    public function testEachBreak(): void
    {
        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $n = $p = 1;
        $connect
            ->table('guest_book')
            ->each(2, function ($value, $key, $page) use (&$n, &$p) {
                if (3 === $n) {
                    return false;
                }

                $this->assertInstanceof(\stdClass::class, $value);
                $this->assertSame($n, (int) $value->id);
                $this->assertSame('tom', $value->name);
                $this->assertSame('I love movie.', $value->content);
                $this->assertStringContainsString(date('Y-m'), $value->create_at);
                $this->assertSame(($n + 1) % 2, $key);
                $this->assertSame($p, $page);

                if (1 === ($n + 1) % 2) {
                    ++$p;
                }

                ++$n;
            })
        ;

        self::assertSame(3, $n);
    }

    #[Api([
        'zh-CN:title' => 'page 分页查询',
    ])]
    public function testPage(): void
    {
        $this->initI18n();

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 25; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $page = $connect
            ->table('guest_book')
            ->findPageList(1, count: 26)
        ;
        $result = $page->toArray()['data'];

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        self::assertCount(10, $result);

        $n = 0;
        foreach ($result as $key => $value) {
            self::assertSame($key, $n);
            $this->assertInstanceof(\stdClass::class, $value);
            self::assertSame('tom', $value->name);
            self::assertSame('I love movie.', $value->content);

            ++$n;
        }

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 26 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page=2">2</a></li><li class="number"><a href="?page=3">3</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        self::assertSame(
            $data,
            $page->render()
        );

        self::assertSame(
            $data,
            $page->toHtml()
        );

        self::assertSame(
            $data,
            $page->__toString()
        );

        self::assertSame(
            $data,
            (string) $page
        );

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_page": 3,
                "total_record": 26,
                "total_macro": false,
                "from": 0,
                "to": 10
            }
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $page->toArray()['page']
            )
        );

        self::assertSame(
            $data,
            $this->varJson(
                $page->jsonSerialize()['page']
            )
        );

        $data = <<<'eot'
            {"per_page":10,"current_page":1,"total_page":3,"total_record":26,"total_macro":false,"from":0,"to":10}
            eot;

        self::assertSame(
            $data,
            json_encode($page->toArray()['page'])
        );

        $this->clearI18n();
    }

    #[Api([
        'zh-CN:title' => 'page 分页带条件查询',
    ])]
    public function testPageWithCondition(): void
    {
        $this->initI18n();

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 25; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $page = $connect
            ->table('guest_book')
            ->where('id', '>', 23)
            ->where(static function ($select): void {
                $select->orWhere('content', 'like', '%l%')
                    ->orWhere('content', 'like', '%o%')
                    ->orWhere('content', 'like', '%m%')
                ;
            })
            ->findPageList(1, count: 3)
        ;
        $result = $page->toArray()['data'];

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        self::assertCount(3, $result);

        $n = 0;
        foreach ($result as $key => $value) {
            self::assertSame($key, $n);
            $this->assertInstanceof(\stdClass::class, $value);
            self::assertSame('tom', $value->name);
            self::assertSame('I love movie.', $value->content);

            ++$n;
        }

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 3 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next disabled">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        self::assertSame(
            $data,
            $page->render()
        );

        self::assertSame(
            $data,
            $page->toHtml()
        );

        self::assertSame(
            $data,
            $page->__toString()
        );

        self::assertSame(
            $data,
            (string) $page
        );

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_page": 1,
                "total_record": 3,
                "total_macro": false,
                "from": 0,
                "to": 3
            }
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $page->toArray()['page']
            )
        );

        self::assertSame(
            $data,
            $this->varJson(
                $page->jsonSerialize()['page']
            )
        );

        $data = <<<'eot'
            {"per_page":10,"current_page":1,"total_page":1,"total_record":3,"total_macro":false,"from":0,"to":3}
            eot;

        self::assertSame(
            $data,
            json_encode($page->toArray()['page'])
        );

        $this->clearI18n();
    }

    #[Api([
        'zh-CN:title' => 'pageMacro 创建一个无限数据的分页查询',
    ])]
    public function testPageMacro(): void
    {
        $this->initI18n();

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 25; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $page = $connect
            ->table('guest_book')
            ->findPageMacro(1)
        ;
        $result = $page->toArray()['data'];

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        self::assertCount(10, $result);

        $n = 0;
        foreach ($result as $key => $value) {
            self::assertSame($key, $n);
            $this->assertInstanceof(\stdClass::class, $value);
            self::assertSame('tom', $value->name);
            self::assertSame('I love movie.', $value->content);

            ++$n;
        }

        $data = <<<'eot'
            <div class="pagination">  <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page=2">2</a></li><li class="number"><a href="?page=3">3</a></li><li class="number"><a href="?page=4">4</a></li><li class="number"><a href="?page=5">5</a></li><li class="number"><a href="?page=6">6</a></li> <li class="btn-quicknext" onclick="window.location.href='?page=6';" onmouseenter="this.innerHTML='&raquo;';" onmouseleave="this.innerHTML='...';">...</li> </ul> <button class="btn-next" onclick="window.location.href='?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        self::assertSame(
            $data,
            $page->render()
        );

        self::assertSame(
            $data,
            $page->toHtml()
        );

        self::assertSame(
            $data,
            $page->__toString()
        );

        self::assertSame(
            $data,
            (string) $page
        );

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_page": 100000000,
                "total_record": 999999999,
                "total_macro": true,
                "from": 0,
                "to": null
            }
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $page->toArray()['page']
            )
        );

        self::assertSame(
            $data,
            $this->varJson(
                $page->jsonSerialize()['page']
            )
        );

        $data = <<<'eot'
            {"per_page":10,"current_page":1,"total_page":100000000,"total_record":999999999,"total_macro":true,"from":0,"to":null}
            eot;

        self::assertSame(
            $data,
            json_encode($page->toArray()['page'])
        );

        $this->clearI18n();
    }

    #[Api([
        'zh-CN:title' => 'pagePrevNext 创建一个只有上下页的分页查询',
    ])]
    public function testPagePrevNext(): void
    {
        $this->initI18n();

        $connect = $this->createDatabaseConnect();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 25; ++$n) {
            $connect
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $page = $connect
            ->table('guest_book')
            ->findPagePrevNext(1, 15)
        ;
        $result = $page->toArray()['data'];

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        self::assertCount(15, $result);

        $n = 0;
        foreach ($result as $key => $value) {
            self::assertSame($key, $n);
            $this->assertInstanceof(\stdClass::class, $value);
            self::assertSame('tom', $value->name);
            self::assertSame('I love movie.', $value->content);

            ++$n;
        }

        $data = <<<'eot'
            <div class="pagination">  <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next" onclick="window.location.href='?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        self::assertSame(
            $data,
            $page->render()
        );

        self::assertSame(
            $data,
            $page->toHtml()
        );

        self::assertSame(
            $data,
            $page->__toString()
        );

        self::assertSame(
            $data,
            (string) $page
        );

        $data = <<<'eot'
            {
                "per_page": 15,
                "current_page": 1,
                "total_page": null,
                "total_record": null,
                "total_macro": false,
                "from": 0,
                "to": null
            }
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $page->toArray()['page']
            )
        );

        self::assertSame(
            $data,
            $this->varJson(
                $page->jsonSerialize()['page']
            )
        );

        $data = <<<'eot'
            {"per_page":15,"current_page":1,"total_page":null,"total_record":null,"total_macro":false,"from":0,"to":null}
            eot;

        self::assertSame(
            $data,
            json_encode($page->toArray()['page'])
        );

        $this->clearI18n();
    }

    #[Api([
        'zh-CN:title' => 'forPage 根据分页设置条件',
    ])]
    public function testForPage(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 114,6",
                [],
                false
            ]
            eot;

        self::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test')
                    ->forPage(20, 6)
                    ->findAll(),
                $connect
            )
        );
    }

    public function testParseFormNotSet(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT 2",
                [],
                false
            ]
            eot;

        self::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->setColumns(Condition::raw('2'))
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'makeSql 获得查询字符串',
    ])]
    public function testMakeSql(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test`"
            ]
            eot;

        self::assertSame(
            $sql,
            $this->varJson(
                [
                    $connect
                        ->table('test')
                        ->makeSql(),
                ]
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'makeSql 获得查询字符串支持集合为一个条件',
    ])]
    public function testMakeSqlWithLogicGroup(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "(SELECT `test`.* FROM `test`)"
            ]
            eot;

        self::assertSame(
            $sql,
            $this->varJson(
                [
                    $connect
                        ->table('test')
                        ->makeSql(true),
                ]
            )
        );
    }

    public function testRunNativeSqlWithProcedureAsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $connect->select('CALL test_procedure(1)');

        $data = <<<'eot'
            [
                "CALL test_procedure(1)",
                [],
                false
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson($connect->getRealLastSql())
        );
    }

    public function testRunNativeSqlTypeInvalid(): void
    {
        self::markTestSkipped('Skip query only allowed select.');

        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            'SQLSTATE[HY000]: General error: 2014 Cannot execute queries while other unbuffered queries are active.  Consider using PDOStatement::fetchAll().  Alternatively, if your code is only ever going to run against mysql, you may enable query buffering by setting the PDO::MYSQL_ATTR_USE_BUFFERED_QUERY attribute.'
        );

        $connect = $this->createDatabaseConnect();
        // 由用户自己保证使用 query,procedure 还是 execute，系统不加限制，减少底层设计复杂度
        $result = $connect->select('DELETE FROM test WHERE id = 1');
        self::assertSame([], $result);
    }

    public function testFindByFooAndBarArgsWasNotMatched(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to undefined method Leevel\Database\Mysql::findByNameAndTitle()');

        $connect = $this->createDatabaseConnectMock();
        $connect->findByNameAndTitle('one');
    }

    public function testFindNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to undefined method Leevel\Database\Mysql::findPage()');

        $connect = $this->createDatabaseConnectMock();
        $connect->findPage();
    }

    #[Api([
        'zh-CN:title' => 'cache 设置查询缓存',
        'zh-CN:description' => <<<'EOT'
**cache 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Database\Select::class, 'cache', 'define')]}
```
EOT,
    ])]
    public function testCache(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $this->assertInstanceof(ICache::class, $manager->getCache());
        $result = $manager
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;
        self::assertFileDoesNotExist($cacheFile);
        self::assertSame(2, $result->id);
        self::assertSame('tom', $result->name);
        self::assertSame('I love movie.', $result->content);

        $resultWithoutCache = $manager
            ->cache('testcachekey')
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;
        // cached data
        $resultWithCache = $manager
            ->cache('testcachekey')
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;

        self::assertFileExists($cacheFile);
        self::assertSame(2, $resultWithCache->id);
        self::assertSame('tom', $resultWithCache->name);
        self::assertSame('I love movie.', $resultWithCache->content);
        self::assertEquals($result, $resultWithCache);
        self::assertEquals($resultWithCache, $resultWithoutCache);
        self::assertFalse($result === $resultWithCache);
        self::assertFalse($resultWithCache === $resultWithoutCache);
    }

    #[Api([
        'zh-CN:title' => 'cache 设置查询缓存支持过期时间',
    ])]
    public function testCacheWithExpire(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;
        self::assertFileDoesNotExist($cacheFile);
        self::assertSame(2, $result->id);
        self::assertSame('tom', $result->name);
        self::assertSame('I love movie.', $result->content);

        $resultWithoutCache = $manager
            ->cache('testcachekey', 3600)
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;
        // cached data
        $resultWithCache = $manager
            ->cache('testcachekey', 3600)
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;

        self::assertFileExists($cacheFile);
        self::assertStringContainsString('[3600,', file_get_contents($cacheFile));
        self::assertSame(2, $resultWithCache->id);
        self::assertSame('tom', $resultWithCache->name);
        self::assertSame('I love movie.', $resultWithCache->content);
        self::assertEquals($result, $resultWithCache);
        self::assertEquals($resultWithCache, $resultWithoutCache);
        self::assertFalse($result === $resultWithCache);
        self::assertFalse($resultWithCache === $resultWithoutCache);
    }

    #[Api([
        'zh-CN:title' => 'cache 设置查询缓存支持缓存连接',
    ])]
    public function testCacheWithConnect(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;
        self::assertFileDoesNotExist($cacheFile);
        self::assertSame(2, $result->id);
        self::assertSame('tom', $result->name);
        self::assertSame('I love movie.', $result->content);

        $fileCache = $manager
            ->container()
            ->make('cache')
        ;
        $this->assertInstanceof(ICache::class, $fileCache);
        $this->assertInstanceof(File::class, $fileCache);

        $resultWithoutCache = $manager
            ->cache('testcachekey', 3600, $fileCache)
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;
        // cached data
        $resultWithCache = $manager
            ->cache('testcachekey', 3600, $fileCache)
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;

        self::assertFileExists($cacheFile);
        self::assertStringContainsString('[3600,', file_get_contents($cacheFile));
        self::assertSame(2, $resultWithCache->id);
        self::assertSame('tom', $resultWithCache->name);
        self::assertSame('I love movie.', $resultWithCache->content);
        self::assertEquals($result, $resultWithCache);
        self::assertEquals($resultWithCache, $resultWithoutCache);
        self::assertFalse($result === $resultWithCache);
        self::assertFalse($resultWithCache === $resultWithoutCache);
    }

    #[Api([
        'zh-CN:title' => 'cache 设置查询缓存支持查询多条记录',
    ])]
    public function testCacheFindAll(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->table('guest_book')
            ->findAll()
        ;
        self::assertFileDoesNotExist($cacheFile);
        self::assertCount(6, $result);
        self::assertSame(1, $result[0]->id);
        self::assertSame('tom', $result[0]->name);
        self::assertSame('I love movie.', $result[0]->content);

        $resultWithoutCache = $manager
            ->cache('testcachekey')
            ->table('guest_book')
            ->findAll()
        ;
        // cached data
        $resultWithCache = $manager
            ->cache('testcachekey')
            ->table('guest_book')
            ->findAll()
        ;

        self::assertFileExists($cacheFile);
        self::assertCount(6, $resultWithCache);
        self::assertSame(1, $resultWithCache[0]->id);
        self::assertSame('tom', $resultWithCache[0]->name);
        self::assertSame('I love movie.', $resultWithCache[0]->content);
        self::assertEquals($result, $resultWithCache);
        self::assertFalse($result === $resultWithCache);
        self::assertEquals($resultWithCache, $resultWithoutCache);
    }

    #[Api([
        'zh-CN:title' => 'cache 设置查询缓存支持查询单条记录',
    ])]
    public function testCacheFindOne(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;
        self::assertFileDoesNotExist($cacheFile);
        self::assertSame(2, $result->id);
        self::assertSame('tom', $result->name);
        self::assertSame('I love movie.', $result->content);

        $resultWithoutCache = $manager
            ->cache('testcachekey')
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;
        // cached data
        $resultWithCache = $manager
            ->cache('testcachekey')
            ->table('guest_book')
            ->where('id', 2)
            ->findOne()
        ;

        self::assertFileExists($cacheFile);
        self::assertSame(2, $resultWithCache->id);
        self::assertSame('tom', $resultWithCache->name);
        self::assertSame('I love movie.', $resultWithCache->content);
        self::assertEquals($result, $resultWithCache);
        self::assertFalse($result === $resultWithCache);
        self::assertEquals($resultWithCache, $resultWithoutCache);
    }

    #[Api([
        'zh-CN:title' => 'cache 设置查询缓存支持查询总记录',
    ])]
    public function testCacheFindCount(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->table('guest_book')
            ->findCount()
        ;
        self::assertFileDoesNotExist($cacheFile);
        self::assertSame(6, $result);

        $resultWithoutCache = $manager
            ->cache('testcachekey')
            ->table('guest_book')
            ->findCount()
        ;
        // cached data
        $resultWithCache = $manager
            ->cache('testcachekey')
            ->table('guest_book')
            ->findCount()
        ;

        self::assertFileExists($cacheFile);
        self::assertSame(6, $resultWithCache);
        self::assertSame($result, $resultWithCache);
        self::assertTrue($result === $resultWithCache);
        self::assertSame($resultWithCache, $resultWithoutCache);
    }

    #[Api([
        'zh-CN:title' => 'cache 设置查询缓存支持 select 查询方法',
    ])]
    public function testCacheSelect(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->table('guest_book')
            ->select('SELECT * FROM guest_book')
        ;
        self::assertFileDoesNotExist($cacheFile);
        self::assertCount(6, $result);
        self::assertSame(1, $result[0]->id);
        self::assertSame('tom', $result[0]->name);
        self::assertSame('I love movie.', $result[0]->content);

        $resultWithoutCache = $manager
            ->cache('testcachekey')
            ->select('SELECT * FROM guest_book')
        ;
        // cached data
        $resultWithCache = $manager
            ->cache('testcachekey')
            ->select('SELECT * FROM guest_book')
        ;

        self::assertFileExists($cacheFile);
        self::assertCount(6, $resultWithCache);
        self::assertSame(1, $resultWithCache[0]->id);
        self::assertSame('tom', $resultWithCache[0]->name);
        self::assertSame('I love movie.', $resultWithCache[0]->content);
        self::assertEquals($result, $resultWithCache);
        self::assertFalse($result === $resultWithCache);
        self::assertEquals($resultWithCache, $resultWithoutCache);
    }

    #[Api([
        'zh-CN:title' => 'cache 设置查询缓存支持分页查询',
        'zh-CN:description' => <<<'EOT'
分页查询会生成两个缓存 KEY，一种是缓存数据本身，一个是缓存分页统计数量。

分页统计数量缓存 KEY 需要加一个后缀与分页数据区分，KEY 后缀为 `\Leevel\Database\Select::PAGE_COUNT_CACHE_SUFFIX`。
EOT,
    ])]
    public function testCachePage(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 25; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->table('guest_book')
            ->findPageList(1)
        ;
        self::assertFileDoesNotExist($cacheFile);

        $resultWithoutCache = $manager
            ->cache('testcachekey')
            ->table('guest_book')
            ->findPageList(1)
        ;
        // cached data
        $resultWithCache = $manager
            ->cache('testcachekey')
            ->table('guest_book')
            ->findPageList(1)
        ;

        self::assertFileExists($cacheFile);
        self::assertFalse($result === $resultWithCache);
        self::assertEquals($resultWithCache, $resultWithoutCache);
    }

    #[Api([
        'zh-CN:title' => 'cache 设置查询缓存不支持 query 查询方法',
        'zh-CN:description' => <<<'EOT'
`query` 是一个底层查询方法支持直接设置缓存，实际上其它的查询都会走这个 `query` 查询方法。

**query 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Database\Database::class, 'query', 'define')]}
```
EOT,
    ])]
    public function testCacheQuery(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 5; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->table('guest_book')
            ->query('SELECT * FROM guest_book')
        ;
        self::assertFileDoesNotExist($cacheFile);
        self::assertCount(6, $result);
        self::assertSame(1, $result[0]->id);
        self::assertSame('tom', $result[0]->name);
        self::assertSame('I love movie.', $result[0]->content);

        $resultWithoutCache = $manager
            ->query('SELECT * FROM guest_book', [], false, 'testcachekey')
        ;
        // cached data
        $resultWithCache = $manager
            ->query('SELECT * FROM guest_book', [], false, 'testcachekey')
        ;

        self::assertFileExists($cacheFile);
        self::assertCount(6, $resultWithCache);
        self::assertSame(1, $resultWithCache[0]->id);
        self::assertSame('tom', $resultWithCache[0]->name);
        self::assertSame('I love movie.', $resultWithCache[0]->content);
        self::assertEquals($result, $resultWithCache);
        self::assertFalse($result === $resultWithCache);
        self::assertEquals($resultWithCache, $resultWithoutCache);
    }

    #[Api([
        'zh-CN:title' => 'cache 设置查询缓存不支持 procedure 查询方法',
        'zh-CN:description' => <<<'EOT'
`procedure` 是一个底层查询方法支持直接设置缓存。

**procedure 原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Database\Database::class, 'procedure', 'define')]}
```
EOT,
    ])]
    public function testCacheProcedure(): void
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        for ($n = 0; $n <= 1; ++$n) {
            $manager
                ->table('guest_book')
                ->insert($data)
            ;
        }

        $cacheDir = \dirname(__DIR__).'/databaseCacheManager';
        $cacheFile = $cacheDir.'/testcachekey.php';

        $result = $manager
            ->procedure('CALL test_procedure(0)')
        ;

        self::assertFileDoesNotExist($cacheFile);
        $data = <<<'eot'
            [
                [
                    {
                        "name": "tom"
                    },
                    {
                        "name": "tom"
                    }
                ],
                [
                    {
                        "content": "I love movie."
                    }
                ],
                []
            ]
            eot;
        self::assertSame(
            $data,
            $this->varJson(
                $result
            )
        );

        $resultWithoutCache = $manager
            ->procedure('CALL test_procedure(0)', [], false, 'testcachekey')
        ;
        self::assertFileExists($cacheFile);
        // cached data
        $resultWithCache = $manager
            ->procedure('CALL test_procedure(0)', [], false, 'testcachekey')
        ;
        self::assertFileExists($cacheFile);
        self::assertSame(
            $data,
            $this->varJson(
                $resultWithCache
            )
        );
        self::assertEquals($result, $resultWithCache);
        self::assertFalse($result === $resultWithCache);
        self::assertEquals($resultWithCache, $resultWithoutCache);
    }

    public function testCacheButCacheWasNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cache was not set.');

        $connect = $this->createDatabaseConnect();
        $connect
            ->cache('testcachekey')
            ->table('guest_book')
            ->findOne()
        ;
    }

    public function testCacheQueryButCacheWasNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cache was not set.');

        $connect = $this->createDatabaseConnect();
        $connect->query('SELECT * FROM guest_book', [], false, 'testcachekey');
    }

    public function testCacheProcedureButCacheWasNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cache was not set.');

        $connect = $this->createDatabaseConnect();
        $connect->procedure('CALL test_procedure(0)', [], false, 'testcachekey');
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book'];
    }

    protected function initI18n(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', static function (): \I18nMock {
            return new \I18nMock();
        });
    }

    protected function clearI18n(): void
    {
        Container::singletons()->clear();
    }
}

class AsSomeDemo
{
    public string $name = '';
    public string $content = '';

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->content = $data['content'];
    }
}
