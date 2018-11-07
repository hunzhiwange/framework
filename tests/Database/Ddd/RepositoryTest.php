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

namespace Tests\Database\Ddd;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;
use Leevel\Database\Ddd\ISpecification;
use Leevel\Database\Ddd\Meta;
use Leevel\Database\Ddd\Repository;
use Leevel\Database\Ddd\Select;
use Leevel\Database\Ddd\Specification;
use Leevel\Database\Ddd\SpecificationExpression;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\Database\Query\Query;
use Tests\TestCase;

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
    use Query;

    protected function setUp()
    {
        $this->clear();

        Meta::setDatabaseResolver(function () {
            return $this->createManager();
        });
    }

    protected function tearDown()
    {
        $this->clear();

        Meta::setDatabaseResolver(null);
    }

    public function testBase()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
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

        $this->clear();
    }

    public function testFind()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
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

        $this->clear();
    }

    public function testFindOrFail()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
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

        $this->clear();
    }

    public function testFindOrFailNotFound()
    {
        $this->expectException(\Leevel\Database\Ddd\EntityNotFoundException::class);
        $this->expectExceptionMessage(
            'Entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post` was not found.'
        );

        $repository = new Repository(new Post());

        $newPost = $repository->findOrFail(1);
    }

    public function testSpecWithClosure()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $select = $repository->spec($andSpec);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame(4, count($result));

        $this->truncate('post');
    }

    public function testSpecWithClass()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $select = $repository->spec($andSpec);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame(4, count($result));

        $this->truncate('post');
    }

    public function testExpr()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $select = $repository->spec($specExpr);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame(4, count($result));

        $this->truncate('post');
    }

    public function testFindAllBySpecWithClosure()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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
        $this->assertSame(4, count($result));

        $this->truncate('post');
    }

    public function testFindAllBySpecWithClass()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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
        $this->assertSame(4, count($result));

        $this->truncate('post');
    }

    public function testFindAllByExpr()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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
        $this->assertSame(4, count($result));

        $this->truncate('post');
    }

    public function testFindCountBySpecWithClosure()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountBySpecWithClass()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountByExpr()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindAllBySpecWithClosureForNot()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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
        $this->assertSame(10, count($result));

        $this->truncate('post');
    }

    public function testFindAllBySpecWithClosureForNotButValueIsTrue()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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
        $this->assertSame(7, count($result));

        $this->truncate('post');
    }

    public function testSpecWithOrFirstIsNo()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $select = $repository->spec($orSpec);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame(7, count($result));

        $this->truncate('post');
    }

    public function testSpecWithOrFirstIsYes()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $select = $repository->spec($orSpec);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame(6, count($result));

        $this->truncate('post');
    }

    public function testSpecWithOrFirstIsNoSecondAlsoIsNo()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $select = $repository->spec($orSpec);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame(10, count($result));

        $this->truncate('post');
    }

    public function testSpecMake()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $select = $repository->spec($spec);
        $result = $select->findAll();

        $this->assertInstanceof(Select::class, $select);
        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame(6, count($result));

        $this->truncate('post');
    }

    public function testSpecificationExpressionMake()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithOrClosureFirstIsYes()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithOrClosureFirstIsNo()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithOrClosureFirstIsNoAndSecondAlsoIsNo()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithOrFirstIsYes()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithOrFirstIsNo()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithOrFirstIsNoAndSecodeAlsoIsNo()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithAndFirstIsYes()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithAndFirstIsNo()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithAndFirstIsYesSecodeIsNo()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithAndFirstIsNoSecodeIsNo()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithAndIsYes()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testFindCountWithAndIsNo()
    {
        $connect = $this->createConnectTest();

        for ($i = 0; $i < 10; $i++) {
            $connect->
            table('post')->
            insert([
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

        $this->truncate('post');
    }

    public function testCall()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $repository = new Repository(new Post());

        $newPost = $repository->where('id', 5)->find(1);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertNull($newPost->id);
        $this->assertNull($newPost->userId);
        $this->assertNull($newPost->title);
        $this->assertNull($newPost->summary);

        $this->clear();
    }

    public function testCreateFlushed()
    {
        $repository = new Repository(new Post());

        $repository->create($post = new Post(['id' => 5, 'title' => 'foo']));

        $repository->create($post); // do nothing.

        $newPost = $repository->find(5);

        $this->assertInstanceof(Post::class, $newPost);
        $this->assertSame('5', $newPost->id);
        $this->assertSame('foo', $newPost->title);

        $this->clear();
    }

    public function testUpdateFlushed()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
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

        $this->clear();
    }

    public function testReplaceFlushed()
    {
        // phpunit 不支持 try catch
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage(
            '(1062)Duplicate entry \'1\' for key \'PRIMARY\''
        );

        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
            'title'   => 'hello world',
            'user_id' => 1,
            'summary' => 'post summary',
        ]));

        $repository = new Repository(new Post());

        $repository->replace($post = new Post(['id' => 1, 'title' => 'new title']));

        // 非 phpunit 模式下面系统会更新 post 的数据
    }

    public function testReplaceFlushed2()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
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

    public function testDeleteFlushed()
    {
        $connect = $this->createConnectTest();

        $this->assertSame('1', $connect->
        table('post')->
        insert([
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

        $this->clear();
    }

    protected function clear()
    {
        $this->truncate('post');
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

    public function handle(Select $select, IEntity $entity)
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

    public function handle(Select $select, IEntity $entity)
    {
        $select->where('id', '<', 8);
    }
}
