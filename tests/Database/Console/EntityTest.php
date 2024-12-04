<?php

declare(strict_types=1);

namespace Tests\Database\Console;

use Leevel\Console\Make;
use Leevel\Database\Console\Entity;
use Leevel\Database\Manager;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper\CreateFile;
use Leevel\Filesystem\Helper\DeleteDirectory;
use Tests\Console\BaseMake;
use Tests\Database\DatabaseTestCase as TestCase;

final class EntityTest extends TestCase
{
    use BaseMake;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->clearConsoleFiles();
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->clearConsoleFiles();
    }

    public function testBaseUse(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithTable(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--table' => 'test',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithStub(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--stub' => __DIR__.'/assert/stub/entity',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString('public ?string $createAt', $content);
        self::assertStringContainsString('#[Struct([', $content);
        self::assertStringContainsString('self::COLUMN_STRUCT => [', $content);
    }

    public function testWithForce(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('File is already exits.'), $result);

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--force' => true,
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithRefresh(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));
        CreateFile::handle($file, file_get_contents(__DIR__.'/assert/refresh'));
        self::assertTrue(is_file($file));

        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        self::assertStringNotContainsString('\'name\' =>', $content);

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--refresh' => true,
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString('public ?string $name', $content);
        self::assertStringContainsString('self::COLUMN_NAME => \'名字\',', $content);
        self::assertStringContainsString('self::COLUMN_STRUCT => [', $content);
    }

    public function testWithTableNotFound(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--table' => 'table_test_not_found',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertFalse(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('Table (table_test_not_found) is not found or has no columns.'), $result);
    }

    public function testWithStubNotFound(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--stub' => 'stub_not_found',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertFalse(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('Entity stub file `stub_not_found` was not found.'), $result);
    }

    public function testWithRefreshExtendsStruct(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));
        CreateFile::handle($file, file_get_contents(__DIR__.'/assert/refresh_with_extends_struct'));
        self::assertTrue(is_file($file));

        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString('protected ?Comment $extends1 = null;', $content);

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--refresh' => true,
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString('#[Struct([', $content);
        self::assertStringContainsString('protected ?Comment $extends1 = null;', $content);
    }

    public function testWithRefreshButNotExistsOld(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--refresh' => true,
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        self::assertTrue(is_file($file));

        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithRefreshCanNotFindStartAndEndPositionOfStruct(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));
        CreateFile::handle($file, file_get_contents(__DIR__.'/assert/refresh_with_bad_struct'));
        self::assertTrue(is_file($file));

        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString('pr2otected ?Comment $extends1 = null;', $content);

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--refresh' => true,
        ], function ($container): void {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('Can not find start and end position of struct.'), $result);
    }

    public function testWithTableWithoutPrimaryKey(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--table' => 'without_primarykey',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString('public const null ID = null;', $content);
    }

    public function testWithTableWithoutCompositePrimaryKey(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--table' => 'composite_id',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString("public const array ID = ['id1', 'id2'];", $content);
    }

    public function testWithComposerJson(): void
    {
        $file = __DIR__.'/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--table' => 'role_soft_deleted',
        ], function ($container): void {
            $this->initContainerService($container);
            $container->make('app')->setPath(__DIR__);
        });

        self::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString("public const string DELETE_AT = 'delete_at';", $content);
    }

    public function testWithTableFieldAllowedNull(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/Test.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'test',
            '--table' => 'field_allowed_null',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        self::assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString('self::COLUMN_NAME => \'商品ID\',', $content);
        self::assertStringContainsString('self::COLUMN_STRUCT => [', $content);
        self::assertStringContainsString("'type' => 'bigint'", $content);
        self::assertStringContainsString('public ?string $description', $content);
    }

    public function testWithTableFieldFloat(): void
    {
        $file = __DIR__.'/../../Console/Base/Entity/FieldFloat.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'FieldFloat',
            '--table' => 'field_float',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('entity <FieldFloat> created successfully.'), $result);
        self::assertStringContainsString('class FieldFloat extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString('self::COLUMN_NAME => \'价格\',', $content);
        self::assertStringContainsString('self::COLUMN_STRUCT => [', $content);
        self::assertStringContainsString("'type' => 'float'", $content);
        self::assertStringContainsString('public ?float $price', $content);
    }

    public function testWithTableFieldFloat2(): void
    {
        $file = __DIR__.'/../../Console/Foo/Entity/FieldFloat.php';
        self::assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command' => 'make:entity',
            'name' => 'Foo:FieldFloat',
            '--table' => 'field_float',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        self::assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        self::assertStringContainsString($this->normalizeContent('entity <Foo:FieldFloat> created successfully.'), $result);
        self::assertStringContainsString('class FieldFloat extends Entity', $content = file_get_contents($file));
        self::assertStringContainsString('self::COLUMN_NAME => \'价格\',', $content);
        self::assertStringContainsString('self::COLUMN_STRUCT => [', $content);
        self::assertStringContainsString("'type' => 'float'", $content);
        self::assertStringContainsString('public ?float $price', $content);
    }

    protected function clearConsoleFiles(): void
    {
        $dirs = [
            __DIR__.'/../../Console/Base',
            __DIR__.'/../../Console/Foo',
            __DIR__.'/Base',
        ];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                DeleteDirectory::handle($dir);
            }
        }
    }

    protected function initContainerService(IContainer $container): void
    {
        $manager = $this->createDatabaseManager();
        $container
            ->singleton(
                'databases',
                static fn (IContainer $container): Manager => $manager,
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
