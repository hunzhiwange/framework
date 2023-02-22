<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\Make;
use Leevel\Database\Manager;
use Leevel\Kernel\IApp;
use Leevel\Support\Str\Camelize;
use Leevel\Support\Str\UnCamelize;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * 生成实体.
 */
class Entity extends Make
{
    /**
     * 命令名字.
     */
    protected string $name = 'make:entity';

    /**
     * 命令描述.
     */
    protected string $description = 'Create a new entity';

    /**
     * 命令帮助.
     */
    protected string $help = <<<'EOF'
        The <info>%command.name%</info> command to make entity with app namespace:

          <info>php %command.full_name% name</info>

        You can also by using the <comment>--namespace</comment> option:

          <info>php %command.full_name% name --namespace=common</info>

        You can also by using the <comment>--table</comment> option:

          <info>php %command.full_name% name --table=test</info>

        You can also by using the <comment>--stub</comment> option:

          <info>php %command.full_name% name --stub=stub/entity</info>

        You can also by using the <comment>--force</comment> option:

          <info>php %command.full_name% name --force</info>

        You can also by using the <comment>--refresh</comment> option:

          <info>php %command.full_name% name --refresh</info>

        You can also by using the <comment>--subdir</comment> option:

          <info>php %command.full_name% name --subdir=foo/bar</info>

        You can also by using the <comment>--connect</comment> option:

        <info>php %command.full_name% name --connect=db_product</info>
        EOF;

    /**
     * 数据库仓储.
     */
    protected Manager $database; /** @phpstan-ignore-line */

    /**
     * 应用.
     */
    protected IApp $app; /** @phpstan-ignore-line */

    /**
     * 刷新临时模板文件.
     */
    protected ?string $tempTemplatePath = null;

    /**
     * 刷新原实体结构数据.
     */
    protected array $oldStructData = [];

    /**
     * 应用的 composer 配置.
     */
    protected ?array $composerOption = null;

    /**
     * 响应命令.
     */
    public function handle(Manager $database, IApp $app): int
    {
        $this->database = $database;
        $this->app = $app;

        // 处理命名空间路径
        $this->parseNamespace();

        // 设置模板路径
        $this->setTemplatePath($this->getStubPath());

        // 保存路径
        $this->setSaveFilePath($this->parseSaveFilePath());

        // 处理强制更新
        $this->handleForce();

        // 处理刷新
        $this->handleRefresh();

        // 设置自定义变量替换
        $this->setCustomReplaceKeyValue($this->getReplace());

        // 设置类型
        $this->setMakeType('entity');

        // 执行
        $this->create();

        // 清理
        $this->clear();

        return 0;
    }

    /**
     * 分析文件保存路径.
     */
    protected function parseSaveFilePath(): string
    {
        return $this->getNamespacePath().'Domain/Entity/'.
            $this->normalizeSubDir($this->getOption('subdir')).
            ucfirst(Camelize::handle((string) $this->getArgument('name'))).'.php';
    }

    /**
     * 执行清理.
     */
    protected function clear(): void
    {
        if ($this->tempTemplatePath && is_file($this->tempTemplatePath)) {
            unlink($this->tempTemplatePath);
        }
    }

    /**
     * 处理强制更新.
     */
    protected function handleForce(): void
    {
        if (true !== $this->getOption('force')) {
            return;
        }

        if (is_file($file = $this->getSaveFilePath())) {
            unlink($file);
        }
    }

    /**
     * 处理刷新.
     */
    protected function handleRefresh(): void
    {
        if (true === $this->getOption('force')) {
            return;
        }

        if (true !== $this->getOption('refresh')) {
            return;
        }

        if (!is_file($file = $this->getSaveFilePath())) {
            return;
        }

        $contentLines = explode(PHP_EOL, file_get_contents($file) ?: '');
        [$startStructIndex, $endStructIndex] = $this->computeStructStartAndEndPosition($contentLines);

        $this->parseOldStructData(
            $contentLines,
            $startStructIndex,
            $endStructIndex,
        );

        $this->setRefreshTemplatePath(
            $contentLines,
            $startStructIndex,
            $endStructIndex,
        );

        unlink($file);
    }

