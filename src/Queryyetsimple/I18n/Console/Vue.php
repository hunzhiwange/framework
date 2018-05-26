<?php declare(strict_types=1);
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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Leevel\I18n\Console;

use RuntimeException;
use Leevel\{
    I18n\Mo,
    Filesystem\Fso,
    Console\Option,
    Console\Command,
    Console\Argument
};

/**
 * Vue mo 语言包转 export module
 * 通过本工具可以使用 GNU Gettext 语言包解决方法来实现国际化
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.11.27
 * @version 1.0
 */
class Vue extends Command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $name = 'i18n:vue';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = 'Make vue i18n export module file with source mo file';

    /**
     * 命令帮助
     *
     * @var string
     */
    protected $help = <<<EOF
The <info>%command.name%</info> command to make i18n export module file with source mo file:

  <info>php %command.full_name% zh-CN(en-Us|zh-TW|all)</info>

You can also by using the <comment>--source</comment>,<comment>--output</comment> and <comment>--file</comment> option:

  <info>php %command.full_name% lang --source=frontend/src/i18n --output=frontend/src/i18n --file=index.js</info>
EOF;

    /**
     * 源文件目录
     *
     * @var string
     */
    protected $sourceDir;

    /**
     * 输出目录
     *
     * @var string
     */
    protected $outputDir;

    /**
     * 输出文件名
     *
     * @var string
     */
    protected $outputFile;

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->paresLang() as $lang) {
            $this->makeI18nFile($lang);
        }
    }

    /**
     * 分析待缓存的语言包
     *
     * @return array
     */
    protected function paresLang()
    {
        if ($this->argument('lang') == 'all') {
            return Fso::lists($this->parseSourceDir(), 'dir');
        } else {
            return [
                $this->argument('lang')
            ];
        }
    }

    /**
     * 生成缓存文件
     *
     * @param string $lang
     * @return void
     */
    protected function makeI18nFile($lang)
    {
        $sourceDir = $this->parseSourceDir() . '/' . $lang;
        $outputDir = $this->parseOutputDir() . '/' . $lang;
        $outputFile = $this->parseOutputFile();

        $moFiles = $this->findMoFile([
            $sourceDir
        ]);

        $result = $this->parseMoData($moFiles);
        if (empty($result)) {
            $result['Query Yet Simple'] = '左右代码 右手年华 不忘初心 简单快乐';
        }

        if (! file_put_contents($outputDir . '/' . $outputFile, '/** ' . date('Y-m-d H:i:s') . ' */' . 
            PHP_EOL . 'export default ' . json_encode($result, JSON_UNESCAPED_UNICODE) . ';')) {
            throw new RuntimeException(sprintf('Dir %s do not have permission.', $outputDir));
        }

        chmod($outputDir, 0777);

        $this->info(sprintf('Vue lang file %s created successfully.', $lang));
        $this->comment($outputDir . '/' . $outputFile);
    }

    /**
     * 分析默认语言源目录
     *
     * @return string
     */
    protected function parseSourceDir()
    {
        if (! is_null($this->sourceDir)) {
            return $this->sourceDir;
        }

        $sourceDir = $this->option('source');
        if (empty($sourceDir)) {
            throw new RuntimeException('Source dir is not set');
        }

        return $this->sourceDir = $sourceDir;
    }

    /**
     * 分析输出语言目录
     *
     * @return string
     */
    protected function parseOutputDir()
    {
        if (! is_null($this->outputDir)) {
            return $this->outputDir;
        }

        $outputDir = $this->option('output');
        if (empty($outputDir)) {
            $outputDir = $this->parseSourceDir();
        }

        return $this->outputDir = $outputDir;
    }

    /**
     * 分析输出文件名
     *
     * @return string
     */
    protected function parseOutputFile()
    {
        if (! is_null($this->outputFile)) {
            return $this->outputFile;
        }

        $outputFile = $this->option('file');
        if (empty($outputFile)) {
            throw new RuntimeException('Output file is not set');
        }

        return $this->outputFile = $outputFile;
    }

    /**
     * 分析 mo 文件语言包数据
     *
     * @param array $arrFile 文件地址
     * @author 小牛
     * @since 2016.11.25
     * @return array
     */
    protected function parseMoData(array $arrFile)
    {
        return (new mo())->readToArray($arrFile);
    }

    /**
     * 分析目录中的 PHP 语言包包含的文件
     *
     * @param array $arrDir 文件地址
     * @author 小牛
     * @since 2016.11.27
     * @return array
     */
    protected function findMoFile(array $arrDir)
    {
        $arrFiles = [];
        foreach ($arrDir as $sDir) {
            if (! is_dir($sDir)) {
                continue;
            }

            $arrFiles = array_merge($arrFiles, Fso::lists($sDir, 'file', true, [], [
                'mo'
            ]));
        }

        return $arrFiles;
    }

    /**
     * 命令参数
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'lang',
                Argument::OPTIONAL,
                'This is the lang name like zh-cn, you also can set it all.',
                'all'
            ]
        ];
    }

    /**
     * 命令配置
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'source',
                'frontend/src/i18n',
                option::VALUE_OPTIONAL,
                'Source i18n dir can be set here',
                'frontend/src/i18n'
            ],
            [
                'output',
                null,
                option::VALUE_OPTIONAL,
                'Output i18n dir can be set here,default value is the same as source'
            ],
            [
                'file',
                'index.js',
                option::VALUE_OPTIONAL,
                'Output default file name',
                'index.js'
            ]
        ];
    }
}
