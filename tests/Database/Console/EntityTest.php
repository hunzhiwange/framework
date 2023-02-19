<?php

declare(strict_types=1);

namespace Tests\Database\Console;

use Leevel\Console\Make;
use Leevel\Database\Console\Entity;
use Leevel\Database\Manager;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Tests\Console\BaseMake;
use Tests\Database\DatabaseTestCase as TestCase;

final class EntityTest extends TestCase
{
    use BaseMake;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clearConsoleFiles();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->clearConsoleFiles();
    }

    public function testBaseUse(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        static::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithTable(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--table' => 'test',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        static::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithStub(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--stub' => __DIR__.'/assert/stub/entity',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        static::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        static::assertStringContainsString('protected ?string $createAt = null;', $content);
        static::assertStringContainsString('#[Struct([', $content);
        static::assertStringContainsString('self::COLUMN_STRUCT => [', $content);
    }

    public function testWithForce(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        static::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('File is already exits.'), $result);

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--force' => true,
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithRefresh(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));
        Helper::createFile($file, file_get_contents(__DIR__.'/assert/refresh'));
        static::assertTrue(is_file($file));

        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        static::assertStringNotContainsString('\'name\' =>', $content);

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--refresh' => true,
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        static::assertStringContainsString('protected ?string $name = null;', $content);
        static::assertStringContainsString('self::COLUMN_NAME => \'名字\',', $content);
        static::assertStringContainsString('self::COLUMN_STRUCT => [', $content);
    }

    public function testWithTableNotFound(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--table' => 'table_test_not_found',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        static::assertFalse(is_file($file));

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('Table (table_test_not_found) is not found or has no columns.'), $result);
    }

    public function testWithStubNotFound(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--stub' => 'stub_not_found',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        static::assertFalse(is_file($file));

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('Entity stub file `stub_not_found` was not found.'), $result);
    }

    public function testWithRefreshExtendsStruct(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));
        Helper::createFile($file, file_get_contents(__DIR__.'/assert/refresh_with_extends_struct'));
        static::assertTrue(is_file($file));

        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        static::assertStringContainsString('protected ?Comment $extends1 = null;', $content);

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--refresh' => true,
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        static::assertStringContainsString('#[Struct([', $content);
        static::assertStringContainsString('protected ?Comment $extends1 = null;', $content);
    }

    public function testWithRefreshButNotExistsOld(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--refresh' => true,
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        static::assertTrue(is_file($file));

        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithRefreshCanNotFindStartAndEndPositionOfStruct(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));
        Helper::createFile($file, file_get_contents(__DIR__.'/assert/refresh_with_bad_struct'));
        static::assertTrue(is_file($file));

        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        static::assertStringContainsString('pr2otected ?Comment $extends1 = null;', $content);

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--refresh' => true,
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('Can not find start and end position of struct.'), $result);
    }

    public function testWithTableWithoutPrimaryKey(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--table' => 'without_primarykey',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        static::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        static::assertStringContainsString('const ID = null;', $content);
    }

    public function testWithTableWithoutCompositePrimaryKey(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--table' => 'composite_id',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        static::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        static::assertStringContainsString("const ID = ['id1', 'id2'];", $content);
    }

    public function testWithComposerJson(): void
    {
        $file = __DIR__.'/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--table' => 'role_soft_deleted',
        ], function ($container): void {
            $this->initContainerService($container);
            $container->make('app')->setPath(__DIR__);
        });

        static::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        static::assertStringContainsString("const DELETE_AT = 'delete_at';", $content);
    }

    public function testWithTableFieldAllowedNull(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        static::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--namespace' => 'App',
            '--table' => 'field_allowed_null',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        static::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        static::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        static::assertStringContainsString('self::COLUMN_NAME => \'商品 ID\',', $content);
        static::assertStringContainsString('self::COLUMN_STRUCT => [', $content);
        static::assertStringContainsString("'type' => 'bigint'", $content);
        static::assertStringContainsString('protected ?string $description = null;', $content);
    }

    protected function clearConsoleFiles(): void
    {
        $dirs = [
            __DIR__.'/../../Console/Domain',
            __DIR__.'/Domain',
        ];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                Helper::deleteDirectory($dir);
            }
        }
    }

    protected function initContainerService(IContainer $container): void
    {
        $manager = $this->createDatabaseManager();
        $container
            ->singleton(
                'databases',
                fn (IContainer $container): Manager => $manager,
            )
        ;
        $container->alias('databases', Manager::class);

        Make::setGlobalReplace([
            'file_comment' => <<<'EOT'
                /**
                 * {{file_title}}.
                 */
                EOT,
            'file_name' => '',
        ]);
    }
}
