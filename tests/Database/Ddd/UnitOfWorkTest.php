<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\UnitOfWork;
use Leevel\Database\DuplicateKeyException;
use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\CompositeId;
use Tests\Database\Ddd\Entity\Guestbook;
use Tests\Database\Ddd\Entity\GuestbookRepository;
use Tests\Database\Ddd\Entity\Relation\Post;

#[Api([
    'zh-CN:title' => '事务工作单元',
    'path' => 'orm/unitOfWork',
    'zh-CN:description' => <<<'EOT'
用事务工作单元更好地处理数据库相关工作。
EOT,
])]
final class UnitOfWorkTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        if (isset($GLOBALS['unitOfWork'])) {
            unset($GLOBALS['unitOfWork']);
        }
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        if (isset($GLOBALS['unitOfWork'])) {
            unset($GLOBALS['unitOfWork']);
        }
    }

    #[Api([
        'zh-CN:title' => '保存一个实体',
        'zh-CN:note' => <<<'EOT'
通过 persist 方法保存一个实体，并通过 flush 将实体持久化到数据库。
EOT,
    ])]
    public function testBaseUse(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post([
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]);

        self::assertNull($post->id);

        $work->persist($post);

        $work->flush();

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
    }

    #[Api([
        'zh-CN:title' => '保存多个实体',
        'zh-CN:note' => <<<'EOT'
