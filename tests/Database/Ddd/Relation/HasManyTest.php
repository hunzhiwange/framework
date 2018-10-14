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

namespace Tests\Database\Ddd\Relation;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Meta;
use Tests\Database\Ddd\Entity\Relation\Comment;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Query\Query;
use Tests\TestCase;

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
    use Query;

    protected function setUp()
    {
        $this->truncate('post');
        $this->truncate('comment');

        Meta::setDatabaseManager($this->createManager());
    }

    protected function tearDown()
    {
        $this->truncate('post');
        $this->truncate('comment');

        Meta::setDatabaseManager(null);
    }

    public function testBaseUse()
    {
        $post = Post::where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'Say hello to the world.',
        ]));

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('comment')->
            insert([
                'title'   => 'niu'.($i + 1),
                'post_id' => 1,
                'content' => 'Comment data.'.($i + 1),
            ]);
        }

        $post = Post::where('id', 1)->findOne();

        $this->assertSame('1', $post->id);
        $this->assertSame('1', $post['id']);
        $this->assertSame('1', $post->getId());
        $this->assertSame('1', $post->user_id);
        $this->assertSame('1', $post->userId);
        $this->assertSame('1', $post['user_id']);
        $this->assertSame('1', $post->getUserId());
        $this->assertSame('hello world', $post->title);
        $this->assertSame('hello world', $post['title']);
        $this->assertSame('hello world', $post->getTitle());
        $this->assertSame('Say hello to the world.', $post->summary);
        $this->assertSame('Say hello to the world.', $post['summary']);
        $this->assertSame('Say hello to the world.', $post->getSummary());

        $comment = $post->comment;

        $this->assertInstanceof(Collection::class, $comment);

        $n = 0;

        foreach ($comment as $k => $v) {
            $id = (int) ($n + 5);

            $this->assertSame($n, $k);
            $this->assertSame($id, (int) $v->id);
            $this->assertSame($id, (int) $v['id']);
            $this->assertSame($id, (int) $v->getId());
            $this->assertSame('niu'.$id, $v['title']);
            $this->assertSame('niu'.$id, $v->title);
            $this->assertSame('niu'.$id, $v->getTitle());
            $this->assertSame('Comment data.'.$id, $v['content']);
            $this->assertSame('Comment data.'.$id, $v->content);
            $this->assertSame('Comment data.'.$id, $v->getContent());

            $n++;
        }

        $this->assertSame(6, count($comment));

        $this->truncate('post');
        $this->truncate('comment');
    }
}
