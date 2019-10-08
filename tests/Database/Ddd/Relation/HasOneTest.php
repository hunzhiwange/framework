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
use Leevel\Database\Ddd\Relation\HasOne;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Ddd\Select;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\Relation\PostContent;

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
    public function testBaseUse(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'Say hello to the world.',
                    'delete_at' => 0,
                ]));

        $this->assertSame(
            0,
            $connect
                ->table('post_content')
                ->insert([
                    'post_id' => 1,
                    'content' => 'I am content with big data.',
                ]));

        $post = Post::select()->where('id', 1)->findOne();

        $this->assertSame(1, $post->id);
        $this->assertSame(1, $post['id']);
        $this->assertSame(1, $post->getId());
        $this->assertSame(1, $post->user_id);
        $this->assertSame(1, $post->userId);
        $this->assertSame(1, $post['user_id']);
        $this->assertSame(1, $post->getUserId());
        $this->assertSame('hello world', $post->title);
        $this->assertSame('hello world', $post['title']);
        $this->assertSame('hello world', $post->getTitle());
        $this->assertSame('Say hello to the world.', $post->summary);
        $this->assertSame('Say hello to the world.', $post['summary']);
        $this->assertSame('Say hello to the world.', $post->getSummary());

        $postContent = $post->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        $this->assertSame(1, $postContent->post_id);
        $this->assertSame(1, $postContent->postId);
        $this->assertSame(1, $postContent['post_id']);
        $this->assertSame(1, $postContent['postId']);
        $this->assertSame(1, $postContent->getPostId());
        $this->assertSame('I am content with big data.', $postContent->content);
        $this->assertSame('I am content with big data.', $postContent['content']);
        $this->assertSame('I am content with big data.', $postContent->getContent());
    }

    public function testEager(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; $i++) {
            $this->assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title'     => 'hello world',
                        'user_id'   => 1,
                        'summary'   => 'Say hello to the world.',
                        'delete_at' => 0,
                    ]));

            $this->assertSame(
                0,
                $connect
                    ->table('post_content')
                    ->insert([
                        'post_id' => $i + 1,
                        'content' => 'I am content with big data.',
                    ]));
        }

        $posts = Post::eager(['post_content'])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(6, $posts);

        foreach ($posts as $value) {
            $postContent = $value->postContent;

            $this->assertInstanceof(PostContent::class, $postContent);
            $this->assertSame($value->id, $postContent->postId);
            $this->assertSame('I am content with big data.', $postContent->content);
        }
    }

    public function testRelationAsMethod(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'Say hello to the world.',
                    'delete_at' => 0,
                ]));

        $this->assertSame(
            0,
            $connect
                ->table('post_content')
                ->insert([
                    'post_id' => 1,
                    'content' => 'I am content with big data.',
                ]));

        $postContentRelation = Post::make()->loadRelation('postContent');

        $this->assertInstanceof(HasOne::class, $postContentRelation);
        $this->assertSame('id', $postContentRelation->getSourceKey());
        $this->assertSame('post_id', $postContentRelation->getTargetKey());
        $this->assertInstanceof(Post::class, $postContentRelation->getSourceEntity());
        $this->assertInstanceof(PostContent::class, $postContentRelation->getTargetEntity());
        $this->assertInstanceof(Select::class, $postContentRelation->getSelect());
    }

    public function testRelationWasNotFound(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'Say hello to the world.',
                    'delete_at' => 0,
                ]));

        $this->assertSame(
            0,
            $connect
                ->table('post_content')
                ->insert([
                    'post_id' => 5,
                    'content' => 'I am content with big data.',
                ]));

        $post = Post::select()->where('id', 1)->findOne();

        $this->assertSame(1, $post->id);
        $this->assertSame(1, $post['id']);
        $this->assertSame(1, $post->getId());
        $this->assertSame(1, $post->user_id);
        $this->assertSame(1, $post->userId);
        $this->assertSame(1, $post['user_id']);
        $this->assertSame(1, $post->getUserId());
        $this->assertSame('hello world', $post->title);
        $this->assertSame('hello world', $post['title']);
        $this->assertSame('hello world', $post->getTitle());
        $this->assertSame('Say hello to the world.', $post->summary);
        $this->assertSame('Say hello to the world.', $post['summary']);
        $this->assertSame('Say hello to the world.', $post->getSummary());

        $postContent = $post->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        $this->assertNull($postContent->post_id);
        $this->assertNull($postContent->postId);
        $this->assertNull($postContent['post_id']);
        $this->assertNull($postContent['postId']);
        $this->assertNull($postContent->getPostId());
        $this->assertNull($postContent->content);
        $this->assertNull($postContent['content']);
        $this->assertNull($postContent->getContent());
    }

    public function testEagerRelationWasNotFound(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; $i++) {
            $this->assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title'     => 'hello world',
                        'user_id'   => 1,
                        'summary'   => 'Say hello to the world.',
                        'delete_at' => 0,
                    ]));

            $this->assertSame(
                0,
                $connect
                    ->table('post_content')
                    ->insert([
                        'post_id' => 9999 + $i,
                        'content' => 'I am content with big data.',
                    ]));
        }

        $posts = Post::eager(['post_content'])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(6, $posts);

        foreach ($posts as $value) {
            $postContent = $value->postContent;

            $this->assertInstanceof(PostContent::class, $postContent);
            $this->assertNull($postContent->postId);
            $this->assertNull($postContent->content);
        }
    }

    public function testSourceDataIsEmtpy(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);
        $postContent = $post->postContent;

        $this->assertInstanceof(PostContent::class, $postContent);
        $this->assertNull($postContent->post_id);
        $this->assertNull($postContent->postId);
        $this->assertNull($postContent['post_id']);
        $this->assertNull($postContent['postId']);
        $this->assertNull($postContent->getPostId());
        $this->assertNull($postContent->content);
        $this->assertNull($postContent['content']);
        $this->assertNull($postContent->getContent());
    }

    public function testEagerWithCondition(): void
    {
        $post = Post::select()->where('id', 1)->findOne();

        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i <= 5; $i++) {
            $this->assertSame(
                $i + 1,
                $connect
                    ->table('post')
                    ->insert([
                        'title'     => 'hello world',
                        'user_id'   => 1,
                        'summary'   => 'Say hello to the world.',
                        'delete_at' => 0,
                    ]));

            $this->assertSame(
                0,
                $connect
                    ->table('post_content')
                    ->insert([
                        'post_id' => $i + 1,
                        'content' => 'I am content with big data.',
                    ]));
        }

        $posts = Post::eager(['post_content' => function (Relation $select) {
            $select->where('post_id', '>', 99999);
        }])->findAll();

        $this->assertInstanceof(Collection::class, $posts);
        $this->assertCount(6, $posts);

        foreach ($posts as $value) {
            $postContent = $value->postContent;
            $this->assertInstanceof(PostContent::class, $postContent);
            $this->assertNotSame($value->id, $postContent->postId);
            $this->assertNotSame('I am content with big data.', $postContent->content);
            $this->assertNull($postContent->postId);
            $this->assertNull($postContent->content);
        }
    }

    public function testValidateRelationKeyNotDefinedSourceKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `source_key` field was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $post->postContentNotDefinedSourceKey;
    }

    public function testValidateRelationKeyNotDefinedTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Relation `target_key` field was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $post->postContentNotDefinedTargetKey;
    }

    public function testValidateRelationFieldSourceKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `post`.`not_found_source_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $post->hasOne(PostContent::class, 'post_id', 'not_found_source_key');
    }

    public function testValidateRelationFieldTargetKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The field `post_content`.`not_found_target_key` of entity `Tests\\Database\\Ddd\\Entity\\Relation\\PostContent` was not defined.'
        );

        $post = Post::select()->where('id', 1)->findOne();
        $this->assertInstanceof(Post::class, $post);
        $this->assertNull($post->id);

        $post->hasOne(PostContent::class, 'not_found_target_key', 'id');
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'post_content'];
    }
}
