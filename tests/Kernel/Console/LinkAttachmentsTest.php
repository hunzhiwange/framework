<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\LinkAttachments;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class LinkAttachmentsTest extends TestCase
{
    use BaseCommand;

    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirs = [
            __DIR__.'/assert_new',
        ];
        foreach ($dirs as $dir) {
            Helper::deleteDirectory($dir);
        }
    }

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new LinkAttachments(),
            [
                'command' => 'link:attachments',
            ],
            function ($container): void {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Linked `%s/assert/attachments` directory to `%s/assert_new/attachments` successed.', __DIR__, __DIR__)),
            $result
        );
        static::assertTrue(is_file(__DIR__.'/assert_new/attachments'));
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForLinkAttachments($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForLinkAttachments extends Apps
{
    public function path(string $path = ''): string
    {
        return __DIR__.'/assert_new/attachments';
    }

    public function storagePath(string $path = ''): string
    {
        return __DIR__.'/assert/attachments';
    }

    protected function registerBaseProvider(): void
    {
    }
}
