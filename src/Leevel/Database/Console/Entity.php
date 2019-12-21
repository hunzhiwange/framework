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

namespace Leevel\Database\Console;

use Exception;
use InvalidArgumentException;
use Leevel\Console\Argument;
use Leevel\Console\Make;
use Leevel\Console\Option;
use Leevel\Database\Manager;
use function Leevel\Support\Str\camelize;
use Leevel\Support\Str\camelize;
use function Leevel\Support\Str\ends_with;
use Leevel\Support\Str\ends_with;
use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;

/**
 * 生成模型实体.
 *
 * @codeCoverageIgnore
 */
class Entity extends Make
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'make:entity';

    /**
     * 命令描述.
     *
     * @var string
     */
    protected string $description = 'Create a new entity';

    /**
     * 命令帮助.
     *
     * @var string
     */
    protected string $help = <<<'EOF'
        The <info>%command.name%</info> command to make entity with app namespace:
        
          <info>php %command.full_name% name</info>
        
        You can also by using the <comment>--namespace</comment> option:
        
          <info>php %command.full_name% name --namespace=common</info>
        
        You can also by using the <comment>--table</comment> option:
        
          <info>php %command.full_name% name --table=test</info>

        You can also by using the <comment>--stub</comment> option:
        
          <info>php %command.full_name% name --stub=/stub/entity</info>

        You can also by using the <comment>--prop</comment> option:
        
          <info>php %command.full_name% name --prop</info>

        You can also by using the <comment>--force</comment> option:
        
          <info>php %command.full_name% name --force</info>

        You can also by using the <comment>--refresh</comment> option:
        
          <info>php %command.full_name% name --refresh</info>

        You can also by using the <comment>--subdir</comment> option:
        
          <info>php %command.full_name% name --subdir=foo/bar</info>
        EOF;

    /**
     * 数据库仓储.
     *
     * @var \Leevel\Database\Manager
     */
    protected Manager $database;

    /**
     * 刷新临时模板文件.
     *
     * @var string
     */
    protected ?string $tempTemplatePath = null;

    /**
     * 刷新原实体结构数据.
     *
     * @var array
     */
    protected array $oldStructData = [];

    /**
     * 响应命令.
     */
    public function handle(Manager $database): void
    {
        $this->database = $database;

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
    }

    /**
     * 分析文件保存路径.
     */
    protected function parseSaveFilePath(): string
    {
        return $this->getNamespacePath().'Domain/Entity/'.
            ($this->option('subdir') ? $this->normalizeSubdir($this->option('subdir')) : '').
            ucfirst(camelize($this->argument('name'))).'.php';
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
        if (true !== $this->option('force')) {
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
        if (true === $this->option('force')) {
            return;
        }

        if (true !== $this->option('refresh')) {
            return;
        }

        if (!is_file($file = $this->getSaveFilePath())) {
            return;
        }

        $contentLines = explode(PHP_EOL, file_get_contents($file));
        list(
            $startCommentIndex,
            $endCommentIndex,
            $startStructIndex,
            $endStructIndex,
            $startPropIndex,
            $endPropIndex) = $this->computeStructStartAndEndPosition($contentLines);

        $this->parseOldStructData(
            $contentLines,
            $startStructIndex,
            $endStructIndex,
        );

        $this->setRefreshTemplatePath(
            $contentLines,
            $startCommentIndex,
            $endCommentIndex,
            $startStructIndex,
            $endStructIndex,
            $startPropIndex,
            $endPropIndex,
        );

        unlink($file);
    }

    /**
     * 分析旧的字段结构数据.
     */
    protected function parseOldStructData(array $contentLines, int $startStructIndex, int $endStructIndex): void
    {
        $structLines = array_slice($contentLines, $startStructIndex + 1, $endStructIndex - $startStructIndex - 1);

        $oldStructData = [];
        $structRegex = '/        \'([\s\S]*?)\' => \[[\s\S]*?        \],/';
        preg_match_all($structRegex, implode(PHP_EOL, $structLines), $mat);
        if ($mat) {
            foreach ($mat[1] as $i => $v) {
                $oldStructData[$v] = $mat[0][$i];
            }
        }

        $this->oldStructData = $oldStructData;
    }

    /**
     * 设置刷新模板.
     */
    protected function setRefreshTemplatePath(array $contentLines, int $startCommentIndex, int $endCommentIndex, int $startStructIndex, int $endStructIndex, int $startPropIndex, int $endPropIndex): void
    {
        $contentLines = $this->replaceStuctContentWithTag(
            $contentLines,
            $startCommentIndex,
            $endCommentIndex,
            $startStructIndex,
            $endStructIndex,
            $startPropIndex,
            $endPropIndex,
        );

        $this->tempTemplatePath = $tempTemplatePath = tempnam(sys_get_temp_dir(), 'leevel_entity');
        file_put_contents($tempTemplatePath, implode(PHP_EOL, $contentLines));
        $this->setTemplatePath($tempTemplatePath);
    }

    /**
     * 替换字段结构内容为标记.
     */
    protected function replaceStuctContentWithTag(array $contentLines, int $startCommentIndex, int $endCommentIndex, int $startStructIndex, int $endStructIndex, int $startPropIndex, int $endPropIndex): array
    {
        for ($i = $startCommentIndex + 2; $i < $endCommentIndex - 1; $i++) {
            unset($contentLines[$i]);
        }

        for ($i = $startStructIndex + 1; $i < $endStructIndex; $i++) {
            unset($contentLines[$i]);
        }

        if ($startPropIndex) {
            for ($i = $startPropIndex - 3; $i < $endPropIndex + 1; $i++) {
                unset($contentLines[$i]);
            }
        }

        $contentLines[$startCommentIndex + 2] = '{{struct_comment}}';
        $contentLines[$startStructIndex + 1] = '{{struct}}';
        if ($startPropIndex) {
            $contentLines[$startPropIndex] = '{{props}}';
        }
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
        $startCommentIndex = $endCommentIndex =
            $startStructIndex = $endStructIndex =
            $startPropIndex = $endPropIndex = 0;

        foreach ($contentLines as $i => $v) {
            $v = trim($v);

            if (!$startCommentIndex && ends_with($v, '* Entity struct.')) {
                $startCommentIndex = $i;
            } elseif (!$endCommentIndex && ends_with($v, '* @var array')) {
                $endCommentIndex = $i;
            }

            if (!$startStructIndex && 0 === strpos($v, 'const STRUCT')) {
                $startStructIndex = $i;
            } elseif (!$endStructIndex && '];' === $v) {
                $endStructIndex = $i;
            }

            if (!$startPropIndex && 0 === strpos($v, 'private $_')) {
                $startPropIndex = $i;
            }
            if (0 === strpos($v, 'private $_')) {
                $endPropIndex = $i;
            }
        }

        if (!$endStructIndex || $endCommentIndex > $startStructIndex) {
            $e = 'Can not find start and end position of struct.';

            throw new Exception($e);
        }

        return [
            $startCommentIndex, $endCommentIndex,
            $startStructIndex, $endStructIndex,
            $startPropIndex, $endPropIndex,
        ];
    }

    /**
     * 获取实体替换信息.
     */
    protected function getReplace(): array
    {
        $columns = $this->getColumns();

        return [
            'file_name'           => ucfirst(camelize($this->argument('name'))),
            'table_name'          => $this->getTableName(),
            'primary_key'         => $this->getPrimaryKey($columns),
            'primary_key_type'    => $this->getPrimaryKeyType($columns),
            'auto_increment'      => $this->getAutoIncrement($columns),
            'auto_increment_type' => $this->getAutoIncrementType($columns),
            'struct'              => $this->getStruct($columns),
            'struct_comment'      => $this->getStructComment($columns),
            'props'               => $this->getProps($columns),
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

        if (count($columns['primary_key']) > 1) {
            return '['.implode(', ', array_map(function ($item) {
                return "'{$item}'";
            }, $columns['primary_key'])).']';
        }

        return  "'{$columns['primary_key'][0]}'";
    }

    /**
     * 获取主键类型信息.
     */
    protected function getPrimaryKeyType(array $columns): string
    {
        if (!$columns['primary_key']) {
            return 'null';
        }

        if (count($columns['primary_key']) > 1) {
            return 'array';
        }

        return 'string';
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
     * 获取自增类型信息.
     */
    protected function getAutoIncrementType(array $columns): string
    {
        if (!$columns['auto_increment']) {
            return 'null';
        }

        return 'string';
    }

    /**
     * 获取结构信息.
     */
    protected function getStruct(array $columns): string
    {
        $struct = [];
        foreach ($columns['list'] as $val) {
            if ($this->tempTemplatePath &&
                isset($this->oldStructData[$val['field']])) {
                $struct[] = $this->oldStructData[$val['field']];

                continue;
            }

            if ($val['primary_key']) {
                $struct[] = <<<EOT
                            '{$val['field']}' => [
                                self::READONLY => true,
                            ],
                    EOT;
            } else {
                $struct[] = <<<EOT
                            '{$val['field']}' => [
                            ],
                    EOT;
            }
        }

        return implode(PHP_EOL, $struct);
    }

    /**
     * 获取结构注释信息.
     */
    protected function getStructComment(array $columns): string
    {
        $struct = [];
        $maxColumnLength = $this->computeMaxColumnLength($columns['list']);
        foreach ($columns['list'] as $val) {
            $column = $this->parseColumnExtendData($val, $maxColumnLength);
            $struct[] = <<<EOT
                     * - {$val['field']}
                {$column}
                EOT;
        }

        return implode(PHP_EOL, $struct);
    }

    /**
     * 计算字段名最大长度.
     */
    protected function computeMaxColumnLength(array $columns): int
    {
        $maxColumnLength = 0;
        foreach ($columns as $v) {
            if (($fieldLength = strlen($v['field'])) > $maxColumnLength) {
                $maxColumnLength = $fieldLength;
            }
        }

        return $maxColumnLength;
    }

    /**
     * 分析字段附加信息数据.
     */
    protected function parseColumnExtendData(array $columns, int $maxColumnLength): string
    {
        $result = [];
        $columns = $this->normalizeColumnItem($columns);
        $columns = $this->normalizeColumnsToPieces($columns);
        foreach ($columns as $i => $v) {
            $v = trim($v);
            if (!preg_match('/\'([\s\S]*?)\' => ([\s\S]*?),/', $v, $mat)) {
                continue;
            }

            $item = $mat[1].': '.$mat[2];
            if (0 === $i % 3) {
                $item = (0 !== $i ? PHP_EOL : '').'     *   '.
                    str_repeat(' ', $maxColumnLength + 2).$item;
            }
            $result[] = $item;
        }

        return implode(', ', $result);
    }

    /**
     * 整理字段为小块.
     */
    protected function normalizeColumnsToPieces(array $columns): array
    {
        $columns = explode(PHP_EOL, var_export($columns, true));
        array_pop($columns);
        array_shift($columns);

        return $columns;
    }

    /**
     * 整理数据库字段.
     *
     * - 删除掉一些必须的字段，以及调整一些展示优先级
     */
    protected function normalizeColumnItem(array $column): array
    {
        $priorityColumn = [
            'comment' => $this->normalizeColumnComment($column['comment']),
        ];
        $unimportantField = [
            'type_name', 'type_length', 'comment', 'collation',
            'field', 'primary_key', 'auto_increment',
        ];

        foreach ($unimportantField as $v) {
            unset($column[$v]);
        }

        return array_merge($priorityColumn, $column);
    }

    /**
     * 整理字段注释.
     */
    protected function normalizeColumnComment(string $comment): string
    {
        if (!$comment) {
            return '';
        }

        return str_replace(PHP_EOL, ' \n ', $comment);
    }

    /**
     * 获取属性信息.
     */
    protected function getProps(array $columns): string
    {
        $props = [];
        foreach ($columns['list'] as $val) {
            $comment = $val['comment'] ?
                $this->normalizeColumnComment($val['comment']) :
                $val['field'];
            $propName = camelize($val['field']);
            $tmpProp = <<<EOT
                    /**
                     * {$comment}.
                     */
                    private \$_{$propName};
                EOT;
            $props[] = $tmpProp;
        }

        return implode(PHP_EOL.PHP_EOL, $props);
    }

    /**
     * 获取数据库表名字.
     */
    protected function getTableName(): string
    {
        if ($this->option('table')) {
            return $this->option('table');
        }

        return un_camelize($this->argument('name'));
    }

    /**
     * 获取模板路径.
     *
     * @throws \InvalidArgumentException
     */
    protected function getStubPath(): string
    {
        if ($this->option('stub')) {
            $stub = $this->option('stub');
        } else {
            $stub = __DIR__.'/stub/entity'.
                (true === $this->option('prop') ? '_prop' : '');
        }

        if (!is_file($stub)) {
            $e = sprintf('Entity stub file `%s` was not found.', $stub);

            throw new InvalidArgumentException($e);
        }

        return $stub;
    }

    /**
     * 获取数据库表字段信息.
     */
    protected function getColumns(): array
    {
        return $this->database->tableColumns($this->getTableName(), true);
    }

    /**
     * 命令参数.
     */
    protected function getArguments(): array
    {
        return [
            [
                'name',
                Argument::REQUIRED,
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
                Option::VALUE_OPTIONAL,
                'Apps namespace registered to system,default namespace is these (Common,App,Admin)',
                'app',
            ],
            [
                'table',
                null,
                Option::VALUE_OPTIONAL,
                'The database table of entity',
            ],
            [
                'stub',
                null,
                Option::VALUE_OPTIONAL,
                'Custom stub of entity',
            ],
            [
                'prop',
                'p',
                Option::VALUE_NONE,
                'With prop stub',
            ],
            [
                'subdir',
                null,
                Option::VALUE_OPTIONAL,
                'Subdir of entity',
            ],
            [
                'refresh',
                'r',
                Option::VALUE_NONE,
                'Refresh entity struct',
            ],
            [
                'force',
                'f',
                Option::VALUE_NONE,
                'Force update entity',
            ],
        ];
    }
}

// import fn.
class_exists(un_camelize::class); // @codeCoverageIgnore
class_exists(camelize::class); // @codeCoverageIgnore
class_exists(ends_with::class); // @codeCoverageIgnore