底层会开启一个事务，只有全部保存成功才会真正持久化到数据库。
EOT,
    ])]
    public function testPersist(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post([
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]);

        self::assertNull($post->id);

        $post2 = new Post([
            'title' => 'hello world',
            'user_id' => 2,
            'summary' => 'foo bar',
        ]);

        self::assertNull($post2->id);

        $work->persist($post);
        $work->persist($post2);
        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
    }

    public function testPersistBefore(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post([
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]);

        self::assertNull($post->id);

        $post2 = new Post([
            'title' => 'hello world',
            'user_id' => 2,
            'summary' => 'foo bar',
        ]);

        self::assertNull($post2->id);

        $work->persist($post);
        $work->persistBefore($post2);

        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                1,
                2
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertSame(2, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);

        self::assertSame(1, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
    }

    public function testPersistAfter(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post([
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]);

        self::assertNull($post->id);

        $post2 = new Post([
            'title' => 'hello world',
            'user_id' => 2,
            'summary' => 'foo bar',
        ]);

        self::assertNull($post2->id);

        $work->persistAfter($post2);
        $work->persist($post);

        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
    }

    #[Api([
        'zh-CN:title' => '新增实体',
        'zh-CN:note' => <<<'EOT'
底层执行的是 insert 语句，只有全部保存成功才会真正持久化到数据库。
EOT,
    ])]
    public function testCreate(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post([
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]);

        $post2 = new Post([
            'title' => 'hello world',
            'user_id' => 2,
            'summary' => 'foo bar',
        ]);

        self::assertNull($post->id);
        self::assertNull($post2->id);
        self::assertFalse($work->created($post));
        self::assertFalse($work->created($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $work->create($post);
        $work->create($post2);

        self::assertTrue($work->created($post));
        self::assertTrue($work->created($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->created($post));
        self::assertTrue($work->created($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
    }

    public function testCreateBefore(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post([
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]);

        $post2 = new Post([
            'title' => 'hello world',
            'user_id' => 2,
            'summary' => 'foo bar',
        ]);

        self::assertNull($post->id);
        self::assertNull($post2->id);
        self::assertFalse($work->created($post));
        self::assertFalse($work->created($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $work->create($post);
        $work->createBefore($post2);

        self::assertTrue($work->created($post));
        self::assertTrue($work->created($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                1,
                2
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->created($post));
        self::assertTrue($work->created($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        self::assertSame(2, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);

        self::assertSame(1, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
    }

    public function testCreateAfter(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post([
            'title' => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]);

        $post2 = new Post([
            'title' => 'hello world',
            'user_id' => 2,
            'summary' => 'foo bar',
        ]);

        self::assertNull($post->id);
        self::assertNull($post2->id);
        self::assertFalse($work->created($post));
        self::assertFalse($work->created($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $work->createAfter($post2);
        $work->create($post);

        self::assertTrue($work->created($post));
        self::assertTrue($work->created($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->created($post));
        self::assertTrue($work->created($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
    }

    #[Api([
        'zh-CN:title' => '更新实体',
        'zh-CN:note' => <<<'EOT'
底层执行的是 update 语句，只有全部保存成功才会真正持久化到数据库。
EOT,
    ])]
    public function testUpdate(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 2,
                    'summary' => 'foo bar',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);

        $post2 = Post::select()->findEntity(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
        self::assertSame('hello world', $post2->title);

        self::assertFalse($work->updated($post));
        self::assertFalse($work->updated($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $post->title = 'new post title';
        $post->summary = 'new post summary';

        $post2->title = 'new post2 title';
        $post2->summary = 'new post2 summary';

        $work->update($post);
        $work->update($post2);

        self::assertTrue($work->updated($post));
        self::assertTrue($work->updated($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->updated($post));
        self::assertTrue($work->updated($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('new post title', $post->title);
        self::assertSame('new post summary', $post->summary);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('new post2 title', $post2->title);
        self::assertSame('new post2 summary', $post2->summary);
    }

    public function testUpdateBefore(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 2,
                    'summary' => 'foo bar',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);

        $post2 = Post::select()->findEntity(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
        self::assertSame('hello world', $post2->title);

        self::assertFalse($work->updated($post));
        self::assertFalse($work->updated($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $post->title = 'new post title';
        $post->summary = 'new post summary';

        $post2->title = 'new post2 title';
        $post2->summary = 'new post2 summary';

        $work->update($post2);
        $work->updateBefore($post);

        self::assertTrue($work->updated($post));
        self::assertTrue($work->updated($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->updated($post));
        self::assertTrue($work->updated($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('new post title', $post->title);
        self::assertSame('new post summary', $post->summary);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('new post2 title', $post2->title);
        self::assertSame('new post2 summary', $post2->summary);
    }

    public function testUpdateAfter(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 2,
                    'summary' => 'foo bar',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);

        $post2 = Post::select()->findEntity(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
        self::assertSame('hello world', $post2->title);

        self::assertFalse($work->updated($post));
        self::assertFalse($work->updated($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $post->title = 'new post title';
        $post->summary = 'new post summary';

        $post2->title = 'new post2 title';
        $post2->summary = 'new post2 summary';

        $work->updateAfter($post2);
        $work->update($post);

        self::assertTrue($work->updated($post));
        self::assertTrue($work->updated($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->updated($post));
        self::assertTrue($work->updated($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('new post title', $post->title);
        self::assertSame('new post summary', $post->summary);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('new post2 title', $post2->title);
        self::assertSame('new post2 summary', $post2->summary);
    }

    #[Api([
        'zh-CN:title' => '删除实体',
        'zh-CN:note' => <<<'EOT'
底层执行的是 delete 语句，只有全部保存成功才会真正持久化到数据库。
EOT,
    ])]
    public function testDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 2,
                    'summary' => 'foo bar',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);

        $post2 = Post::select()->findEntity(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
        self::assertSame('hello world', $post2->title);

        self::assertFalse($work->deleted($post));
        self::assertFalse($work->deleted($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $work->delete($post);
        $work->delete($post2);
        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $postAfter = Post::select()->findEntity(1);
        $post2After = Post::select()->findEntity(2);

        self::assertNull($postAfter->id);
        self::assertNull($postAfter->userId);
        self::assertNull($postAfter->title);
        self::assertNull($postAfter->summary);

        self::assertNull($post2After->id);
        self::assertNull($post2After->userId);
        self::assertNull($post2After->title);
        self::assertNull($post2After->summary);

        $postAfter = Post::withSoftDeleted()->findEntity(1);
        $post2After = Post::withSoftDeleted()->findEntity(2);

        self::assertSame(1, $postAfter->id);
        self::assertSame(1, $postAfter->userId);
        self::assertSame('post summary', $postAfter->summary);
        self::assertSame('hello world', $postAfter->title);

        self::assertSame(2, $post2After->id);
        self::assertSame(2, $post2After->userId);
        self::assertSame('foo bar', $post2After->summary);
        self::assertSame('hello world', $post2After->title);
    }

    public function testDeleteBefore(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 2,
                    'summary' => 'foo bar',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);
        $post2 = Post::select()->findEntity(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
        self::assertSame('hello world', $post2->title);

        self::assertFalse($work->deleted($post));
        self::assertFalse($work->deleted($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $work->delete($post);
        $work->deleteBefore($post2);
        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->flush();

        $data = <<<'eot'
            [
                1,
                2
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $postAfter = Post::select()->findEntity(1);
        $post2After = Post::select()->findEntity(2);

        self::assertNull($postAfter->id);
        self::assertNull($postAfter->userId);
        self::assertNull($postAfter->title);
        self::assertNull($postAfter->summary);

        self::assertNull($post2After->id);
        self::assertNull($post2After->userId);
        self::assertNull($post2After->title);
        self::assertNull($post2After->summary);

        $postAfter = Post::withSoftDeleted()->findEntity(1);
        $post2After = Post::withSoftDeleted()->findEntity(2);

        self::assertSame(1, $postAfter->id);
        self::assertSame(1, $postAfter->userId);
        self::assertSame('post summary', $postAfter->summary);
        self::assertSame('hello world', $postAfter->title);

        self::assertSame(2, $post2After->id);
        self::assertSame(2, $post2After->userId);
        self::assertSame('foo bar', $post2After->summary);
        self::assertSame('hello world', $post2After->title);
    }

    public function testDeleteAfter(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 2,
                    'summary' => 'foo bar',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);
        $post2 = Post::select()->findEntity(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
        self::assertSame('hello world', $post2->title);

        self::assertFalse($work->deleted($post));
        self::assertFalse($work->deleted($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $work->deleteAfter($post);
        $work->delete($post2);
        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->flush();

        $data = <<<'eot'
            [
                1,
                2
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $postAfter = Post::select()->findEntity(1);
        $post2After = Post::select()->findEntity(2);

        self::assertNull($postAfter->id);
        self::assertNull($postAfter->userId);
        self::assertNull($postAfter->title);
        self::assertNull($postAfter->summary);

        self::assertNull($post2After->id);
        self::assertNull($post2After->userId);
        self::assertNull($post2After->title);
        self::assertNull($post2After->summary);

        $postAfter = Post::withSoftDeleted()->findEntity(1);
        $post2After = Post::withSoftDeleted()->findEntity(2);

        self::assertSame(1, $postAfter->id);
        self::assertSame(1, $postAfter->userId);
        self::assertSame('post summary', $postAfter->summary);
        self::assertSame('hello world', $postAfter->title);

        self::assertSame(2, $post2After->id);
        self::assertSame(2, $post2After->userId);
        self::assertSame('foo bar', $post2After->summary);
        self::assertSame('hello world', $post2After->title);
    }

    public function testForceDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 2,
                    'summary' => 'foo bar',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);

        $post2 = Post::select()->findEntity(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
        self::assertSame('hello world', $post2->title);

        self::assertFalse($work->deleted($post));
        self::assertFalse($work->deleted($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $work->forceDelete($post);
        $work->forceDelete($post2);
        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $postAfter = Post::select()->findEntity(1);
        $post2After = Post::select()->findEntity(2);

        self::assertNull($postAfter->id);
        self::assertNull($postAfter->userId);
        self::assertNull($postAfter->title);
        self::assertNull($postAfter->summary);

        self::assertNull($post2After->id);
        self::assertNull($post2After->userId);
        self::assertNull($post2After->title);
        self::assertNull($post2After->summary);

        $postAfter = Post::withSoftDeleted()->findEntity(1);
        $post2After = Post::withSoftDeleted()->findEntity(2);

        self::assertNull($postAfter->id);
        self::assertNull($postAfter->userId);
        self::assertNull($postAfter->title);
        self::assertNull($postAfter->summary);

        self::assertNull($post2After->id);
        self::assertNull($post2After->userId);
        self::assertNull($post2After->title);
        self::assertNull($post2After->summary);
    }

    public function testForceDeleteBefore(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 2,
                    'summary' => 'foo bar',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);

        $post2 = Post::select()->findEntity(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
        self::assertSame('hello world', $post2->title);

        self::assertFalse($work->deleted($post));
        self::assertFalse($work->deleted($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $work->forceDelete($post);
        $work->forceDeleteBefore($post2);
        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->flush();

        $data = <<<'eot'
            [
                1,
                2
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $postAfter = Post::select()->findEntity(1);
        $post2After = Post::select()->findEntity(2);

        self::assertNull($postAfter->id);
        self::assertNull($postAfter->userId);
        self::assertNull($postAfter->title);
        self::assertNull($postAfter->summary);

        self::assertNull($post2After->id);
        self::assertNull($post2After->userId);
        self::assertNull($post2After->title);
        self::assertNull($post2After->summary);

        $postAfter = Post::withSoftDeleted()->findEntity(1);
        $post2After = Post::withSoftDeleted()->findEntity(2);

        self::assertNull($postAfter->id);
        self::assertNull($postAfter->userId);
        self::assertNull($postAfter->title);
        self::assertNull($postAfter->summary);

        self::assertNull($post2After->id);
        self::assertNull($post2After->userId);
        self::assertNull($post2After->title);
        self::assertNull($post2After->summary);
    }

    public function testForceDeleteAfter(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 2,
                    'summary' => 'foo bar',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);

        $post2 = Post::select()->findEntity(2);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Entity::class, $post2);
        $this->assertInstanceof(Post::class, $post);
        $this->assertInstanceof(Post::class, $post2);

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);

        self::assertSame(2, $post2->id);
        self::assertSame(2, $post2->userId);
        self::assertSame('foo bar', $post2->summary);
        self::assertSame('hello world', $post2->title);

        self::assertFalse($work->deleted($post));
        self::assertFalse($work->deleted($post2));
        self::assertFalse($work->registered($post));
        self::assertFalse($work->registered($post2));

        $work->forceDeleteAfter($post2);
        $work->forceDelete($post);
        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->deleted($post));
        self::assertTrue($work->deleted($post2));
        self::assertTrue($work->registered($post));
        self::assertTrue($work->registered($post2));

        $postAfter = Post::select()->findEntity(1);
        $post2After = Post::select()->findEntity(2);

        self::assertNull($postAfter->id);
        self::assertNull($postAfter->userId);
        self::assertNull($postAfter->title);
        self::assertNull($postAfter->summary);

        self::assertNull($post2After->id);
        self::assertNull($post2After->userId);
        self::assertNull($post2After->title);
        self::assertNull($post2After->summary);

        $postAfter = Post::withSoftDeleted()->findEntity(1);
        $post2After = Post::withSoftDeleted()->findEntity(2);

        self::assertNull($postAfter->id);
        self::assertNull($postAfter->userId);
        self::assertNull($postAfter->title);
        self::assertNull($postAfter->summary);

        self::assertNull($post2After->id);
        self::assertNull($post2After->userId);
        self::assertNull($post2After->title);
        self::assertNull($post2After->summary);
    }

    #[Api([
        'zh-CN:title' => '刷新实体',
        'zh-CN:note' => <<<'EOT'
底层执行的是 select 语句，这个操作会读取数据库最新信息并刷新实体的属性。
EOT,
    ])]
    public function testRefresh(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $post = new Post([
            'id' => 1,
            'title' => 'old',
            'summary' => 'old',
        ], true);

        self::assertSame(1, $post->id);
        self::assertSame('old', $post->summary);
        self::assertSame('old', $post->title);

        $work->persist($post);
        $work->refresh($post);

        self::assertSame(1, $post->id);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);
        $post->title = 'new title';

        $work->flush();

        $post = Post::select()->findEntity(1);

        $this->assertInstanceof(Entity::class, $post);
        $this->assertInstanceof(Post::class, $post);

        self::assertSame(1, $post->id);
        self::assertSame(1, $post->userId);
        self::assertSame('post summary', $post->summary);
        self::assertSame('new title', $post->title);
    }

    public function testRefreshButUpdateNoDataAndDoNothing(): void
    {
        $work = UnitOfWork::make();
        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $post = new Post([
            'id' => 1,
            'title' => 'old',
            'summary' => 'old',
        ], true);

        self::assertSame(1, $post->id);
        self::assertSame('old', $post->summary);
        self::assertSame('old', $post->title);

        $work->persist($post);
        $work->refresh($post);

        self::assertSame(1, $post->id);
        self::assertSame('post summary', $post->summary);
        self::assertSame('hello world', $post->title);

        self::assertNull($work->flush());
    }

    #[Api([
        'zh-CN:title' => '手工启动事务 beginTransaction',
        'zh-CN:note' => <<<'EOT'
通常来说事务工作单元会自动帮你处理事务，可以通过手工 beginTransaction，成功 commit 或者失败 rollBack，系统提供了 API 让你也手工开启事务处理。
EOT,
    ])]
    public function testBeginTransaction(): void
    {
        $work = UnitOfWork::make(new Post());

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $work->beginTransaction();


        $post = Post::select()->findEntity(1);
        $work->update($post);

        try {
            $post->title = 'new title';
            $work->commit();
        } catch (\Throwable) {
            $work->close();
            $work->rollBack();
        }

        self::assertSame(1, $post->id);
        self::assertSame('new title', $post->title);
    }

    #[Api([
        'zh-CN:title' => '执行失败事务回滚 rollBack',
        'zh-CN:note' => <<<'EOT'
底层会自动运行一个事务，如果执行失败自动回滚，不会更新数据库。
EOT,
    ])]
    public function testFlushButRollBack(): void
    {
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage(
            // MySQL8:SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1' for key 'post.PRIMARY'
            // MySQL5.7:SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate '1' for key 'PRIMARY'
            'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'1\' for key'
        );

        $work = UnitOfWork::make();

        $post = new Post([
            'id' => 1,
            'title' => 'old',
            'summary' => 'old',
            'user_id' => 0,
        ]);

        $post2 = new Post([
            'id' => 1,
            'title' => 'old',
            'summary' => 'old',
            'user_id' => 0,
        ]);

        $work->create($post);
        $work->create($post2);

        $work->flush();
    }

    public function test2(): void
    {
        $e = new DuplicateKeyException();
        $e->setUniqueIndex('hello');
        self::assertSame('hello', $e->getUniqueIndex());
    }

    #[Api([
        'zh-CN:title' => '事务包裹在闭包中 transaction',
        'zh-CN:note' => <<<'EOT'
可以将事务包裹在一个闭包中，如果执行失败自动回滚，不会更新数据库。
EOT,
    ])]
    public function testTransaction(): void
    {
        $work = UnitOfWork::make(new Post());

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $work->transaction(static function (UnitOfWork $w): void {
            $post = Post::select()->findEntity(1);
            $w->update($post);

            $post->title = 'new title';
        });

        $newPost = Post::select()->findEntity(1);

        self::assertSame(1, $newPost->id);
        self::assertSame('new title', $newPost->title);
    }

    #[Api([
        'zh-CN:title' => '事务包裹在闭包中失败回滚 transaction',
        'zh-CN:note' => <<<'EOT'
可以将事务包裹在一个闭包中，执行失败自动回滚测试，不会更新数据库。
EOT,
    ])]
    public function testTransactionAndRollBack(): void
    {
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage(
            // MySQL8:SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1' for key 'post.PRIMARY'
            // MySQL5.7:SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1' for key 'PRIMARY'
            'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'1\' for key'
        );

        $work = UnitOfWork::make(new Post());

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        $work->transaction(static function ($w): void {
            $post = new Post([
                'id' => 1,
                'title' => 'old',
                'summary' => 'old',
                'user_id' => 0,
            ]);

            $post2 = new Post([
                'id' => 1,
                'title' => 'old',
                'summary' => 'old',
                'user_id' => 0,
            ]);

            $w->create($post);
            $w->create($post2);
        });

        self::assertSame(0, $connect->table('post')->findCount());
    }

    #[Api([
        'zh-CN:title' => '设置实体 setEntity',
        'zh-CN:note' => <<<'EOT'
系统默认读取基础的数据库配置来处理数据相关信息，设置跟实体还可以更改事务处理的数据库连接。
EOT,
    ])]
    public function testSetRootEntity(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);
        $work->setEntity($post);

        $work->update($post);

        $post->title = 'new title';

        $work->flush();

        self::assertSame(1, $post->id);
        self::assertSame('new title', $post->title);

        $newPost = Post::select()->findEntity(1);

        self::assertSame(1, $newPost->id);
        self::assertSame('new title', $newPost->title);

        $work->setEntity($post);
    }

    public function testFlushButNotFoundAny(): void
    {
        $work = UnitOfWork::make(new Post());

        self::assertNull($work->flush());
    }

    #[Api([
        'zh-CN:title' => '保持实体支持缓存',
        'zh-CN:note' => <<<'EOT'
保存两个一样的实体，第二个实体并不会被添加。
EOT,
    ])]
    public function testPersistStageManagedEntityDoNothing(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $post = new Post([
            'id' => 1,
            'title' => 'old',
            'summary' => 'old',
            'user_id' => 0,
        ]);

        $work->persist($post, 'create');
        $work->persist($post, 'create');

        $work->flush();

        self::assertSame(1, $connect->table('post')->findCount());
    }

    #[Api([
        'zh-CN:title' => '重新保存已删除的实体实体',
        'zh-CN:note' => <<<'EOT'
这样被删除的实体并不会被删除。
EOT,
    ])]
    public function testPersistStageRemovedEntity(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);
        self::assertSame(1, $post->id);
        self::assertSame('hello world', $post->title);
        self::assertSame('post summary', $post->summary);
        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
        $work->delete($post);
        self::assertSame(UnitOfWork::STATE_REMOVED, $work->getEntityState($post));
        $work->persist($post);
        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
        $work->flush();
        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
        self::assertSame(1, $connect->table('post')->findCount());
    }

    public function testPersistStageForceRemovedEntity(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);
        self::assertSame(1, $post->id);
        self::assertSame('hello world', $post->title);
        self::assertSame('post summary', $post->summary);
        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
        $work->forceDelete($post);
        self::assertSame(UnitOfWork::STATE_REMOVED, $work->getEntityState($post));
        $work->persist($post);
        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
        $work->flush();
        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
        self::assertSame(1, $connect->table('post')->findCount());
    }

    #[Api([
        'zh-CN:title' => '注册更新的实体不能重新被创建',
    ])]
    public function testCreateButAlreadyInUpdates(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Updated entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for create.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'foo']);

        $work->update($post);

        $work->create($post);
    }

    #[Api([
        'zh-CN:title' => '注册删除的实体不能重新被创建',
    ])]
    public function testCreateButAlreadyInDeletes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Deleted entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for create.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5]);

        $work->delete($post);

        $work->create($post);
    }

    #[Api([
        'zh-CN:title' => '注册替换的实体不能重新被创建',
    ])]
    public function testCreateButAlreadyInReplaces(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Replaced entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for create.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5]);

        $work->replace($post);

        $work->create($post);
    }

    #[Api([
        'zh-CN:title' => '不能多次创建同一个实体',
    ])]
    public function testCreateManyTimes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for twice.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $post = new Post(['title' => 'foo']);

        $work->create($post);
        $work->create($post);
    }

    #[Api([
        'zh-CN:title' => '已经删除的实体不能够被更新',
    ])]
    public function testUpdateButAlreadyInDeletes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Deleted entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for update.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->delete($post);

        $work->update($post);
    }

    #[Api([
        'zh-CN:title' => '已经创建的实体不能够被更新',
    ])]
    public function testUpdateButAlreadyInCreates(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Created entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for update.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->create($post);

        $work->update($post);
    }

    #[Api([
        'zh-CN:title' => '已经替换的实体不能够被更新',
    ])]
    public function testUpdateButAlreadyInReplaces(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Replaced entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for update.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->replace($post);

        $work->update($post);
    }

    #[Api([
        'zh-CN:title' => 'update 不能多次更新同一个实体',
    ])]
    public function testUpdateManyTimes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be updated for twice.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $post = new Post(['id' => 1, 'title' => 'foo']);

        $work->update($post);
        $work->update($post);
    }

    public function testUpdateButHasNoPrimaryData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` has no unique key data for update.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $post = new Post(['title' => 'foo']);

        $work->update($post);
    }

    #[Api([
        'zh-CN:title' => 'delete.create 已创建的实体可以被删除',
    ])]
    public function testDeleteCreated(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $post = new Post(['title' => 'foo', 'id' => 5]);

        $work->create($post);
        $work->delete($post);

        $work->flush();

        self::assertSame(0, $connect->table('post')->findCount());
    }

    public function testDeleteCreatedWithoutPrimaryKeyData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` has no unique key data for delete.'
        );

        $work = UnitOfWork::make();
        $post = new Post(['title' => 'foo']);

        $work->create($post);
        $work->delete($post);
    }

    #[Api([
        'zh-CN:title' => 'delete.update 删除已更新的实体',
    ])]
    public function testDeleteUpdated(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);

        $work->update($post);
        $work->delete($post);

        $post->title = 'new';

        $work->flush();

        $postNew = Post::select()->findEntity(1);

        self::assertSame(1, $connect->table('post')->findCount());
        self::assertSame(0, $connect->table('post')->where('delete_at', 0)->findCount());
        self::assertNull($postNew->id);
        self::assertNull($postNew->title);
    }

    #[Api([
        'zh-CN:title' => 'delete.replace 删除已替换的实体',
    ])]
    public function testDeleteReplaced(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);

        $work->replace($post);
        $work->delete($post);

        $post->title = 'new';

        $work->flush();

        $postNew = Post::select()->findEntity(1);

        self::assertSame(1, $connect->table('post')->findCount());
        self::assertSame(0, $connect->table('post')->where('delete_at', 0)->findCount());
        self::assertNull($postNew->id);
        self::assertNull($postNew->title);
    }

    public function testRefreshButNotIsStageManaged(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not managed.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->delete($post);

        $work->refresh($post);
    }

    public function testFlushButUnitOfWorkWasClosed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unit of work has closed.'
        );

        $work = UnitOfWork::make();
        $work->close();
        $post = new Post(['id' => 5, 'title' => 'new']);
        $work->persist($post);
        $work->flush();
    }

    #[Api([
        'zh-CN:title' => 'repository 取得实体仓储',
    ])]
    public function testRepository(): void
    {
        $work = UnitOfWork::make();

        $repository = $work->repository(Guestbook::class);

        $this->assertInstanceof(GuestbookRepository::class, $repository);
    }

    #[Api([
        'zh-CN:title' => 'repository 取得实体仓储支持实体实例',
    ])]
    public function testRepository2(): void
    {
        $work = UnitOfWork::make();

        $repository = $work->repository(new Guestbook());

        $this->assertInstanceof(GuestbookRepository::class, $repository);
    }

    #[Api([
        'zh-CN:title' => 'remove 移除未被管理的实体不做任何处理直接返回',
    ])]
    public function testRemoveStageNewDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->remove($post = new Post());

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除管理的新增实体直接删除',
    ])]
    public function testRemoveStageCreateManaged(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->create($post = new Post(['id' => 5]));
        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));
        $work->remove($post);
        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除管理的更新实体直接删除',
    ])]
    public function testRemoveStageUpdateManaged(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->update($post = new Post(['id' => 5], true));
        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));
        $work->remove($post);
        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除未被管理的实体到前置区域不做任何处理直接返回',
    ])]
    public function testRemoveBeforeStageNewDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->removeBefore($post = new Post());

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除未被管理的实体到后置区域不做任何处理直接返回',
    ])]
    public function testRemoveAfterBeforeStageNewDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->removeAfter($post = new Post());

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除未被管理的实体不做任何处理直接返回',
    ])]
    public function testForceRemoveStageNewDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->forceRemove($post = new Post());

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除未被管理的实体到前置区域不做任何处理直接返回',
    ])]
    public function testForceRemoveBeforeStageNewDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->forceRemoveBefore($post = new Post());

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除未被管理的实体到后置区域不做任何处理直接返回',
    ])]
    public function testForceRemoveAfterStageNewDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->forceRemoveAfter($post = new Post());

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除已删除的实体不做任何处理直接返回',
    ])]
    public function testRemoveStageRemovedDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->delete($post = new Post(['id' => 5]));
        $work->remove($post);

        self::assertSame(UnitOfWork::STATE_REMOVED, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除已删除的实体到前置区域不做任何处理直接返回',
    ])]
    public function testRemoveBeforeStageRemovedDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->delete($post = new Post(['id' => 5]));
        $work->removeBefore($post);

        self::assertSame(UnitOfWork::STATE_REMOVED, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除已删除的实体到后置区域不做任何处理直接返回',
    ])]
    public function testRemoveAfterStageRemovedDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->delete($post = new Post(['id' => 5]));
        $work->removeAfter($post);

        self::assertSame(UnitOfWork::STATE_REMOVED, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除已删除的实体不做任何处理直接返回',
    ])]
    public function testForceRemoveStageRemovedDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->delete($post = new Post(['id' => 5]));
        $work->forceRemove($post);

        self::assertSame(UnitOfWork::STATE_REMOVED, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除已删除的实体到前置区域不做任何处理直接返回',
    ])]
    public function testForceRemoveBeforeStageRemovedDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->delete($post = new Post(['id' => 5]));
        $work->forceRemoveBefore($post);

        self::assertSame(UnitOfWork::STATE_REMOVED, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除已删除的实体到后置区域不做任何处理直接返回',
    ])]
    public function testForceRemoveAfterStageRemovedDoNothing(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $work->delete($post = new Post(['id' => 5]));
        $work->forceRemoveAfter($post);

        self::assertSame(UnitOfWork::STATE_REMOVED, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除已经被管理的新增实体将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testRemoveStageManagedWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post);

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->remove($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除已经被管理的新增实体到前置区域将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testRemoveBeforeStageManagedWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post);

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->removeBefore($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除已经被管理的新增实体到后置区域将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testRemoveAfterStageManagedWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post);

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->removeAfter($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除已经被管理的新增实体将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testForceRemoveStageManagedWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post);

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->forceRemove($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->flush();

        $sql = '';
        self::assertSame(
            $sql,
            $post->select()->getLastSql(),
        );
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除已经被管理的新增实体到前置区域将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testForceRemoveBeforeStageManagedWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post);

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->forceRemoveBefore($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->flush();

        $sql = '';
        self::assertSame(
            $sql,
            $post->select()->getLastSql(),
        );
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除已经被管理的新增实体到后置区域将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testForceRemoveAfterStageManagedWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post);

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->forceRemoveAfter($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->flush();

        $sql = '';
        self::assertSame(
            $sql,
            $post->select()->getLastSql(),
        );
    }

    #[Api([
        'zh-CN:title' => 'remove 移除已经被管理的替换实体将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testRemoveStageManagedReplaceWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post, 'replace');

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->remove($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除已经被管理的替换实体到前置区域将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testRemoveBeforeStageManagedReplaceWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post, 'replace');

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->removeBefore($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'remove 移除已经被管理的替换实体到后置区域将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testRemoveAfterStageManagedReplaceWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post, 'replace');

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->removeAfter($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除已经被管理的替换实体将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testForceRemoveStageManagedReplaceWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post, 'replace');

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->forceRemove($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除已经被管理的替换实体到前置区域将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testForceRemoveBeforeStageManagedReplaceWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post, 'replace');

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->forceRemoveBefore($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'forceRemove 强制移除已经被管理的替换实体到后置区域将会清理已管理状态，但是不做删除然后直接返回',
    ])]
    public function testForceRemoveAfterStageManagedReplaceWillDelete(): void
    {
        $work = UnitOfWork::make();

        $this->assertInstanceof(UnitOfWork::class, $work);

        $post = new Post();

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));

        $work->persist($post, 'replace');

        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));

        $work->forceRemoveAfter($post);

        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => 'persist 保持实体自动识别为更新状态',
    ])]
    public function testPersistAsSaveUpdate(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $post = new Post([
            'id' => 1,
            'title' => 'old',
            'summary' => 'old',
        ], true);

        $work->persist($post);

        $work->flush();

        self::assertSame(0, $connect->table('post')->findCount());
    }

    #[Api([
        'zh-CN:title' => 'persist 保持实体为更新状态',
    ])]
    public function testPersistAsUpdate(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $post = new Post([
            'id' => 1,
            'title' => 'old',
            'summary' => 'old',
        ]);

        $work->persist($post, 'update');

        $work->flush();

        self::assertSame(0, $connect->table('post')->findCount());
    }

    #[Api([
        'zh-CN:title' => 'persist 保持实体为替换状态',
    ])]
    public function testPersistAsReplace(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $post = new Post([
            'id' => 1,
            'title' => 'old',
            'summary' => 'old',
            'user_id' => 1,
        ]);

        $work->persist($post, 'replace');

        $work->flush();

        $updatedPost = Post::select()->findEntity(1);

        self::assertSame(1, $updatedPost->id);
        self::assertSame('old', $updatedPost->title);
        self::assertSame(1, $updatedPost->userId);
        self::assertSame('old', $updatedPost->summary);
    }

    #[Api([
        'zh-CN:title' => 'persist 已经持久化并且脱离管理的实体状态不能被再次保持',
    ])]
    public function testPersistStageDetachedEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Detached entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be persist.'
        );

        $work = UnitOfWork::make();
        $post = new Post(['id' => 5, 'title' => 'new']);
        $work->persist($post);
        $work->flush();
        $work->persist($post);
    }

    #[Api([
        'zh-CN:title' => 'remove 已经持久化并且脱离管理的实体状态不能被再次移除',
    ])]
    public function testRemoveStageDetachedEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Detached entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be remove.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->persist($post);

        $work->flush($post);

        $work->remove($post);
    }

    #[Api([
        'zh-CN:title' => 'on 保持的实体回调',
    ])]
    public function testOnCallbacks(): void
    {
        $work = UnitOfWork::make();

        $post = new Post([
            'title' => 'new',
            'user_id' => 0,
        ]);
        $guestBook = new Guestbook(['name' => '']);

        $work->persist($post);
        $work->persist($guestBook);

        $work->on($post, static function ($p) use ($guestBook): void {
            $guestBook->content = 'guest_book content was post id is '.$p->id;
        });

        $work->flush($post);

        $newGuestbook = Guestbook::select()->findEntity(1);

        self::assertSame('guest_book content was post id is 1', $newGuestbook->content);

        $work->clear();
    }

    #[Api([
        'zh-CN:title' => 'on 替换的实体回调',
    ])]
    public function testOnCallbacksForReplace(): void
    {
        $work = UnitOfWork::make();

        $post = new Post([
            'title' => 'new',
            'user_id' => 0,
        ]);
        $guestBook = new Guestbook(['name' => '']);

        $work->replace($post);
        $work->replace($guestBook);

        $work->on($post, static function ($p) use ($guestBook): void {
            $guestBook->content = 'guest_book content was post id is '.$p->id;
        });

        $work->flush($post);

        $newGuestbook = Guestbook::select()->findEntity(1);

        self::assertSame('guest_book content was post id is 1', $newGuestbook->content);

        $work->clear();
    }

    #[Api([
        'zh-CN:title' => 'on 更新的实体回调',
    ])]
    public function testOnCallbacksForUpdate(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name' => '',
                    'content' => 'hello world',
                ])
        );

        $post = new Post(['id' => 1, 'title' => 'new'], true);
        $guestBook = new Guestbook(['id' => 1], true);

        $work->update($post);
        $work->update($guestBook);

        $work->on($post, static function ($p) use ($guestBook): void {
            $guestBook->content = 'guest_book content was post id is '.$p->id;
        });

        $post->title = 'new new';

        $work->flush($post);

        $newGuestbook = Guestbook::select()->findEntity(1);

        self::assertSame('guest_book content was post id is 1', $newGuestbook->content);

        $work->clear();
    }

    public function testOnCallbacksForUpdateButUpdateNoDataAndDoNothing(): void
    {
        $work = UnitOfWork::make();
        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        self::assertSame(
            1,
            $connect
                ->table('guest_book')
                ->insert([
                    'name' => '',
                    'content' => 'hello world',
                ])
        );

        $post = new Post(['id' => 1, 'title' => 'new'], true);
        $guestBook = new Guestbook(['id' => 1], true);

        $work->update($post);
        $work->update($guestBook);

        $work->on($post, static function ($p) use ($guestBook): void {
            $guestBook->content = 'guest_book content was post id is '.$p->id;
        });

        self::assertNull($work->flush($post));
    }

    #[Api([
        'zh-CN:title' => 'on 删除的实体回调',
    ])]
    public function testOnCallbacksForDelete(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $post = Post::select()->findEntity(1);
        $work->persist($post)->remove($post);

        $work->on($post, static function ($p): void {
            // post has already removed,do nothing
        });

        $work->flush($post);

        $newPost = Post::select()->findEntity(1);
        self::assertSame(1, $newPost->id);
        $work->clear();
    }

    #[Api([
        'zh-CN:title' => 'replace 注册替换实体',
    ])]
    public function testReplace(): void
    {
        $work = UnitOfWork::make();

        $post = new Post([
            'id' => 1,
            'title' => 'new',
            'user_id' => 0,
        ]);
        $post2 = new Post([
            'id' => 2,
            'title' => 'new2',
            'user_id' => 2,
        ]);

        self::assertFalse($work->replaced($post));
        self::assertFalse($work->replaced($post2));
        $work->replace($post);
        $work->replace($post2);
        self::assertTrue($work->replaced($post));
        self::assertTrue($work->replaced($post2));
        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                2,
                1
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->replaced($post));
        self::assertTrue($work->replaced($post2));

        $createPost = Post::select()->findEntity(1);
        $this->assertInstanceof(Post::class, $createPost);
        self::assertSame(1, $createPost->id);
        self::assertSame('new', $createPost->title);

        $createPost = Post::select()->findEntity(2);
        $this->assertInstanceof(Post::class, $createPost);
        self::assertSame(2, $createPost->id);
        self::assertSame('new2', $createPost->title);
    }

    #[Api([
        'zh-CN:title' => 'replace 注册替换实体到前置区域',
    ])]
    public function testReplaceBefore(): void
    {
        $work = UnitOfWork::make();

        $post = new Post([
            'id' => 1,
            'title' => 'new',
            'user_id' => 0,
        ]);
        $post2 = new Post([
            'id' => 2,
            'title' => 'new2',
            'user_id' => 2,
        ]);

        self::assertFalse($work->replaced($post));
        self::assertFalse($work->replaced($post2));
        $work->replace($post);
        $work->replaceBefore($post2);
        self::assertTrue($work->replaced($post));
        self::assertTrue($work->replaced($post2));
        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                1,
                2
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->replaced($post));
        self::assertTrue($work->replaced($post2));

        $createPost = Post::select()->findEntity(1);
        $this->assertInstanceof(Post::class, $createPost);
        self::assertSame(1, $createPost->id);
        self::assertSame('new', $createPost->title);

        $createPost = Post::select()->findEntity(2);
        $this->assertInstanceof(Post::class, $createPost);
        self::assertSame(2, $createPost->id);
        self::assertSame('new2', $createPost->title);
    }

    #[Api([
        'zh-CN:title' => 'replace 注册替换实体到后置区域',
    ])]
    public function testReplaceAfter(): void
    {
        $work = UnitOfWork::make();

        $post = new Post([
            'id' => 1,
            'title' => 'new',
            'user_id' => 0,
        ]);
        $post2 = new Post([
            'id' => 2,
            'title' => 'new2',
            'user_id' => 2,
        ]);

        self::assertFalse($work->replaced($post));
        self::assertFalse($work->replaced($post2));
        $work->replaceAfter($post);
        $work->replace($post2);
        self::assertTrue($work->replaced($post));
        self::assertTrue($work->replaced($post2));
        $work->on($post2, static function (): void {
            $GLOBALS['unitOfWork'][] = 1;
        });
        $work->on($post, static function (): void {
            $GLOBALS['unitOfWork'][] = 2;
        });

        $work->flush();

        $data = <<<'eot'
            [
                1,
                2
            ]
            eot;

        self::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['unitOfWork']
            )
        );

        self::assertTrue($work->replaced($post));
        self::assertTrue($work->replaced($post2));

        $createPost = Post::select()->findEntity(1);
        $this->assertInstanceof(Post::class, $createPost);
        self::assertSame(1, $createPost->id);
        self::assertSame('new', $createPost->title);

        $createPost = Post::select()->findEntity(2);
        $this->assertInstanceof(Post::class, $createPost);
        self::assertSame(2, $createPost->id);
        self::assertSame('new2', $createPost->title);
    }

    #[Api([
        'zh-CN:title' => 'replace 注册替换实体更新例子',
    ])]
    public function testReplaceAsUpdate(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        self::assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title' => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                    'delete_at' => 0,
                ])
        );

        $post = new Post([
            'id' => 1,
            'title' => 'new',
            'summary' => 'new',
            'user_id' => 1,
        ]);

        $work->replace($post);

        $work->flush();

        $updatedPost = Post::select()->findEntity(1);

        self::assertSame(1, $updatedPost->id);
        self::assertSame('new', $updatedPost->title);
        self::assertSame(1, $updatedPost->userId);
        self::assertSame('new', $updatedPost->summary);
    }

    #[Api([
        'zh-CN:title' => '已创建的实体不能够被替换',
    ])]
    public function testReplaceButAlreadyInCreates(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Created entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for replace.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->create($post);

        $work->replace($post);
    }

    #[Api([
        'zh-CN:title' => '已更新的实体不能够被替换',
    ])]
    public function testReplaceButAlreadyInUpdates(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Updated entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for replace.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->update($post);

        $work->replace($post);
    }

    #[Api([
        'zh-CN:title' => '同一个实体不能被替换多次',
    ])]
    public function testReplaceManyTimes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be replaced for twice.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $post = new Post(['title' => 'foo']);

        $work->replace($post);
        $work->replace($post);
    }

    #[Api([
        'zh-CN:title' => '已删除的实体不能够被替换',
    ])]
    public function testReplaceButAlreadyInDeletes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Deleted entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be added for replace.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['id' => 5, 'title' => 'new']);

        $work->delete($post);

        $work->replace($post);
    }

    public function testDeleteButHasNoPrimaryData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` has no unique key data for delete.'
        );

        $work = UnitOfWork::make();

        $post = new Post(['title' => 'new']);

        $work->delete($post);
    }

    #[Api([
        'zh-CN:title' => '同一个实体不能够被删除多次',
    ])]
    public function testDeleteManyTimes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` cannot be deleted for twice.'
        );

        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $post = new Post(['id' => 1, 'title' => 'foo']);

        $work->delete($post);
        $work->delete($post);
    }

    public function testEntityState(): void
    {
        $work = UnitOfWork::make();
        $connect = $this->createDatabaseConnect();
        $post = new Post(['id' => 1, 'title' => 'foo']);
        self::assertSame(UnitOfWork::STATE_NEW, $work->getEntityState($post));
        $work->persist($post);
        self::assertSame(UnitOfWork::STATE_MANAGED, $work->getEntityState($post));
        $work->flush();
        self::assertSame(UnitOfWork::STATE_DETACHED, $work->getEntityState($post));
    }

    #[Api([
        'zh-CN:title' => '不能多次创建同一个实体',
    ])]
    public function testPersistAsCompositeIdReplace2(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $compositeId = new CompositeId([
            'id1' => 1,
            'id2' => 2,
            'name' => 'old',
        ]);

        $work->persist($compositeId);

        $work->flush();

        self::assertSame(1, $connect->table('composite_id')->findCount());
    }

    #[Api([
        'zh-CN:title' => 'persist 保持实体为替换支持复合主键',
    ])]
    public function testPersistAsCompositeIdReplace(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $compositeId = new CompositeId([
            'id1' => 1,
            'id2' => 2,
            'name' => 'old',
        ]);

        $work->persist($compositeId, 'replace');

        $work->flush();

        self::assertSame(1, $connect->table('composite_id')->findCount());
    }

    #[Api([
        'zh-CN:title' => '获取事务执行结果',
    ])]
    public function testGetFlushResult(): void
    {
        $work = UnitOfWork::make();

        $connect = $this->createDatabaseConnect();

        $compositeId = new CompositeId([
            'id1' => 1,
            'id2' => 2,
            'name' => 'old',
        ]);

        $work->persist($compositeId);

        $work->flush();

        self::assertSame(1, $connect->table('composite_id')->findCount());
        self::assertSame(0, $work->getFlushResult($compositeId));
    }

    #[Api([
        'zh-CN:title' => '实体可以为虚拟闭包',
    ])]
    public function test1(): void
    {
        $work = UnitOfWork::make(new CompositeId());

        $connect = $this->createDatabaseConnect();

        $compositeId = new CompositeId([
            'id1' => 1,
            'id2' => 2,
            'name' => 'old',
        ]);
        $compositeCall = static function () use ($compositeId): mixed {
            return $compositeId->save()->flush();
        };

        $work->persist($compositeCall);

        $work->flush();

        self::assertSame(1, $connect->table('composite_id')->findCount());
        self::assertSame(0, $work->getFlushResult($compositeCall));
    }

    public function test3(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Default entity is not set.'
        );

        $work = UnitOfWork::make();

        $compositeId = new CompositeId([
            'id1' => 1,
            'id2' => 2,
            'name' => 'old',
        ]);
        $compositeCall = static function () use ($compositeId): mixed {
            return $compositeId->save()->flush();
        };

        $work->persist($compositeCall);

        $work->flush();
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'guest_book', 'composite_id'];
    }
}
