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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Ddd;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\ISpecification;
use Leevel\Database\Ddd\Repository;
use Leevel\Database\Ddd\Select;
use Leevel\Database\Ddd\Specification;
use Leevel\Database\Page;
use Leevel\Page\Page as BasePage;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\DemoUnique;
use Tests\Database\Ddd\Entity\Relation\Post;

class RepositoryTest extends TestCase
{
    public function testBase(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $repository = new Repository(new Post());

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame(1, $newPost->id);
        $this->assertSame(1, $newPost->userId);
        $this->assertSame('hello world', $newPost->title);
        $this->assertSame('post summary', $newPost->summary);
        $this->assertInstanceof(Post::class, $repository->entity());
    }

    public function testFind(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $repository = new Repository(new Post());

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame(1, $newPost->id);
        $this->assertSame(1, $newPost->userId);
        $this->assertSame('hello world', $newPost->title);
        $this->assertSame('post summary', $newPost->summary);
    }

    public function testFindOrFail(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $repository = new Repository(new Post());

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame(1, $newPost->id);
        $this->assertSame(1, $newPost->userId);
        $this->assertSame('hello world', $newPost->title);
        $this->assertSame('post summary', $newPost->summary);
    }

    public function testFindOrFailNotFound(): void
    {
        $this->expectException(\Leevel\Database\Ddd\EntityNotFoundException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not found.'
        );

        $repository = new Repository(new Post());

        $newPost = $repository->findOrFail(1);
    }

    public function testSpecWithClosure(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $andSpec = $spec->and(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $andSpec);
        $this->assertInstanceof(Specification::class, $andSpec);

        $select = $repository->condition($andSpec);
        $result = $select->findAll();

        $sql = <<<'eot'
            SQL: [126] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id AND `post`.`id` < :post_id_1 | Params:  3 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=2 | name=[10] ":post_id_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 3 AND `post`.`id` < 8)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(4, $result);
    }

    public function testSpecWithClass(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());
        $spec = Specification::from(new Demo1Specification($request));
        $spec->and(new Demo2Specification($request));

        $this->assertInstanceof(ISpecification::class, $spec);
        $this->assertInstanceof(Specification::class, $spec);

        $select = $repository->condition($spec);
        $result = $select->findAll();

