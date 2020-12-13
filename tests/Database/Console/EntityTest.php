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

class EntityTest extends TestCase
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
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithTable(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--table'     => 'test',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithStub(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--stub'      => __DIR__.'/assert/stub/entity',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString('custom stub', $content);
    }

    public function testWithProp(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--prop'      => true,
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString('private $_id', $content);
        $this->assertStringContainsString('private $_name', $content);
    }

    public function testWithForce(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
        ], function ($container) {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('File is already exits.'), $result);

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--force'     => true,
        ], function ($container) {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithRefresh(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));
        Helper::createFile($file, file_get_contents(__DIR__.'/assert/refresh'));
        $this->assertTrue(is_file($file));

        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringNotContainsString('\'name\' =>', $content);

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--refresh'   => true,
        ], function ($container) {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString('\'name\' =>', $content);
    }

    public function testWithTableNotFound(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--table'     => 'table_test_not_found',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertFalse(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('Table (table_test_not_found) is not found or has no columns.'), $result);
    }

    public function testWithStubNotFound(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--stub'      => 'stub_not_found',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertFalse(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('Entity stub file `stub_not_found` was not found.'), $result);
    }

    public function testWithRefreshExtendsStruct(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));
        Helper::createFile($file, file_get_contents(__DIR__.'/assert/refresh_with_extends_struct'));
        $this->assertTrue(is_file($file));

        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringNotContainsString('\'name\' =>', $content);
        $this->assertStringContainsString('\'extends1\'', $content);

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--refresh'   => true,
        ], function ($container) {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString('\'name\' =>', $content);
        $this->assertStringContainsString('\'extends1\'', $content);
    }

    public function testWithRefreshButNotExistsOld(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--refresh'   => true,
        ], function ($container) {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        $this->assertTrue(is_file($file));

        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
    }

    public function testWithPropAndRefresh(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--prop'      => true,
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString('private $_id', $content);
        $this->assertStringContainsString('private $_name', $content);

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--prop'      => true,
            '--refresh'   => true,
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString('private $_id', $content);
        $this->assertStringContainsString('private $_name', $content);
    }

    public function testWithRefreshCanNotFindStartAndEndPositionOfStruct(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));
        Helper::createFile($file, file_get_contents(__DIR__.'/assert/refresh_with_bad_struct'));
        $this->assertTrue(is_file($file));

        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringNotContainsString('\'name\' =>', $content);
        $this->assertStringContainsString('\'extends1\'', $content);

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--refresh'   => true,
        ], function ($container) {
            $this->initContainerService($container);
        });
        $result = $this->normalizeContent($result);

        $this->assertStringContainsString($this->normalizeContent('Can not find start and end position of struct.'), $result);
    }

    public function testWithTableWithoutPrimaryKey(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--table'     => 'without_primarykey',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString('const ID = null;', $content);
    }

    public function testWithTableWithoutCompositePrimaryKey(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--table'     => 'composite_id',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString("const ID = ['id1', 'id2'];", $content);
    }

    public function testWithTableWithoutFieldComment(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--table'     => 'test_query_subsql',
            '--prop'      => true,
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString('* name.', $content);
    }

    public function testWithComposerJson(): void
    {
        $file = __DIR__.'/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--table'     => 'role_soft_deleted',
            '--prop'      => true,
        ], function ($container) {
            $this->initContainerService($container);
            $container->make('app')->setPath(__DIR__);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString('private $_deleteAt;', $content);
        $this->assertStringContainsString("const DELETE_AT = 'delete_at';", $content);
    }

    public function testWithTableFieldAllowedNull(): void
    {
        $file = __DIR__.'/../../Console/Domain/Entity/Test.php';
        $this->assertFalse(is_file($file));

        $result = $this->runCommand(new Entity(), [
            'command'     => 'make:entity',
            'name'        => 'test',
            '--namespace' => 'Common',
            '--table'     => 'field_allowed_null',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertTrue(is_file($file));

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('entity <test> created successfully.'), $result);
        $this->assertStringContainsString('class Test extends Entity', $content = file_get_contents($file));
        $this->assertStringContainsString('null: true', $content);
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
            );
        $container->alias('databases', Manager::class);

        Make::setGlobalReplace([
            'file_comment' => <<<'EOT'
                /**
                 * {{file_title}}.
                 */
                EOT,
            'file_name'    => '',
        ]);
    }
}
