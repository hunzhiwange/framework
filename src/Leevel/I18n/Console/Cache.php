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

namespace Leevel\I18n\Console;

use InvalidArgumentException;
use Leevel\Console\Command;
use function Leevel\Filesystem\Fso\list_directory;
use Leevel\I18n\Load;
use Leevel\Kernel\IApp;
use Leevel\Leevel\Bootstrap\LoadI18n;

/**
 * 语言包缓存.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.05.06
 *
 * @version 1.0
 */
class Cache extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'i18n:cache';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Cache i18n to a file.';

    /**
     * IOC 容器.
     *
     * @var \Leevel\Kernel\IApp
     */
    protected $app;

    /**
     * 扩展语言包目录.
     *
     * @var array
     */
    protected $extends;

    /**
     * 响应命令.
     *
     * @param \Leevel\Kernel\IApp $app
     */
    public function handle(IApp $app): void
    {
        $this->app = $app;
        $this->extends = $this->extends();

        $this->line('Start to cache i18n.');

        list_directory($app->i18nPath(), false, function ($item) use ($app) {
            if ($item->isDir()) {
                $i18n = $item->getFilename();

                $data = $this->data($i18n);

                $cachePath = $app->i18nCachedPath($i18n);

                $this->writeCache($cachePath, $data);

                $this->info(sprintf('I18n cache file %s cache successed.', $cachePath));
            }
        });

        $this->info('I18n cache files cache successed.');
    }

    /**
     * 获取语言包扩展.
     *
     * @return array
     */
    protected function extends(): array
    {
        return (new LoadI18n())->getExtend($this->app);
    }

    /**
     * 获取语言数据.
     *
     * @param string $i18n
     *
     * @return array
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
     *
     * @param string $cachePath
     * @param array  $data
     */
    protected function writeCache(string $cachePath, array $data): void
    {
        $dirname = dirname($cachePath);

        if (!is_dir($dirname)) {
            if (is_dir(dirname($dirname)) && !is_writable(dirname($dirname))) {
                $e = sprintf('Unable to create the %s directory.', $dirname);

                throw new InvalidArgumentException($e);
            }

            mkdir($dirname, 0777, true);
        }

        $content = '<?'.'php /* '.date('Y-m-d H:i:s').' */ ?'.'>'.
            PHP_EOL.'<?'.'php return '.var_export($data, true).'; ?'.'>';

        if (!is_writable($dirname) ||
            !file_put_contents($cachePath, $content)) {
            $e = sprintf('Dir %s is not writeable.', $dirname);

            throw new InvalidArgumentException($e);
        }

        chmod($cachePath, 0666 & ~umask());
    }

    /**
     * 命令参数.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [];
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

// @codeCoverageIgnoreStart
if (!function_exists('Leevel\\Filesystem\\Fso\\list_directory')) {
    include dirname(__DIR__, 2).'/Filesystem/Fso/list_directory.php';
}
// @codeCoverageIgnoreEnd
