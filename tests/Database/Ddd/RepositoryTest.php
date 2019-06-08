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

namespace Tests\Database\Ddd;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;
use Leevel\Database\Ddd\ISpecification;
use Leevel\Database\Ddd\Repository;
use Leevel\Database\Ddd\Select;
use Leevel\Database\Ddd\Specification;
use Leevel\Database\Ddd\SpecificationExpression;
use Leevel\Page\IPage;
use Leevel\Page\Page;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Ddd\Entity\TestUnique;

/**
 * repository test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.29
 *
 * @version 1.0
 */
class RepositoryTest extends TestCase
{
    public function testBase(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]));

        $repository = new Repository(new Post());

        $newPost = $repository->find(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame('1', $newPost->id);
        $this->assertSame('1', $newPost->userId);
        $this->assertSame('hello world', $newPost->title);
        $this->assertSame('post summary', $newPost->summary);
        $this->assertInstanceof(Post::class, $repository->entity());
    }

    public function testFind(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]));

        $repository = new Repository(new Post());

        $newPost = $repository->find(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame('1', $newPost->id);
        $this->assertSame('1', $newPost->userId);
        $this->assertSame('hello world', $newPost->title);
        $this->assertSame('post summary', $newPost->summary);
    }

    public function testFindOrFail(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]));

        $repository = new Repository(new Post());

        $newPost = $repository->find(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame('1', $newPost->id);
        $this->assertSame('1', $newPost->userId);
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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $andSpec = $spec->and(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $andSpec);
        $this->assertInstanceof(Specification::class, $andSpec);

        $select = $repository->condition($andSpec);
        $result = $select->findAll();

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Demo1Specification($request);

        $andSpec = $spec->and(new Demo2Specification($request));

        $this->assertInstanceof(ISpecification::class, $andSpec);
        $this->assertInstanceof(Specification::class, $andSpec);

        $select = $repository->condition($andSpec);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(4, $result);
    }

    public function testExpr(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->andClosure(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 8);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

        $select = $repository->condition($specExpr);
        $result = $select->findAll();

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $andSpec = $spec->and(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $andSpec);

        $this->assertInstanceof(Specification::class, $andSpec);

        $result = $repository->findAll($andSpec);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Demo1Specification($request);

        $andSpec = $spec->and(new Demo2Specification($request));

        $this->assertInstanceof(ISpecification::class, $andSpec);
        $this->assertInstanceof(Specification::class, $andSpec);

        $result = $repository->findAll($andSpec);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->andClosure(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 8);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

        $result = $repository->findAll($specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $andSpec = $spec->and(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $andSpec);
        $this->assertInstanceof(Specification::class, $andSpec);

        $result = $repository->findCount($andSpec);

        $this->assertSame(4, $result);
    }

    public function testFindCountBySpecWithClass(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Demo1Specification($request);

        $andSpec = $spec->and(new Demo2Specification($request));

        $this->assertInstanceof(ISpecification::class, $andSpec);
        $this->assertInstanceof(Specification::class, $andSpec);

        $result = $repository->findCount($andSpec);

        $this->assertSame(4, $result);
    }

    public function testFindCountByExpr(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->andClosure(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 8);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(4, $result);
    }

    public function testFindAllBySpecWithClosureForNot(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $notSpec = $spec->not();

        $this->assertInstanceof(ISpecification::class, $notSpec);
        $this->assertInstanceof(Specification::class, $notSpec);

        $result = $repository->findAll($notSpec);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'not_bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $notSpec = $spec->not();

        $this->assertInstanceof(ISpecification::class, $notSpec);
        $this->assertInstanceof(Specification::class, $notSpec);

        $result = $repository->findAll($notSpec);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $orSpec = $spec->or(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $orSpec);
        $this->assertInstanceof(Specification::class, $orSpec);

        $select = $repository->condition($orSpec);
        $result = $select->findAll();

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 4);
        });

        $orSpec = $spec->or(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 8);
        }));

        $this->assertInstanceof(ISpecification::class, $orSpec);
        $this->assertInstanceof(Specification::class, $orSpec);

        $select = $repository->condition($orSpec);
        $result = $select->findAll();

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $spec = new Specification(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $orSpec = $spec->or(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $spec = Specification::make(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 4);
        });

        $select = $repository->condition($spec);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(6, $result);
    }

    public function testSpecificationExpressionMake(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = SpecificationExpression::make(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->orClosure(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 6);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->orClosure(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 6);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->orClosure(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 6);
        });

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->or(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->or(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->or(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->and(new Specification(function (IEntity $entity) use ($request) {
            return 'world' === $request['hello'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '<', 6);
        }));

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->not();

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'world'];

        $repository = new Repository(new Post());

        $specExpr = new SpecificationExpression(function (IEntity $entity) use ($request) {
            return 'bar' === $request['foo'];
        }, function (Select $select, IEntity $entity) {
            $select->where('id', '>', 3);
        });

        $specExpr->not();

        $this->assertInstanceof(ISpecification::class, $specExpr);
        $this->assertInstanceof(SpecificationExpression::class, $specExpr);

        $result = $repository->findCount($specExpr);

        $this->assertSame(7, $result);
    }

    public function testCall(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]));

        $repository = new Repository(new Post());

        $newPost = $repository
            ->where('id', 5)
            ->find(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertNull($newPost->id);
        $this->assertNull($newPost->userId);
        $this->assertNull($newPost->title);
        $this->assertNull($newPost->summary);
    }

    public function testCreateFlushed(): void
    {
        $repository = new Repository(new Post());

        $repository->create($post = new Post(['id' => 5, 'title' => 'foo']));

        $repository->create($post); // do nothing.

        $newPost = $repository->find(5);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame('5', $newPost->id);
        $this->assertSame('foo', $newPost->title);
    }

    public function testUpdateFlushed(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]));

        $repository = new Repository(new Post());

        $repository->update($post = new Post(['id' => 1, 'title' => 'new title']));

        $repository->update($post); // do nothing.

        $newPost = $repository->find(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame('1', $newPost->id);
        $this->assertSame('new title', $newPost->title);
    }

    public function testReplaceFlushed(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]));

        $repository = new Repository(new Post());

        $affectedRow = $repository->replace($post = new Post(['id' => 1, 'title' => 'new title']));

        $this->assertSame(1, $affectedRow);

        $updatedPost = $repository->find(1);

        $this->assertSame('1', $updatedPost->id);
        $this->assertSame('new title', $updatedPost->title);
        $this->assertSame('1', $updatedPost->userId);
        $this->assertSame('post summary', $updatedPost->summary);
    }

    public function testReplaceFlushed2(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]));

        $repository = new Repository(new Post());

        $repository->replace($post = new Post(['id' => 2, 'title' => 'new title']));

        $repository->replace($post); // do nothing.

        $newPost = $repository->find(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame('1', $newPost->id);
        $this->assertSame('hello world', $newPost->title);
        $this->assertSame('post summary', $newPost->summary);

        $newPost2 = $repository->find(2);

        $this->assertInstanceof(Post::class, $newPost2);
        $this->assertSame('2', $newPost2->id);
        $this->assertSame('new title', $newPost2->title);
        $this->assertSame('', $newPost2->summary);
    }

    public function testReplaceUnique(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('test_unique')
                ->insert([
                    'name'     => 'hello world',
                    'identity' => 'hello',
                ])
        );

        $testUniqueData = TestUnique::find(1);

        $this->assertInstanceof(TestUnique::class, $testUniqueData);
        $this->assertSame('1', $testUniqueData->id);
        $this->assertSame('hello world', $testUniqueData->name);
        $this->assertSame('hello', $testUniqueData->identity);

        $testUnique = new TestUnique(['id' => 1, 'name' => 'hello new', 'identity' => 'hello']);

        $repository = new Repository($testUnique);

        $repository->replace($testUnique);

        $testUniqueData = TestUnique::find(1);

        $this->assertInstanceof(TestUnique::class, $testUniqueData);
        $this->assertSame('1', $testUniqueData->id);
        $this->assertSame('hello new', $testUniqueData->name);
        $this->assertSame('hello', $testUniqueData->identity);
    }

    public function testDeleteFlushed(): void
    {
        $connect = $this->createDatabaseConnect();

        $this->assertSame(
            '1',
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]));

        $repository = new Repository(new Post());

        $repository->delete($post = new Post(['id' => 1, 'title' => 'new title']));

        $repository->delete($post); // do nothing.

        $newPost = $repository->find(1);

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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, IEntity $entity) use ($request) {
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

        $select = $repository->condition(5);
    }

    public function testFindPage(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $repository = new Repository(new Post());

        list($page, $result) = $repository->findPage(1, 10);

        $this->assertIsArray($page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(10, $result);

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_record": 10,
                "from": 0
            }
            eot;

        $this->assertSame(
            $data,
                $this->varJson(
                    $page
                )
        );
    }

    public function testFindPageWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, IEntity $entity) use ($request) {
            $select->where('id', '<', 8);
        };

        list($page, $result) = $repository->findPage(1, 10, $condition);

        $this->assertIsArray($page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(7, $result);

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_record": 7,
                "from": 0
            }
            eot;

        $this->assertSame(
            $data,
                $this->varJson(
                    $page
                )
        );
    }

    public function testFindPageHtml(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $repository = new Repository(new Post());

        list($page, $result) = $repository->findPageHtml(1, 10);

        $this->assertInstanceof(IPage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(10, $result);
    }

    public function testFindPageHtmlWithCondition(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, IEntity $entity) use ($request) {
            $select->where('id', '<', 8);
        };

        list($page, $result) = $repository->findPageHtml(1, 10, $condition);

        $this->assertInstanceof(IPage::class, $page);
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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $repository = new Repository(new Post());

        list($page, $result) = $repository->findPageMacro(1, 10);

        $this->assertInstanceof(IPage::class, $page);
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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, IEntity $entity) use ($request) {
            $select->where('id', '<', 8);
        };

        list($page, $result) = $repository->findPageMacro(1, 10, $condition);

        $this->assertInstanceof(IPage::class, $page);
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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $repository = new Repository(new Post());

        list($page, $result) = $repository->findPagePrevNext(1, 10);

        $this->assertInstanceof(IPage::class, $page);
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
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, IEntity $entity) use ($request) {
            $select->where('id', '<', 8);
        };

        list($page, $result) = $repository->findPagePrevNext(1, 10, $condition);

        $this->assertInstanceof(IPage::class, $page);
        $this->assertInstanceof(Page::class, $page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(7, $result);
    }

    public function testFindPageWithOnlyScope(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        list($page, $result) = $repository->findPage(1, 10, 'test');

        $this->assertIsArray($page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(6, $result);

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_record": 6,
                "from": 0
            }
            eot;

        $this->assertSame(
            $data,
                $this->varJson(
                    $page
                )
        );
    }

    public function testFindPageWithOnlyScopeSplitToArray(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        list($page, $result) = $repository->findPage(1, 10, 'test,test');

        $this->assertIsArray($page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(6, $result);

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_record": 6,
                "from": 0
            }
            eot;

        $this->assertSame(
            $data,
                $this->varJson(
                    $page
                )
        );
    }

    public function testFindPageWithConditionAndScope(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, IEntity $entity) use ($request) {
            $select->where('id', '<', 8);
        };

        list($page, $result) = $repository->findPage(1, 10, [$condition, 'test']);

        $this->assertIsArray($page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(3, $result);

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_record": 3,
                "from": 0
            }
            eot;

        $this->assertSame(
            $data,
                $this->varJson(
                    $page
                )
        );
    }

    public function testFindPageWithConditionAndScopeAndScopeIsArray(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world',
                    'user_id' => 1,
                    'summary' => 'post summary',
                ]);
        }

        $request = ['foo' => 'no-bar', 'hello' => 'no-world'];

        $repository = new Repository(new Post());

        $condition = function (Select $select, IEntity $entity) use ($request) {
            $select->where('id', '<', 8);
        };

        list($page, $result) = $repository->findPage(1, 10, [$condition, 'test', 'test']);

        $this->assertIsArray($page);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertCount(3, $result);

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_record": 3,
                "from": 0
            }
            eot;

        $this->assertSame(
            $data,
                $this->varJson(
                    $page
                )
        );
    }

    public function testFindList(): void
    {
        $connect = $this->createDatabaseConnect();

        for ($i = 0; $i < 10; $i++) {
            $connect
                ->table('post')
                ->insert([
                    'title'   => 'hello world'.$i,
                    'user_id' => 1,
                    'summary' => 'post summary',
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
                    'title'   => 'hello world'.$i,
                    'user_id' => 1,
                    'summary' => 'post summary',
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
                    'title'   => 'hello world'.$i,
                    'user_id' => 1,
                    'summary' => 'post summary',
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

    public function isSatisfiedBy(IEntity $entity): bool
    {
        return 'bar' === $this->request['foo'];
    }

    public function handle(Select $select, IEntity $entity): void
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

    public function isSatisfiedBy(IEntity $entity): bool
    {
        return 'world' === $this->request['hello'];
    }

    public function handle(Select $select, IEntity $entity): void
    {
        $select->where('id', '<', 8);
    }
}
