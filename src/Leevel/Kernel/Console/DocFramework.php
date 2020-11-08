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
     *
     * @var string
     */
    protected string $name = 'make:docwithin';

    /**
     * 命令行描述.
     *
     * @var string
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