    /**
     * 分析旧的字段结构数据.
     */
    protected function parseOldStructData(array $contentLines, int $middleStructIndex, int $endStructIndex): void
    {
        $oldStructData = [];
        $contentLines = $this->normalizeOldStructData($contentLines, $middleStructIndex, $endStructIndex);
        $regex = '/#\[Struct\(\[[\s\S]+?\]\)\][\s\S]+?protected[\s]*[\S]+?[\s]*\$([\S]+?)[\s]*=[\s]*[\S]+?;/';
        if (preg_match_all($regex, $contentLines, $matches)) {
            foreach ($matches[1] as $i => $v) {
                $oldStructData[UnCamelize::handle($v)] = '    '.trim($matches[0][$i], PHP_EOL).PHP_EOL;
            }
        }

        $this->oldStructData = $oldStructData;
    }

    /**
     * 整理旧的字段结构数据.
     */
    protected function normalizeOldStructData(array $contentLines, int $middleStructIndex, int $endStructIndex): string
    {
        $structLines = \array_slice(
            $contentLines,
            $middleStructIndex,
            $endStructIndex - $middleStructIndex + 2,
        );

        return implode(PHP_EOL, $structLines);
    }

    /**
     * 设置刷新模板.
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    protected function setRefreshTemplatePath(array $contentLines, int $startStructIndex, int $endStructIndex): void
    {
        $contentLines = $this->replaceStuctContentWithTag(
            $contentLines,
            $startStructIndex,
            $endStructIndex,
        );

        $tempTemplatePath = tempnam(sys_get_temp_dir(), 'leevel_entity');
        if (false === $tempTemplatePath) {
            throw new \Exception('Create unique file name failed.');
        }
        $this->tempTemplatePath = $tempTemplatePath;
        file_put_contents($tempTemplatePath, implode(PHP_EOL, $contentLines));
        $this->setTemplatePath($tempTemplatePath);
    }

    /**
     * 替换字段结构内容为标记.
     */
    protected function replaceStuctContentWithTag(array $contentLines, int $startStructIndex, int $endStructIndex): array
    {
        for ($i = $startStructIndex + 1; $i < $endStructIndex + 2; ++$i) {
            unset($contentLines[$i]);
        }

        $contentLines[$startStructIndex] = '{{struct}}';
        ksort($contentLines);

        return $contentLines;
    }

    /**
     * 计算原实体内容中字段所在行起始和结束位置.
     *
     * @throws \Exception
     */
    protected function computeStructStartAndEndPosition(array $contentLines): array
    {
        $startStructIndex = $endStructIndex = 0;
        foreach ($contentLines as $i => $v) {
            $v = trim($v);
            if (!$startStructIndex && str_starts_with($v, '#[Struct([')) {
                $startStructIndex = $i;
            } elseif ('])]' === $v) {
                $endStructIndex = $i;
            }
        }

        if (!$endStructIndex || $startStructIndex > $endStructIndex) {
            $e = 'Can not find start and end position of struct.';

            throw new \Exception($e);
        }

        return [$startStructIndex, $endStructIndex];
    }

    /**
     * 获取实体替换信息.
     */
    protected function getReplace(): array
    {
        $columns = $this->getColumns();

        return [
            'file_name' => ucfirst(Camelize::handle((string) $this->getArgument('name'))),
            'table_name' => $tableName = $this->getTableName(),
            'file_title' => $columns['table_comment'] ?: $tableName,
            'primary_key' => $this->getPrimaryKey($columns),
            'auto_increment' => $this->getAutoIncrement($columns),
            'struct' => $this->getStruct($columns),
            'sub_dir' => $this->normalizeSubDir($this->getOption('subdir'), true),
            'const_extend' => $this->getConstExtend($columns),
        ];
    }

