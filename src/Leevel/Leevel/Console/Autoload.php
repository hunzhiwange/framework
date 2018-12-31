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

use InvalidArgumentException;
use Leevel\Console\Command;
use Leevel\Console\Option;
use Leevel\Kernel\IProject;

/**
 * 优化 composer 自动加载.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.01
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Autoload extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'autoload';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Optimize base on composer dump-autoload -o.';

    /**
     * IOC 容器.
     *
     * @var \Leevel\Kernel\IProject
     */
    protected $project;

    /**
     * 响应命令.
     *
     * @param \Leevel\Kernel\IProject $project
     */
    public function handle(IProject $project)
    {
        $this->project = $project;

        $this->line('Start to cache autoload.');

        if (false === $this->ignore()) {
            $this->line('Exec composer dump-autoload -o.');
            exec($this->normalizeComposerCommand());
        }

        $data = $this->data();

        $cachePath = $this->autoloadCachedPath();

        $this->writeCache($cachePath, $data);

        $this->info(sprintf('Autoload file %s cache successed.', $cachePath));
    }

    /**
     * 取得 composer 优化命令.
     *
     * @return string
     */
    protected function normalizeComposerCommand(): string
    {
        return sprintf(
            '%s dump-autoload -o',
            escapeshellarg($this->composer())
        );
    }

    /**
     * 取得缓存路径.
     */
    protected function autoloadCachedPath(): string
    {
        return $this->project->runtimePath('autoload.php');
    }

    /**
     * 获取缓存数据.
     *
     * @return string
     */
    protected function data(): string
    {
        $data = [];

        $data[] = file_get_contents($this->project->path('vendor/composer/ClassLoader.php'));
        $data[] = $this->dataInit();
        $data[] = $this->dataHelper();
        $data[] = $this->dataLoader();

        unlink($this->project->path('vendor/composer/ComposerStaticInit.php'));

        return implode(PHP_EOL.PHP_EOL, $data).PHP_EOL;
    }

    /**
     * 取得初始化数据.
     *
     * @return string
     */
    protected function dataInit(): string
    {
        list($lengths, $prefixs) = $this->prefixesPsr4();

        return 'class ComposerStaticInit
{
    public static $files = '.var_export($this->files(), true).';

    public static $prefixLengthsPsr4 = '.var_export($lengths, true).';

    public static $prefixDirsPsr4 = '.var_export($prefixs, true).';

    public static $prefixesPsr0 = [];

    public static $classMap = '.var_export($this->classMap(), true).';

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit::$classMap;

        }, null, ClassLoader::class);
    }
}';
    }

    /**
     * 取得助手文件数据.
     *
     * @return string
     */
    protected function dataHelper(): string
    {
        return <<<'eot'
foreach (ComposerStaticInit::$files as $fileIdentifier => $_) {
    $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;
}

require_once dirname(__FILE__, 2).'/vendor/hunzhiwange/framework/src/Leevel/Leevel/functions.php';
require_once dirname(__FILE__, 2).'/common/Infra/functions.php';
eot;
    }

    /**
     * 取得载入数据.
     *
     * @return string
     */
    protected function dataLoader(): string
    {
        return <<<'eot'
if (!isset($GLOBALS['__composer_leevel'])) {
    $GLOBALS['__composer_autoload_leevel'] = new ClassLoader();
}

$loader =  $GLOBALS['__composer_autoload_leevel'];

call_user_func(ComposerStaticInit::getInitializer($loader));

$loader->register(true);

spl_autoload_register(function (string $className) use(&$loader) {
    static $loaded;

    if (null === $loaded) {
        $loader = require_once __DIR__.'/../vendor/autoload.php';
        $loaded = true;
    }

    $loader->loadClass($className);
 });

return $loader;
eot;
    }

    /**
     * 获取 psr4 前缀.
     *
     * @return array
     */
    protected function prefixesPsr4(): array
    {
        $lengths = $prefixs = [];

        $composerStaticClass = $this->composerStaticClass();
        $prefixesPsr4 = $composerStaticClass::$prefixDirsPsr4;

        foreach ($this->optimizeNamespaces() as $prefix) {
            $first = $prefix[0];
            $prefix .= '\\';

            $lengths[$first][$prefix] = strlen($prefix);

            if (isset($prefixesPsr4[$prefix])) {
                $prefixs[$prefix] = $prefixesPsr4[$prefix];
            }
        }

        return [$lengths, $prefixs];
    }

    /**
     * 获取类映射.
     *
     * @return array
     */
    protected function classMap(): array
    {
        $result = [];

        $optimizeNamespaces = $this->optimizeNamespaces();
        $composerStaticClass = $this->composerStaticClass();
        $classMap = $composerStaticClass::$classMap;

        foreach ($classMap as $key => $value) {
            list($namespace) = explode('\\', $key);

            if (in_array($namespace, $optimizeNamespaces, true)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * 获取载入助手函数.
     *
     * @return array
     */
    protected function files(): array
    {
        $optimizeNamespaces = $this->optimizeNamespaces();
        $composerStaticClass = $this->composerStaticClass();

        return $composerStaticClass::$files;
    }

    /**
     * 取得 composer 静态类.
     *
     * @return string
     */
    protected function composerStaticClass(): string
    {
        static $staticClass;

        if ($staticClass) {
            return $staticClass;
        }

        $content = file_get_contents($this->project->path('vendor/autoload.php'));

        if (preg_match('/ComposerAutoloaderInit(\S{32})::getLoader/', $content, $matches)) {
            $staticContent = file_get_contents(
                $this->project->path('vendor/composer/autoload_static.php')
            );

            file_put_contents(
                $staticPath = $this->project->path('vendor/composer/ComposerStaticInit.php'),
                str_replace('ComposerStaticInit'.$matches[1], 'ComposerStaticInit', $staticContent)
            );

            include $staticPath;

            return $staticClass = '\\Composer\\Autoload\\ComposerStaticInit';
        }

        throw new InvalidArgumentException('Composer autoload is invalid.');
    }

    /**
     * 取得优化的命名空间.
     *
     * @return array
     */
    protected function optimizeNamespaces(): array
    {
        static $result;

        if ($result) {
            return $result;
        }

        return $result = $this->appNamespaces();
    }

    /**
     * 取得应用的 composer 配置.
     *
     * @return array
     */
    protected function appNamespaces(): array
    {
        $path = $this->project->path().'/composer.json';

        if (!is_file($path)) {
            return [];
        }

        $options = $this->getFileContent($path);

        $appNamespaces = !empty($options['autoload']['psr-4']) ?
            array_map(function (string $value) {
                return rtrim($value, '\\');
            }, array_keys($options['autoload']['psr-4'])) : [];

        $extraNamespaces = !empty($options['extra']['leevel-console']['autoload']['namespaces']) ?
            $options['extra']['leevel-console']['autoload']['namespaces'] : [];

        return array_merge($appNamespaces, $extraNamespaces);
    }

    /**
     * 获取配置信息.
     *
     * @param string $path
     *
     * @return array
     */
    protected function getFileContent(string $path): array
    {
        return json_decode(file_get_contents($path), true);
    }

    /**
     * 写入缓存.
     *
     * @param string $cachePath
     * @param array  $data
     */
    protected function writeCache(string $cachePath, string $data)
    {
        $dirname = dirname($cachePath);

        if (!is_dir($dirname)) {
            if (is_dir(dirname($dirname)) && !is_writable(dirname($dirname))) {
                throw new InvalidArgumentException(
                    sprintf('Unable to create the %s directory.', $dirname)
                );
            }

            mkdir($dirname, 0777, true);
        }

        if (!is_writable($dirname) ||
            !file_put_contents($cachePath, $data)) {
            throw new InvalidArgumentException(
                sprintf('Dir %s is not writeable.', $dirname)
            );
        }

        chmod($cachePath, 0666 & ~umask());
    }

    /**
     * 取得 Composer 路径.
     *
     * @return string
     */
    protected function composer(): string
    {
        return $this->option('composer');
    }

    /**
     * 取得忽略 composer 自身命令.
     *
     * @return bool
     */
    protected function ignore(): bool
    {
        return $this->option('ignore') ? true : false;
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
        return [
            [
                'composer',
                null,
                Option::VALUE_OPTIONAL,
                'Where is composer.',
                'composer',
            ],
            [
                'ignore',
                null,
                Option::VALUE_OPTIONAL,
                'Ignore composer itself.',
                null,
            ],
        ];
    }
}
