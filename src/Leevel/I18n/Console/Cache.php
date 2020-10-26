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

namespace Leevel\I18n\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\create_file;
use function Leevel\Filesystem\Helper\create_file;
use function Leevel\Filesystem\Helper\traverse_directory;
use Leevel\Filesystem\Helper\traverse_directory;
use Leevel\I18n\Load;
use Leevel\Kernel\Bootstrap\LoadI18n;
use Leevel\Kernel\IApp;

/**
 * 语言包缓存.
 */
class Cache extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'i18n:cache';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Cache i18n to a file';

    /**
     * 应用.
     *
     * @var \Leevel\Kernel\IApp
     */
    protected IApp $app;

    /**
     * 扩展语言包目录.
     *
     * @var array
     */
    protected array $extends = [];

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->app = $app;
        $this->extends = $this->extends();
        $this->line('Start to cache i18n.');

        traverse_directory($app->i18nPath(), false, function ($item) use ($app): void {
            if ($item->isDir()) {
                $i18n = $item->getFilename();
                $data = $this->data($i18n);
                $cachePath = $app->i18nCachedPath($i18n);
                $this->writeCache($cachePath, $data);
                $this->info(sprintf('I18n cache successed at %s.', $cachePath));
            }
        });

        $this->info('I18n cache successed.');

        return 0;
    }

    /**
     * 获取语言包扩展.
     */
    protected function extends(): array
    {
        return (new LoadI18n())->getExtend($this->app);
    }

    /**
     * 获取语言数据.
     */
    protected function data(string $i18n): array
    {
        $load = (new Load([$this->app->i18nPath()]))
            ->setI18n($i18n)
            ->addDir($this->extends);

        return $load->loadData();
    }

    /**
     * 写入缓存.
     */
    protected function writeCache(string $cachePath, array $data): void
    {
        $content = '<?'.'php /* '.date('Y-m-d H:i:s').' */ ?'.'>'.
            PHP_EOL.'<?'.'php return '.var_export($data, true).'; ?'.'>';
        create_file($cachePath, $content);
    }
}

// import fn.
class_exists(traverse_directory::class);
class_exists(create_file::class);