        $sql = <<<'eot'
            SQL: [126] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id AND `post`.`id` < :post_id_1 | Params:  3 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=2 | name=[10] ":post_id_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 3 AND `post`.`id` < 8)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(4, $result);
    }

    public function testNonStandardSpec(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Non standard specification,please use \\Leevel\\Database\\Ddd\\Specification::from(\\Leevel\\Database\\Ddd\\ISpecification $specification) to convert it.'
        );

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());
        $spec = new Demo1Specification($request);
        $spec->and(new Demo2Specification($request));
    }

    public function testExpr(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 8);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $select = $repository->condition($specExpr);
        $result = $select->findAll();

        $sql = <<<'eot'
            SQL: [126] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id AND `post`.`id` < :post_id_1 | Params:  3 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=2 | name=[10] ":post_id_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 3 AND `post`.`id` < 8)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(4, $result);
    }

    public function testFindAllBySpecWithClosure(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $andSpec = $spec->and(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $andSpec);
        $this->assertInstanceof(Specification::class, $andSpec);

        $result = $repository->findAll($andSpec);

        $sql = <<<'eot'
            SQL: [126] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id AND `post`.`id` < :post_id_1 | Params:  3 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=2 | name=[10] ":post_id_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 3 AND `post`.`id` < 8)
            eot;
        $this->assertSame(
            $sql,
            $repository->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(4, $result);
    }

    public function testFindAllBySpecWithClass(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = Specification::from(new Demo1Specification($request));
        $spec->and(new Demo2Specification($request));

        $this->assertInstanceof(ISpecification::class, $spec);
        $this->assertInstanceof(Specification::class, $spec);

        $result = $repository->findAll($spec);

        $sql = <<<'eot'
            SQL: [126] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id AND `post`.`id` < :post_id_1 | Params:  3 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=2 | name=[10] ":post_id_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 3 AND `post`.`id` < 8)
            eot;
        $this->assertSame(
            $sql,
            $repository->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(4, $result);
    }

    public function testFindAllByExpr(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 8);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findAll($specExpr);

        $sql = <<<'eot'
            SQL: [126] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id AND `post`.`id` < :post_id_1 | Params:  3 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=2 | name=[10] ":post_id_1" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 3 AND `post`.`id` < 8)
            eot;
        $this->assertSame(
            $sql,
            $repository->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(4, $result);
    }

    public function testFindCountBySpecWithClosure(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $andSpec = $spec->and(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $andSpec);
        $this->assertInstanceof(Specification::class, $andSpec);

        $result = $repository->findCount($andSpec);

        $sql = <<<'eot'
            SQL: [147] SELECT COUNT(*) AS row_count FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id AND `post`.`id` < :post_id_1 LIMIT 1 | Params:  3 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=2 | name=[10] ":post_id_1" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 3 AND `post`.`id` < 8 LIMIT 1)
            eot;
        $this->assertSame(
            $sql,
            $repository->getLastSql(),
        );

        $this->assertSame(4, $result);
    }

    public function testFindCountBySpecWithClass(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = Specification::from(new Demo1Specification($request));
        $spec->and(new Demo2Specification($request));

        $this->assertInstanceof(ISpecification::class, $spec);
        $this->assertInstanceof(Specification::class, $spec);

        $result = $repository->findCount($spec);

        $sql = <<<'eot'
            SQL: [147] SELECT COUNT(*) AS row_count FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id AND `post`.`id` < :post_id_1 LIMIT 1 | Params:  3 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=2 | name=[10] ":post_id_1" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 3 AND `post`.`id` < 8 LIMIT 1)
            eot;
        $this->assertSame(
            $sql,
            $repository->getLastSql(),
        );

        $this->assertSame(4, $result);
    }

    public function testFindCountByExpr(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 8);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $sql = <<<'eot'
            SQL: [147] SELECT COUNT(*) AS row_count FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id AND `post`.`id` < :post_id_1 LIMIT 1 | Params:  3 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 | Key: Name: [10] :post_id_1 | paramno=2 | name=[10] ":post_id_1" | is_param=1 | param_type=1 (SELECT COUNT(*) AS row_count FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 3 AND `post`.`id` < 8 LIMIT 1)
            eot;
        $this->assertSame(
            $sql,
            $repository->getLastSql(),
        );

        $this->assertSame(4, $result);
    }

    public function testFindAllBySpecWithClosureForNot(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $notSpec = $spec->not();

        $this->assertInstanceof(ISpecification::class, $notSpec);
        $this->assertInstanceof(Specification::class, $notSpec);

        $result = $repository->findAll($notSpec);

        $sql = <<<'eot'
            SQL: [70] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at | Params:  1 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0)
            eot;
        $this->assertSame(
            $sql,
            $repository->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(10, $result);
    }

    public function testFindAllBySpecWithClosureForNotButValueIsTrue(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'not_bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $notSpec = $spec->not();

        $this->assertInstanceof(ISpecification::class, $notSpec);
        $this->assertInstanceof(Specification::class, $notSpec);

        $result = $repository->findAll($notSpec);

        $sql = <<<'eot'
            SQL: [97] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id | Params:  2 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 3)
            eot;
        $this->assertSame(
            $sql,
            $repository->getLastSql(),
        );

        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(7, $result);
    }

    public function testSpecWithOrFirstIsNo(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (Entity $entity) use ($request) {
            return 'bar_no' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $orSpec = $spec->or(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $orSpec);
        $this->assertInstanceof(Specification::class, $orSpec);

        $select = $repository->condition($orSpec);
        $result = $select->findAll();

        $sql = <<<'eot'
            SQL: [97] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` < :post_id | Params:  2 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` < 8)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(7, $result);
    }

    public function testSpecWithOrFirstIsYes(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 4);
        });

        $orSpec = $spec->or(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $orSpec);
        $this->assertInstanceof(Specification::class, $orSpec);

        $select = $repository->condition($orSpec);
        $result = $select->findAll();

        $sql = <<<'eot'
            SQL: [97] SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = :post_delete_at AND `post`.`id` > :post_id | Params:  2 | Key: Name: [15] :post_delete_at | paramno=0 | name=[15] ":post_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (SELECT `post`.* FROM `post` WHERE `post`.`delete_at` = 0 AND `post`.`id` > 4)
            eot;
        $this->assertSame(
            $sql,
            $select->getLastSql(),
        );

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(6, $result);
    }

    public function testSpecWithOrFirstIsNoSecondAlsoIsNo(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $orSpec = $spec->or(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $orSpec);
        $this->assertInstanceof(Specification::class, $orSpec);

        $select = $repository->condition($orSpec);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(10, $result);
    }

    public function testSpecMake(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = Specification::make(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 4);
        });

        $select = $repository->condition($spec);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(6, $result);
    }

    public function testSpecificationMake(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = Specification::make(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $result = $repository->findCount($specExpr);

        $this->assertSame(7, $result);
    }

    public function testFindCountWithOrClosureFirstIsYes(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->or(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 6);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(7, $result);
    }

    public function testFindCountWithOrClosureFirstIsNo(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->or(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 6);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(5, $result);
    }

    public function testFindCountWithOrClosureFirstIsNoAndSecondAlsoIsNo(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->or(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 6);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(10, $result);
    }

    public function testFindCountWithOrFirstIsYes(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->or(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(7, $result);
    }

    public function testFindCountWithOrFirstIsNo(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->or(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(5, $result);
    }

    public function testFindCountWithOrFirstIsNoAndSecodeAlsoIsNo(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->or(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(10, $result);
    }

    public function testFindCountWithAndFirstIsYes(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(2, $result);
    }

    public function testFindCountWithAndFirstIsNo(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(10, $result);
    }

    public function testFindCountWithAndFirstIsYesSecodeIsNo(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(10, $result);
    }

    public function testFindCountWithAndFirstIsNoSecodeIsNo(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(new Specification(function (Entity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(10, $result);
    }

    public function testFindCountWithAndIsYes(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->not();

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(10, $result);
    }

    public function testFindCountWithAndIsNo(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new Specification(function (Entity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, Entity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->not();

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(Specification::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(7, $result);
    }

    public function testCall(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $repository = new Repository(new Post());

        $newPost = $repository
            ->where('id', 5)
            ->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertNull($newPost->id);
        $this->assertNull($newPost->userId);
        $this->assertNull($newPost->title);
        $this->assertNull($newPost->summary);
    }

    public function testCreateTwice(): void
    {
        $repository = new Repository(new Post());

        $repository->create($post = new Post([
            'id'      => 5,
            'title'   => 'foo',
            'user_id' => 0,
        ]));

        $this->assertSame('SQL: [147] INSERT INTO `post` (`post`.`id`,`post`.`title`,`post`.`user_id`) VALUES (:pdonamedparameter_id,:pdonamedparameter_title,:pdonamedparameter_user_id) | Params:  3 | Key: Name: [21] :pdonamedparameter_id | paramno=0 | name=[21] ":pdonamedparameter_id" | is_param=1 | param_type=1 | Key: Name: [24] :pdonamedparameter_title | paramno=1 | name=[24] ":pdonamedparameter_title" | is_param=1 | param_type=2 | Key: Name: [26] :pdonamedparameter_user_id | paramno=2 | name=[26] ":pdonamedparameter_user_id" | is_param=1 | param_type=1 (INSERT INTO `post` (`post`.`id`,`post`.`title`,`post`.`user_id`) VALUES (5,\'foo\',0))', $repository->getLastSql());

        $this->assertSame(5, $post->id);
        $this->assertSame('foo', $post->title);
        $this->assertSame(0, $post->userId);
        $this->assertSame([], $post->changed());
        $repository->create($post);
        $this->assertSame('SQL: [31] INSERT INTO `post` () VALUES () | Params:  0 (INSERT INTO `post` () VALUES ())', $repository->getLastSql());

        $newPost = $repository->findEntity(5);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame(5, $newPost->id);
        $this->assertSame('foo', $newPost->title);

        $newPost = $repository->findEntity(6);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame(6, $newPost->id);
        $this->assertSame('', $newPost->title);
    }

    public function testUpdateTwice(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` has no data need to be update.'
        );

        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $repository = new Repository(new Post());
        $repository->update($post = new Post(['id' => 1, 'title' => 'new title']));

        $this->assertSame('SQL: [88] UPDATE `post` SET `post`.`title` = :pdonamedparameter_title WHERE `post`.`id` = :post_id | Params:  2 | Key: Name: [24] :pdonamedparameter_title | paramno=0 | name=[24] ":pdonamedparameter_title" | is_param=1 | param_type=2 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (UPDATE `post` SET `post`.`title` = \'new title\' WHERE `post`.`id` = 1)', $repository->getLastSql());

        $this->assertSame([], $post->changed());
        $repository->update($post);
    }

    public function testReplaceTwiceAndFindExistData(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $repository = new Repository(new Post());
        $affectedRow = $repository->replace($post = new Post([
            'id'      => 1,
            'title'   => 'new title',
            'user_id' => 1,
        ]));
        $this->assertSame('SQL: [134] UPDATE `post` SET `post`.`title` = :pdonamedparameter_title,`post`.`user_id` = :pdonamedparameter_user_id WHERE `post`.`id` = :post_id | Params:  3 | Key: Name: [24] :pdonamedparameter_title | paramno=0 | name=[24] ":pdonamedparameter_title" | is_param=1 | param_type=2 | Key: Name: [26] :pdonamedparameter_user_id | paramno=1 | name=[26] ":pdonamedparameter_user_id" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=2 | name=[8] ":post_id" | is_param=1 | param_type=1 (UPDATE `post` SET `post`.`title` = \'new title\',`post`.`user_id` = 1 WHERE `post`.`id` = 1)', $repository->getLastSql());

        $this->assertSame(1, $affectedRow);
        $this->assertSame([], $post->changed());

        $repository->replace($post); // 新增一条数据.
        $this->assertSame('SQL: [31] INSERT INTO `post` () VALUES () | Params:  0 (INSERT INTO `post` () VALUES ())', $repository->getLastSql());

        $updatedPost = $repository->findEntity(1);
        $this->assertSame(1, $updatedPost->id);
        $this->assertSame('new title', $updatedPost->title);
        $this->assertSame(1, $updatedPost->userId);
        $this->assertSame('post summary', $updatedPost->summary);

        $newPost2 = $repository->findEntity(2);

        $this->assertInstanceof(Post::class, $newPost2);
        $this->assertSame(2, $newPost2->id);
        $this->assertSame('', $newPost2->title);
        $this->assertSame('', $newPost2->summary);
    }

    public function testReplaceTwiceAndNotFindExistData(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $repository = new Repository(new Post());
        $repository->replace($post = new Post([
            'id'      => 2,
            'title'   => 'new title',
            'user_id' => 0,
        ]));
        $this->assertSame('SQL: [147] INSERT INTO `post` (`post`.`id`,`post`.`title`,`post`.`user_id`) VALUES (:pdonamedparameter_id,:pdonamedparameter_title,:pdonamedparameter_user_id) | Params:  3 | Key: Name: [21] :pdonamedparameter_id | paramno=0 | name=[21] ":pdonamedparameter_id" | is_param=1 | param_type=1 | Key: Name: [24] :pdonamedparameter_title | paramno=1 | name=[24] ":pdonamedparameter_title" | is_param=1 | param_type=2 | Key: Name: [26] :pdonamedparameter_user_id | paramno=2 | name=[26] ":pdonamedparameter_user_id" | is_param=1 | param_type=1 (INSERT INTO `post` (`post`.`id`,`post`.`title`,`post`.`user_id`) VALUES (2,\'new title\',0))', $repository->getLastSql());

        $this->assertSame([], $post->changed());
        $repository->replace($post); // 新增一条数据.
        $this->assertSame('SQL: [31] INSERT INTO `post` () VALUES () | Params:  0 (INSERT INTO `post` () VALUES ())', $repository->getLastSql());

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame(1, $newPost->id);
        $this->assertSame('hello world', $newPost->title);
        $this->assertSame('post summary', $newPost->summary);

        $newPost2 = $repository->findEntity(2);

        $this->assertInstanceof(Post::class, $newPost2);
        $this->assertSame(2, $newPost2->id);
        $this->assertSame('new title', $newPost2->title);
        $this->assertSame('', $newPost2->summary);

        $newPost3 = $repository->findEntity(3);

        $this->assertInstanceof(Post::class, $newPost3);
        $this->assertSame(3, $newPost3->id);
        $this->assertSame('', $newPost3->title);
        $this->assertSame('', $newPost3->summary);
    }

    public function testReplaceUnique(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('test_unique')
                ->insert([
                    'name'     => 'hello world',
                    'identity' => 'hello',
                ])
        );

        $testUniqueData = DemoUnique::select()->findEntity(1);

        $this->assertInstanceof(DemoUnique::class, $testUniqueData);
        $this->assertSame(1, $testUniqueData->id);
        $this->assertSame('hello world', $testUniqueData->name);
        $this->assertSame('hello', $testUniqueData->identity);

        $testUnique = new DemoUnique(['id' => 1, 'name' => 'hello new', 'identity' => 'hello']);

        $repository = new Repository($testUnique);
        $repository->replace($testUnique);

        $testUniqueData = DemoUnique::select()->findEntity(1);

        $this->assertInstanceof(DemoUnique::class, $testUniqueData);
        $this->assertSame(1, $testUniqueData->id);
        $this->assertSame('hello new', $testUniqueData->name);
        $this->assertSame('hello', $testUniqueData->identity);
    }

    public function testSoftDeleteTwice(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $repository = new Repository(new Post());

        $repository->delete($post = new Post(['id' => 1, 'title' => 'new title']));
        $this->assertSame('SQL: [96] UPDATE `post` SET `post`.`delete_at` = :pdonamedparameter_delete_at WHERE `post`.`id` = :post_id | Params:  2 | Key: Name: [28] :pdonamedparameter_delete_at | paramno=0 | name=[28] ":pdonamedparameter_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (UPDATE `post` SET `post`.`delete_at` = '.time().' WHERE `post`.`id` = 1)', $repository->getLastSql());

        $repository->delete($post); // 将会更新 `delete_at` 字段.
        $this->assertSame('SQL: [96] UPDATE `post` SET `post`.`delete_at` = :pdonamedparameter_delete_at WHERE `post`.`id` = :post_id | Params:  2 | Key: Name: [28] :pdonamedparameter_delete_at | paramno=0 | name=[28] ":pdonamedparameter_delete_at" | is_param=1 | param_type=1 | Key: Name: [8] :post_id | paramno=1 | name=[8] ":post_id" | is_param=1 | param_type=1 (UPDATE `post` SET `post`.`delete_at` = '.time().' WHERE `post`.`id` = 1)', $repository->getLastSql());

        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertNull($newPost->id);
        $this->assertNull($newPost->userId);
        $this->assertNull($newPost->title);
        $this->assertNull($newPost->summary);
    }

    public function testForceDeleteTwice(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            1,
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]));

        $repository = new Repository(new Post());

        $repository->forceDelete($post = new Post(['id' => 1, 'title' => 'new title']));
        $this->assertSame('SQL: [47] DELETE FROM `post` WHERE `post`.`id` = :post_id | Params:  1 | Key: Name: [8] :post_id | paramno=0 | name=[8] ":post_id" | is_param=1 | param_type=1 (DELETE FROM `post` WHERE `post`.`id` = 1)', $repository->getLastSql());
        $repository->forceDelete($post); // 会执行 SQL，因为已经删除，没有任何影响.
        $this->assertSame('SQL: [47] DELETE FROM `post` WHERE `post`.`id` = :post_id | Params:  1 | Key: Name: [8] :post_id | paramno=0 | name=[8] ":post_id" | is_param=1 | param_type=1 (DELETE FROM `post` WHERE `post`.`id` = 1)', $repository->getLastSql());
        $newPost = $repository->findEntity(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertNull($newPost->id);
        $this->assertNull($newPost->userId);
        $this->assertNull($newPost->title);
        $this->assertNull($newPost->summary);
    }

    public function testConditionIsClosure(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, Entity $entity) use ($request) {
            $select->where('id', '<', 8);
        };

        $select = $repository->condition($condition);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(7, $result);
    }

    public function testConditionTypeIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid condition type.'
        );

        $repository = new Repository(new Post());
        $repository->condition(5);
    }

    public function testFindPage(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $repository = new Repository(new Post());

        $page = $repository->findPage(1, 10);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(10, $result);
    }

    public function testFindPageWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, Entity $entity) use ($request) {
            $select->where('id', '<', 8);
        };

        $page = $repository->findPage(1, 10, $condition);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(7, $result);
    }

    public function testFindPageMacro(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $repository = new Repository(new Post());

        $page = $repository->findPageMacro(1, 10);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(10, $result);
    }

    public function testFindPageMacroWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, Entity $entity) use ($request) {
            $select->where('id', '<', 8);
        };

        $page = $repository->findPageMacro(1, 10, $condition);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(7, $result);
    }

    public function testFindPagePrevNext(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $repository = new Repository(new Post());

        $page = $repository->findPagePrevNext(1, 10);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(10, $result);
    }

    public function testFindPagePrevNextWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world',
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, Entity $entity) use ($request) {
            $select->where('id', '<', 8);
        };

        $page = $repository->findPagePrevNext(1, 10, $condition);
        $result = $page->getData();

        $this->assertInstanceof(BasePage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(7, $result);
    }

    public function testFindList(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world'.$i,
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $repository = new Repository(new Post());

        $result = $repository->findList(null, 'summary', 'title');

        $this->assertIsArray($result);

        $data = <<<'eot'
            {
                "hello world0": "post summary",
                "hello world1": "post summary",
                "hello world2": "post summary",
                "hello world3": "post summary",
                "hello world4": "post summary",
                "hello world5": "post summary",
                "hello world6": "post summary",
                "hello world7": "post summary",
                "hello world8": "post summary",
                "hello world9": "post summary"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    public function testFindListFieldValueIsArray(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world'.$i,
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $repository = new Repository(new Post());

        $result = $repository->findList(null, ['summary', 'title']);

        $this->assertIsArray($result);

        $data = <<<'eot'
            {
                "hello world0": "post summary",
                "hello world1": "post summary",
                "hello world2": "post summary",
                "hello world3": "post summary",
                "hello world4": "post summary",
                "hello world5": "post summary",
                "hello world6": "post summary",
                "hello world7": "post summary",
                "hello world8": "post summary",
                "hello world9": "post summary"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    public function testFindListWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'     => 'hello world'.$i,
                    'user_id'   => 1,
                    'summary'   => 'post summary',
                    'delete_at' => 0,
                ]);
        }

        $repository = new Repository(new Post());

        $result = $repository->findList(function (Select $select) {
            $select->where('id', '>', 5);
        }, ['summary', 'title']);

        $this->assertIsArray($result);

        $data = <<<'eot'
            {
                "hello world5": "post summary",
                "hello world6": "post summary",
                "hello world7": "post summary",
                "hello world8": "post summary",
                "hello world9": "post summary"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $result
            )
        );
    }

    protected function getDatabaseTable(): array
    {
        return ['post', 'test_unique'];
    }
}

class Demo1Specification extends Specification
{
    private $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function isSatisfiedBy(Entity $entity): bool
    {
        return 'bar' === $this->request['foo'];
    }

    public function handle(Select $select, Entity $entity): void
    {
        $select->where('id', '>', 3);
    }
}

class Demo2Specification extends Specification
{
    private $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function isSatisfiedBy(Entity $entity): bool
    {
        return 'world' === $this->request['hello'];
    }

    public function handle(Select $select, Entity $entity): void
    {
        $select->where('id', '<', 8);
    }
}