    /**
     * 获取主键信息.
     */
    protected function getPrimaryKey(array $columns): string
    {
        if (!$columns['primary_key']) {
            return 'null';
        }

        if (\count($columns['primary_key']) > 1) {
            return '['.implode(', ', array_map(function ($item) {
                return "'{$item}'";
            }, $columns['primary_key'])).']';
        }

        return "'{$columns['primary_key'][0]}'";
    }

    /**
     * 获取自增信息.
     */
    protected function getAutoIncrement(array $columns): string
    {
        return $columns['auto_increment'] ?
            "'{$columns['auto_increment']}'" : 'null';
    }

    /**
     * 获取结构信息.
     */
    protected function getStruct(array $columns): string
    {
        $showPropBlackColumn = $this->composerOption()['show_prop_black'];
        $struct = [];
        foreach ($columns['list'] as $val) {
            // 刷新操作
            $oldStructData = null;
            if ($this->tempTemplatePath
                && isset($this->oldStructData[$val['field']])) {
                $oldStructData = $this->oldStructData[$val['field']];
                unset($this->oldStructData[$val['field']]);
            }

            $columnInfo = $this->parseColumnExtendData($val);

            $structData = [];
            $structData[] = <<<'EOT'
                    #[Struct([
                EOT;

            if ($val['comment']) {
                $structData[] = <<<EOT
                            self::COLUMN_NAME => '{$val['comment']}',
                    EOT;
            }

            if ($val['primary_key']) {
                $structData[] = <<<'EOT'
                            self::READONLY => true,
                    EOT;
            }

            if (\in_array($val['field'], $showPropBlackColumn, true)) {
                $structData[] = <<<'EOT'
                            self::SHOW_PROP_BLACK => true,
                    EOT;
            }

            if ($columnInfo) {
                $structData[] = <<<EOT
                            self::COLUMN_STRUCT => [
                    {$columnInfo}
                            ],
                    EOT;
            }

            $fieldName = Camelize::handle($val['field']);
            $fieldType = $this->parseColumnType($val['type']);

            $structData[] = <<<EOT
                    ])]
                    protected ?{$fieldType} \${$fieldName} = null;

                EOT;

            $nowStructData = implode(PHP_EOL, $structData);
            if ($oldStructData && trim($oldStructData) !== trim($nowStructData)) {
                $oldStructData = str_replace('protected ?', '// protected ?', $oldStructData);
                $struct[] = $oldStructData;
            }

            $struct[] = $nowStructData;
        }

        // 刷新操作
        if ($this->tempTemplatePath) {
            foreach ($this->oldStructData as $k => $v) {
                $struct[] = $v;
            }
        }

        return trim(implode(PHP_EOL, $struct), PHP_EOL);
    }

    protected function parseColumnType(string $type): string
    {
        return match ($type) {
            'int', 'tinyint', 'smallint', 'mediumint', 'bigint', 'boolean' => 'int',
            'float', 'double' => 'float',
            default => 'string',
        };
    }

    /**
     * 获取 const 扩展信息.
     */
    protected function getConstExtend(array $columns): string
    {
        $deleteAtColumn = $this->composerOption()['delete_at'];
        if (!$deleteAtColumn || !isset($columns['list'][$deleteAtColumn])) {
            return '';
        }

        return <<<'EOT'


                /**
                 * Soft delete column.
                 */
                public const DELETE_AT = 'delete_at';
            EOT;
    }

    /**
     * 取得应用的 composer 配置.
     */
    protected function composerOption(): array
    {
        if (null !== $this->composerOption) {
            return $this->composerOption;
        }

        $path = $this->app->path().'/composer.json';
        if (!is_file($path)) {
            return $this->composerOption = [
                'show_prop_black' => [],
                'delete_at' => null,
            ];
        }

        $option = $this->getFileContent($path);
        $option = $option['extra']['leevel-console']['database-entity'];

        return $this->composerOption = [
            'show_prop_black' => $option['show_prop_black'] ?? [],
            'delete_at' => $option['delete_at'] ?? null,
        ];
    }

    /**
     * 获取配置信息.
     */
    protected function getFileContent(string $path): array
    {
        return (array) json_decode(file_get_contents($path) ?: '', true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * 计算字段名最大长度.
     */
    protected function computeMaxColumnLength(array $columns): int
    {
        $maxColumnLength = 0;
        foreach ($columns as $v) {
            if (($fieldLength = \strlen($v['field'])) > $maxColumnLength) {
                $maxColumnLength = $fieldLength;
            }
        }

        return $maxColumnLength;
    }

    /**
     * 分析字段附加信息数据.
     */
    protected function parseColumnExtendData(array $columns): string
    {
        $result = [];
        $i = 0;
        foreach ($this->normalizeColumnItem($columns) as $k => $v) {
            switch (true) {
                case true === $v:
                    $v = 'true';

                    break;

                case false === $v:
                    $v = 'false';

                    break;

                case null === $v:
                    $v = 'null';

                    break;

                case \is_string($v):
                    $v = "'".trim($v)."'";

                    break;
            }

            $item = "            '{$k}' => {$v},";
            ++$i;
            $result[] = $item;
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * 整理数据库字段.
     *
     * - 删除掉一些必须的字段，以及调整一些展示优先级
     */
    protected function normalizeColumnItem(array $column): array
    {
        if (null !== $column['default']) {
            $column['default'] = match ($this->parseColumnType($column['type'])) {
                'int' => (int) $column['default'],
                'float' => (float) $column['default'],
                'string' => (string) $column['default'],
                default => $column['default'],
            };
        }

        $data = [
            'type' => $column['type'],
            'default' => $column['default'],
        ];

        if ($column['type_length']) {
            $data['length'] = $column['type_length'];
        }

        return $data;
    }

    /**
     * 整理字段注释.
     */
    protected function normalizeColumnComment(string $comment): string
    {
        if (!$comment) {
            return '';
        }

        return str_replace(PHP_EOL, ' '.PHP_EOL.' ', $comment);
    }

    /**
     * 获取数据库表名字.
     */
    protected function getTableName(): string
    {
        if ($this->getOption('table')) {
            return (string) $this->getOption('table');
        }

        return UnCamelize::handle((string) $this->getArgument('name'));
    }

    /**
     * 获取模板路径.
     *
     * @throws \InvalidArgumentException
     */
    protected function getStubPath(): string
    {
        if ($this->getOption('stub')) {
            $stub = (string) $this->getOption('stub');
        } else {
            $stub = __DIR__.'/stub/entity';
        }

        if (!is_file($stub)) {
            $e = sprintf('Entity stub file `%s` was not found.', $stub);

            throw new \InvalidArgumentException($e);
        }

        return $stub;
    }

    /**
     * 获取数据库表字段信息.
     *
     * @throws \Exception
     */
    protected function getColumns(): array
    {
        $connect = $this->getOption('connect') ?: null;
        $result = $this->database
            ->connect($connect)
            ->getTableColumns($tableName = $this->getTableName(), true)
        ;
        if (empty($result['list'])) {
            $e = sprintf('Table (%s) is not found or has no columns.', $tableName);

            throw new \Exception($e);
        }

        return $result;
    }

    /**
     * 命令参数.
     */
    protected function getArguments(): array
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'This is the entity name.',
            ],
        ];
    }

    /**
     * 命令配置.
     */
    protected function getOptions(): array
    {
        return [
            [
                'namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'Apps namespace registered to system,default namespace is these (App,Admin)',
                'App',
            ],
            [
                'table',
                null,
                InputOption::VALUE_OPTIONAL,
                'The database table of entity',
            ],
            [
                'stub',
                null,
                InputOption::VALUE_OPTIONAL,
                'Custom stub of entity',
            ],
            [
                'subdir',
                null,
                InputOption::VALUE_OPTIONAL,
                'Subdir of entity',
            ],
            [
                'refresh',
                'r',
                InputOption::VALUE_NONE,
                'Refresh entity struct',
            ],
            [
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force update entity',
            ],
            [
                'connect',
                null,
                InputOption::VALUE_OPTIONAL,
                'The database connect of entity',
            ],
        ];
    }
}
