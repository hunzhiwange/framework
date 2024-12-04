<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Relation;

use Leevel\Database\Ddd\EntityCollection as Collection;
use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\Role;
use Tests\Database\Ddd\Entity\Relation\User;
use Tests\Database\Ddd\Entity\Relation\UserRole;

#[Api([
    'zh-CN:title' => 'nested 嵌套预加载关联',
    'path' => 'orm/nested',
    'zh-CN:description' => <<<'EOT'
预加载关联可以减少查询，并且支持嵌套，通过 `.` 分隔嵌套关联。
EOT,
])]
final class NestedTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用方法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Database\Ddd\Entity\Relation\Post**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Post::class)]}
```

**Tests\Database\Ddd\Entity\Relation\UserRole**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\UserRole::class)]}
```

**Tests\Database\Ddd\Entity\Relation\Role**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Role::class)]}
```
EOT,
    ])]
    public function testBase(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(0, $posts);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; ++$i) {
            self::assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title' => 'hello world',
                        'user_id' => 1,
                        'summary' => 'Say hello to the world.',
                        'delete_at' => 0,
                    ])
            );
        }

        self::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ])
        );

        self::assertSame(
            1,
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ])
        );

        self::assertSame(
            3,
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ])
        );

        self::assertSame(
            1,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ])
        );

        self::assertSame(
            2,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 3,
                ])
        );

        $posts = Post::eager(['user.role'])
            ->limit(5)
            ->findAll()
        ;

        $this->assertInstanceof(Collection::class, $posts);
        self::assertCount(5, $posts);

        $post = Post::select()->where('id', 1)->findOne();

        self::assertSame(1, $post->id);
        self::assertSame(1, $post['id']);
        self::assertSame(1, $post->getId());
        self::assertSame(1, $post->userId);
        self::assertSame(1, $post['user_id']);
        self::assertSame(1, $post->getUserId());
        self::assertSame('hello world', $post->title);
        self::assertSame('hello world', $post['title']);
        self::assertSame('hello world', $post->getTitle());
        self::assertSame('Say hello to the world.', $post->summary);
        self::assertSame('Say hello to the world.', $post['summary']);
        self::assertSame('Say hello to the world.', $post->getSummary());

        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        self::assertSame(1, $user->id);
        self::assertSame(1, $user['id']);
        self::assertSame(1, $user->getId());
        self::assertSame('niu', $user->name);
        self::assertSame('niu', $user['name']);
        self::assertSame('niu', $user->getName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);

        $user1 = $role[0];

        $this->assertInstanceof(Role::class, $user1);
        self::assertSame(1, $user1->id);
        self::assertSame(1, $user1['id']);
        self::assertSame(1, $user1->getId());
        self::assertSame('管理员', $user1->name);
        self::assertSame('管理员', $user1['name']);
        self::assertSame('管理员', $user1->getName());

        $user2 = $role[1];
        $this->assertInstanceof(Role::class, $user2);
        self::assertSame(3, $user2->id);
        self::assertSame(3, $user2['id']);
        self::assertSame(3, $user2->getId());
        self::assertSame('会员', $user2->name);
        self::assertSame('会员', $user2['name']);
        self::assertSame('会员', $user2->getName());

        self::assertCount(2, $role);
        self::assertSame(1, $role[0]['id']);
        self::assertSame('管理员', $role[0]['name']);
        self::assertSame(3, $role[1]['id']);
        self::assertSame('会员', $role[1]['name']);

        $middle = $role[0]->middle();
        $this->assertInstanceof(UserRole::class, $middle);
        self::assertSame(1, $middle->userId);
        self::assertSame(1, $middle->roleId);

        $middle = $role[1]->middle();
        $this->assertInstanceof(UserRole::class, $middle);
        self::assertSame(1, $middle->userId);
        self::assertSame(3, $middle->roleId);
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'user_role', 'role', 'user'];
    }
}
