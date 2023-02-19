<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Relation;

use Leevel\Database\Ddd\EntityCollection as Collection;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\Role;
use Tests\Database\Ddd\Entity\Relation\User;
use Tests\Database\Ddd\Entity\Relation\UserRole;

/**
 * @api(
 *     zh-CN:title="nested 嵌套预加载关联",
 *     path="orm/nested",
 *     zh-CN:description="
 * 预加载关联可以减少查询，并且支持嵌套，通过 `.` 分隔嵌套关联。
 * ",
 * )
 */
final class NestedTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基本使用方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Database\Ddd\Entity\Relation\Post**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Post::class)]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\Relation\UserRole**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\UserRole::class)]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\Relation\Role**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\Relation\Role::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBase(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        static::assertCount(0, $posts);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; ++$i) {
            static::assertSame(
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

        static::assertSame(
            1,
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ])
        );

        static::assertSame(
            1,
            $connect
                ->table('role')
                ->insert([
                    'name' => '管理员',
                ])
        );

        static::assertSame(
            2,
            $connect
                ->table('role')
                ->insert([
                    'name' => '版主',
                ])
        );

        static::assertSame(
            3,
            $connect
                ->table('role')
                ->insert([
                    'name' => '会员',
                ])
        );

        static::assertSame(
            1,
            $connect
                ->table('user_role')
                ->insert([
                    'user_id' => 1,
                    'role_id' => 1,
                ])
        );

        static::assertSame(
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
        static::assertCount(5, $posts);

        $post = Post::select()->where('id', 1)->findOne();

        static::assertSame(1, $post->id);
        static::assertSame(1, $post['id']);
        static::assertSame(1, $post->getId());
        static::assertSame(1, $post->user_id);
        static::assertSame(1, $post->userId);
        static::assertSame(1, $post['user_id']);
        static::assertSame(1, $post->getUserId());
        static::assertSame('hello world', $post->title);
        static::assertSame('hello world', $post['title']);
        static::assertSame('hello world', $post->getTitle());
        static::assertSame('Say hello to the world.', $post->summary);
        static::assertSame('Say hello to the world.', $post['summary']);
        static::assertSame('Say hello to the world.', $post->getSummary());

        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        static::assertSame(1, $user->id);
        static::assertSame(1, $user['id']);
        static::assertSame(1, $user->getId());
        static::assertSame('niu', $user->name);
        static::assertSame('niu', $user['name']);
        static::assertSame('niu', $user->getName());

        $role = $user->role;

        $this->assertInstanceof(Collection::class, $role);

        $user1 = $role[0];

        $this->assertInstanceof(Role::class, $user1);
        static::assertSame(1, $user1->id);
        static::assertSame(1, $user1['id']);
        static::assertSame(1, $user1->getId());
        static::assertSame('管理员', $user1->name);
        static::assertSame('管理员', $user1['name']);
        static::assertSame('管理员', $user1->getName());

        $user2 = $role[1];
        $this->assertInstanceof(Role::class, $user2);
        static::assertSame(3, $user2->id);
        static::assertSame(3, $user2['id']);
        static::assertSame(3, $user2->getId());
        static::assertSame('会员', $user2->name);
        static::assertSame('会员', $user2['name']);
        static::assertSame('会员', $user2->getName());

        static::assertCount(2, $role);
        static::assertSame(1, $role[0]['id']);
        static::assertSame('管理员', $role[0]['name']);
        static::assertSame(3, $role[1]['id']);
        static::assertSame('会员', $role[1]['name']);

        $middle = $role[0]->middle();
        $this->assertInstanceof(UserRole::class, $middle);
        static::assertSame(1, $middle->userId);
        static::assertSame(1, $middle->roleId);

        $middle = $role[1]->middle();
        $this->assertInstanceof(UserRole::class, $middle);
        static::assertSame(1, $middle->userId);
        static::assertSame(3, $middle->roleId);
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'user_role', 'role', 'user'];
    }
}
