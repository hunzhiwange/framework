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

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Relation\BelongsTo;
use Leevel\Database\Ddd\Select;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\User;

/**
 * belongs test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.13
 *
 * @version 1.0
 */
class BelongsToTest extends TestCase
{
    public function testBaseUse(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'Say hello to the world.',
                ]),
        );

        $this->assertSame(
            '1',
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $post = Post::select()->where('id', 1)->findOne();

        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post['id']);
        $this->assertSame('1', $post->getterId());
        $this->assertSame('1', $post->user_id);
        $this->assertSame('1', $post->userId);
        $this->assertSame('1', $post['user_id']);
        $this->assertSame('1', $post->getterUserId());
        $this->assertSame('hello world', $post->title);
        $this->assertSame('hello world', $post['title']);
        $this->assertSame('hello world', $post->getterTitle());
        $this->assertSame('Say hello to the world.', $post->summary);
        $this->assertSame('Say hello to the world.', $post['summary']);
        $this->assertSame('Say hello to the world.', $post->getterSummary());

        $user = $post->user;

        $this->assertInstanceof(User::class, $user);
        $this->assertSame('1', $user->id);
        $this->assertSame('1', $user['id']);
        $this->assertSame('1', $user->getterId());
        $this->assertSame('niu', $user->name);
        $this->assertSame('niu', $user['name']);
        $this->assertSame('niu', $user->getterName());
    }

    public function testEager(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; $i++) {
            $this->assertSame(
                (string) ($i + 1),
                $connect
                    ->table('post')
                    ->insert([
                        'title'   => 'hello world',
                        'user_id' => 1,
                        'summary' => 'Say hello to the world.',
                    ]),
            );
        }

        $this->assertSame(
            '1',
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $posts = Post::eager(['user'])
            ->limit(5)
            ->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(5, $posts);

        foreach ($posts as $value) {
            $user = $value->user;

            $this->assertInstanceof(User::class, $user);
            $this->assertSame('1', $user->id);
            $this->assertSame('niu', $user->name);
        }
    }

    public function testRelationAsMethod(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'Say hello to the world.',
                ]),
        );

        $this->assertSame(
            '1',
            $connect
                ->table('user')
                ->insert([
                    'name' => 'niu',
                ]),
        );

        $userRelation = Post::make()->loadRelation('user');

        $this->assertInstanceof(BelongsTo::class, $userRelation);
        $this->assertSame('user_id', $userRelation->getSourceKey());
        $this->assertSame('id', $userRelation->getTargetKey());
        $this->assertInstanceof(Post::class, $userRelation->getSourceEntity());
        $this->assertInstanceof(User::class, $userRelation->getTargetEntity());
        $this->assertInstanceof(Select::class, $userRelation->getSelect());
    }

    public function testEagerButNotFoundSourceData(): void
    {
        $posts = Post::select()->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);

        $posts = Post::eager(['user'])->limit(5)->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(0, $posts);
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'user'];
    }
}
