<?php

declare(strict_types=1);

namespace Tests\Console;

use Leevel\Console\Application;
use Leevel\Console\Command;
use Leevel\Di\Container;
use Tests\TestCase;

final class ApplicationTest extends TestCase
{
    public function testBaseUse(): void
    {
        $application = new Application(new Container(), '1.0');

        static::assertSame($application->getVersion(), '1.0');

        $this->assertInstanceof(Container::class, $application->getContainer());

        $application->add(new Test1());

        $this->assertInstanceof(Test1::class, $application->get('test1'));
    }

    public function testNormalizeCommand(): void
    {
        $application = new Application(new Container(), '1.0');

        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        $application->normalizeCommands([Test2::class]);

        static::assertSame($_SERVER['test'], '1');

        unset($_SERVER['test']);
    }
}

class Test1 extends Command
{
    protected string $name = 'test1';

    protected string $description = 'test1 for command';

    public function handle(): void
    {
    }
}

class Test2 extends Command
{
    protected string $name = 'test2';

    protected string $description = 'test2 for command';

    public function __construct()
    {
        parent::__construct();

        $_SERVER['test'] = '1';
    }

    public function handle(): void
    {
    }
}
