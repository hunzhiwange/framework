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

namespace Leevel\Database\Console;

use Leevel\Console\Argument;
use Leevel\Console\Make;
use Leevel\Console\Option;
use Leevel\Database\Manager;
use function Leevel\Support\Str\camelize;
use function Leevel\Support\Str\un_camelize;

/**
 * 生成模型实体.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.02
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Entity extends Make
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'make:entity';

    /**
     * 命令描述.
     *
     * @var string
     */
    protected $description = 'Create a new entity.';

    /**
     * 命令帮助.
     *
     * @var string
     */
    protected $help = <<<'EOF'
        The <info>%command.name%</info> command to make entity with app namespace:
        
          <info>php %command.full_name% name</info>
        
        You can also by using the <comment>--namespace</comment> option:
        
          <info>php %command.full_name% name --namespace=common</info>
        
        You can also by using the <comment>--table</comment> option:
        
          <info>php %command.full_name% name --table=test</info>
        EOF;

    /**
     * 数据库仓储.
     *
     * @var \Leevel\Database\Manager
     */
    protected $database;

    /**
     * 响应命令.
     *
     * @param \Leevel\Database\Manager $database
     */
    public function handle(Manager $database): void
    {
        $this->database = $database;

        // 处理命名空间路径
        $this->parseNamespace();

        // 设置模板路径
        $this->setTemplatePath(__DIR__.'/stub/entity');

        // 保存路径
        $this->setSaveFilePath(
            $this->getNamespacePath().
            'Domain/Entity/'.
            ucfirst(camelize($this->argument('name'))).'.php'
        );

        $this->setCustomReplaceKeyValue($this->getReplace());

        // 设置类型
        $this->setMakeType('entity');

        // 执行
        $this->create();
    }

    /**
     * 获取实体替换信息.
     *
     * @return array
     */
    protected function getReplace(): array
    {
        $columns = $this->getColumns();

        return [
            'file_name'        => ucfirst(camelize($this->argument('name'))),
            'table_name'       => $this->getTableName(),
            'primary_key'      => $this->getPrimaryKey($columns),
            'primary_key_type' => $this->getPrimaryKeyType($columns),
            'auto_increment'   => $this->getAutoIncrement($columns),
            'struct'           => $this->getStruct($columns),
            'props'            => $this->getProps($columns),
        ];
    }

    /**
     * 获取主键信息.
     *
     * @param array $columns
     *
     * @return string
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
     *
     * @param array $columns
     *
     * @return string
     */
    protected function getPrimaryKeyType(array $columns): string
    {
        if (!$columns['primary_key']) {
            return 'string';
        }

        if (count($columns['primary_key']) > 1) {
            return 'array';
        }

        return 'string';
    }

    /**
     * 获取自增信息.
     *
     * @param array $columns
     *
     * @return string
     */
    protected function getAutoIncrement(array $columns): string
    {
        return $columns['auto_increment'] ? "'{$columns['auto_increment']}'" : 'null';
    }

    /**
     * 获取结构信息.
     *
     * @param array $columns
     *
     * @return string
     */
    protected function getStruct(array $columns): string
    {
        $struct = ['['];

        foreach ($columns['list'] as $val) {
            if ($val['primary_key']) {
                $struct[] = "        '{$val['name']}' => [
            'readonly' => true,
        ],";
            } else {
                $struct[] = "        '{$val['name']}' => [],";
            }
        }

        $struct[] = '    ]';

        return implode(PHP_EOL, $struct);
    }

    /**
     * 获取属性信息.
     *
     * @param array $columns
     *
     * @return string
     */
    protected function getProps(array $columns): string
    {
        $props = [];

        foreach ($columns['list'] as $val) {
            $comment = $val['comment'] ?: $val['name'];

            $propName = camelize($val['name']);

            $type = in_array($val['type'], ['tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint'], true) ?
                'int' : 'string';

            $tmpProp = "
    /**
     * {$comment}.
     *
     * @var {$type}
     */
    private \${$propName};
";

            $props[] = $tmpProp;
        }

        return implode('', $props);
    }

    /**
     * 获取数据库表名字.
     *
     * @return string
     */
    protected function getTableName(): string
    {
        if ($this->option('table')) {
            return $this->option('table');
        }

        return un_camelize($this->argument('name'));
    }

    /**
     * 获取数据库表字段信息.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return $this->database->tableColumns($this->getTableName(), true);
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
                'name',
                Argument::REQUIRED,
                'This is the entity name.',
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
        ];
    }
}

// @codeCoverageIgnoreStart
if (!function_exists('Leevel\\Support\\Str\\un_camelize')) {
    include dirname(__DIR__, 2).'/Support/Str/un_camelize.php';
}

if (!function_exists('Leevel\\Support\\Str\\camelize')) {
    include dirname(__DIR__, 2).'/Support/Str/camelize.php';
}
// @codeCoverageIgnoreEnd
