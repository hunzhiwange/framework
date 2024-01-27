<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Config\IConfig;
use Leevel\Console\Command;
use Leevel\Kernel\IApp;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 解析内部单元测试用例为 Markdown 文档.
 */
class DocFramework extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'make:docwithin';

    /**
     * 命令行描述.
     */
    protected string $description = 'Markdown generation based on within test cases';

    /**
     * 响应命令.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function handle(IApp $app, IConfig $config): int
    {
        $input = [
            'path' => $this->getArgument('path'),
            'outputdir' => $config->get('console\\framework_doc_outputdir'),
            '--bootstrap' => $app->path('vendor/hunzhiwange/framework/tests/bootstrap.php'),
            '--git' => $config->get('console\\framework_doc_git'),
            '--logdir' => $config->get('console\\framework_doc_logdir'),
        ];

        $i18n = explode(',', (string) $config->get('console\\framework_doc_i18n'));
        foreach ($i18n as $v) {
            $input['--i18n'] = $v;
            $this->call('make:doc', $input);
        }

        return self::SUCCESS;
    }

    /**
     * 命令参数.
     */
    protected function getArguments(): array
    {
        return [
            [
                'path',
                InputArgument::REQUIRED,
                'This is the tests file or dir path.',
            ],
        ];
    }
}
