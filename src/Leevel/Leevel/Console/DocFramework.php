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

namespace Leevel\Leevel\Console;

use Leevel\Console\Argument;
use Leevel\Console\Command;
use Leevel\Kernel\IApp;
use Leevel\Option\IOption;

/**
 * 解析内部单元测试用例为 Markdown 文档.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.03.23
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class DocFramework extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'make:docwithin';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Markdown generation based on within test cases.';

    /**
     * 响应命令.
     *
     * @param \Leevel\Kernel\IApp    $app
     * @param \Leevel\Option\IOption $option
     */
    public function handle(IApp $app, IOption $option): void
    {
        $input = [
            'path'      => $this->path(),
            'testsdir'  => $app->path('vendor/hunzhiwange/framework/tests'),
            'outputdir' => $option->get('console\\framework_doc_outputdir'),
        ];

        $this->call('make:doc', $input);
    }

    /**
     * 取得测试用例文件或者目录相对路径.
     *
     * @return string
     */
    protected function path(): string
    {
        return $this->argument('path');
    }

    /**
     * 命令参数.
     *
     * @return array
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

    /**
     * 命令配置.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [];
    }
}
