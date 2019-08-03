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
use Leevel\Database\Ddd\Relation\HasMany;
use Leevel\Database\Ddd\Select;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Comment;
use Tests\Database\Ddd\Entity\Relation\Post;

/**
 * hasMany test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.14
 *
 * @version 1.0
 */
class HasManyTest extends TestCase
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

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 1,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

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

        $comment = $post->comment;

        $this->assertInstanceof(Collection::class, $comment);

        $n = 0;

        foreach ($comment as $k => $v) {
            $id = (int) ($n + 5);

            $this->assertSame($n, $k);
            $this->assertSame($id, (int) $v->id);
            $this->assertSame($id, (int) $v['id']);
            $this->assertSame($id, (int) $v->getterId());
            $this->assertSame('niu'.$id, $v['title']);
            $this->assertSame('niu'.$id, $v->title);
            $this->assertSame('niu'.$id, $v->getterTitle());
            $this->assertSame('Comment data.'.$id, $v['content']);
            $this->assertSame('Comment data.'.$id, $v->content);
            $this->assertSame('Comment data.'.$id, $v->getterContent());

            $n++;
        }

        $this->assertCount(6, $comment);
    }

    public function testEager(): void
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
            '2',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'foo bar',
                    'user_id' => 1,
                    'summary' => 'Say foo to the bar.',
                ]),
        );

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 1,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 2,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

        $posts = Post::eager(['comment'])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(2, $posts);

        $min = 5;

        foreach ($posts as $k => $value) {
            $comments = $value->comment;

            $this->assertInstanceof(Collection::class, $comments);
            $this->assertSame(0 === $k ? 6 : 10, count($comments));

            foreach ($comments as $comment) {
                $this->assertInstanceof(Comment::class, $comment);
                $this->assertSame((string) $min, $comment->id);

                $min++;
            }
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

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('comment')
                ->insert([
                    'title'   => 'niu'.($i + 1),
                    'post_id' => 1,
                    'content' => 'Comment data.'.($i + 1),
                ]);
        }

        $commentRelation = Post::make()->comment();

        $this->assertInstanceof(HasMany::class, $commentRelation);
        $this->assertSame('id', $commentRelation->getSourceKey());
        $this->assertSame('post_id', $commentRelation->getTargetKey());
        $this->assertInstanceof(Post::class, $commentRelation->getSourceEntity());
        $this->assertInstanceof(Comment::class, $commentRelation->getTargetEntity());
        $this->assertInstanceof(Select::class, $commentRelation->getSelect());
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'comment'];
    }
}
