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
use Leevel\Database\Ddd\Relation\HasOne;
use Leevel\Database\Ddd\Select;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\PostContent;
use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * hasOne test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.13
 *
 * @version 1.0
 */
class HasOneTest extends TestCase
{
    use Query;

    protected function setUp()
    {
        $this->truncate('post');
        $this->truncate('post_content');

        Meta::setDatabaseManager($this->createManager());
    }

    protected function tearDown()
    {
        $this->truncate('post');
        $this->truncate('post_content');

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

        $this->assertSame('0', $connect->
        table('post_content')->
        insert([
            'post_id' => 1,
            'content' => 'I am content with big data.',
        ]));

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

        $postContent = $post->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        $this->assertSame('1', $postContent->post_id);
        $this->assertSame('1', $postContent->postId);
        $this->assertSame('1', $postContent['post_id']);
        $this->assertSame('1', $postContent['postId']);
        $this->assertSame('1', $postContent->getPostId());
        $this->assertSame('I am content with big data.', $postContent->content);
        $this->assertSame('I am content with big data.', $postContent['content']);
        $this->assertSame('I am content with big data.', $postContent->getContent());

        $this->truncate('post');
        $this->truncate('post_content');
    }

    public function testEager()
    {
        $post = Post::where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createConnectTest();

        for ($i = 0; $i <= 5; $i++) {
            $this->assertSame((string) ($i + 1), $connect->
            table('post')->
            insert([
                'title'   => 'hello world',
                'user_id' => 1,
                'summary' => 'Say hello to the world.',
            ]));

            $this->assertSame('0', $connect->
            table('post_content')->
            insert([
                'post_id' => $i + 1,
                'content' => 'I am content with big data.',
            ]));
        }

        $posts = Post::eager(['post_content'])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertSame(6, count($posts));

        foreach ($posts as $value) {
            $postContent = $value->postContent;

            $this->assertInstanceof(PostContent::class, $postContent);
            $this->assertSame($value->id, $postContent->postId);
            $this->assertSame('I am content with big data.', $postContent->content);
        }

        $this->truncate('post');
        $this->truncate('post_content');
    }

    public function testRelationAsMethod()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'Say hello to the world.',
        ]));

        $this->assertSame('0', $connect->
        table('post_content')->
        insert([
            'post_id' => 1,
            'content' => 'I am content with big data.',
        ]));

        $postContentRelation = Post::postContent();

        $this->assertInstanceof(HasOne::class, $postContentRelation);
        $this->assertSame('id', $postContentRelation->getSourceKey());
        $this->assertSame('post_id', $postContentRelation->getTargetKey());
        $this->assertInstanceof(Post::class, $postContentRelation->getSourceEntity());
        $this->assertInstanceof(PostContent::class, $postContentRelation->getTargetEntity());
        $this->assertInstanceof(Select::class, $postContentRelation->getSelect());

        $this->truncate('post');
        $this->truncate('post_content');
    }
}
