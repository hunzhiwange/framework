<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Argument;
use Leevel\Console\Command;
use Leevel\Kernel\IApp;
use Leevel\Option\IOption;

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
     */
    public function handle(IApp $app, IOption $option): int
    {
        $input = [
            'path'       => $this->getArgument('path'),
            'bootstrap'  => $app->path('vendor/hunzhiwange/framework/tests/bootstrap.php'),
            'outputdir'  => $option->get('console\\framework_doc_outputdir'),
            'git'        => $option->get('console\\framework_doc_git'),
            '--logdir'   => $option->get('console\\framework_doc_logdir'),
        ];

        $i18n = explode(',', $option->get('console\\framework_doc_i18n'));
        foreach ($i18n as $v) {
            $input['--i18n'] = $v;
            $this->call('make:doc', $input);
        }

        return 0;
    }

    /**
     * 命令参数.
     */
    protected function getArguments(): array
    {
        return [
            [
                'path',
                Argument::REQUIRED,
                'This is the tests file or dir path.',
            ],
        ];
    }
}
