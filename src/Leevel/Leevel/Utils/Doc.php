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

namespace Leevel\Leevel\Utils;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;

/**
 * 文档解析 Markdown.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.02.27
 *
 * @version 1.0
 *
 * @todo 将 markdown 模板提炼出来
 * @todo 为本功能编写单元测试用例
 * @codeCoverageIgnore
 */
class Doc
{
    /**
     * 解析文档保存基础路径.
     *
     * @var string
     */
    protected $basePath;

    /**
     * 解析文档保存路径.
     *
     * @var string
     */
    protected $savePath;

    /**
     * 解析文档行内容.
     *
     * @var array
     */
    protected $lines = [];

    /**
     * 构造函数.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->basePath = $path;
    }

    /**
     * 解析文档.
     *
     * @param string $className
     *
     * @return string
     */
    public function handle(string $className): string
    {
        $this->parseFileContnet($reflection = new ReflectionClass($className));

        if (!($markdown = $this->parseClassContent($reflection))) {
            return '';
        }

        $markdown .= $this->parseMethodContents($reflection);

        return $markdown;
    }

    /**
     * 解析文档并保存.
     *
     * @param string $className
     * @param string $path
     *
     * @return bool
     */
    public function handleAndSave(string $className, ?string $path = null): bool
    {
        $this->setSavePath($path);

        $markdown = trim($this->handle($className));

        if (!$markdown || !$this->savePath) {
            return false;
        }

        $this->writeCache($this->savePath, $markdown);

        return true;
    }

    /**
     * 设置保存路径.
     *
     * @param string $path
     */
    protected function setSavePath(?string $path = null): void
    {
        if (null === $path) {
            $this->savePath = null;
        } else {
            $this->savePath = $this->basePath.'/'.$path.'.md';
        }
    }

    /**
     * 方法是否需要被解析.
     *
     * @param \ReflectionMethod $method
     *
     * @return bool
     */
    protected function isMethodNeedParsed(ReflectionMethod $method): bool
    {
        $name = $method->getName();

        return 0 === strpos($name, 'test') || 0 === strpos($name, 'doc');
    }

    /**
     * 解析文档内容.
     *
     * @param \ReflectionClass $reflection
     */
    protected function parseFileContnet(ReflectionClass $reflection): void
    {
        $lines = explode(PHP_EOL, file_get_contents($reflection->getFileName()));

        $this->lines = $lines;
    }

    /**
     * 解析文档注解内容.
     *
     * @param \ReflectionClass $reflection
     *
     * @return string
     */
    protected function parseClassContent(ReflectionClass $reflection): string
    {
        if (!($comment = $reflection->getDocComment()) ||
            !($info = $this->parseComment($comment))) {
            return '';
        }

        $data = [];
        $data[] = $this->formatTitle($info['title'] ?? '');
        $data[] = $this->formatDescription($info['description'] ?? '');
        $data[] = $this->formatUsers($reflection);

        // 指定保存路径
        if (isset($info['path'])) {
            $this->setSavePath($info['path']);
        }

        return implode(PHP_EOL, $data);
    }

    /**
     * 解析所有方法注解内容.
     *
     * @param \ReflectionClass $reflection
     *
     * @return string
     */
    protected function parseMethodContents(ReflectionClass $reflection): string
    {
        $markdown = '';

        foreach ($reflection->getMethods() as $method) {
            if (!$this->isMethodNeedParsed($method)) {
                continue;
            }

            $markdown .= $this->parseMethodContent($method);
        }

        return $markdown;
    }

    /**
     * 解析方法注解内容.
     *
     * @param \ReflectionMethod $comment
     *
     * @return string
     */
    protected function parseMethodContent(ReflectionMethod $method): string
    {
        if (!($comment = $method->getDocComment()) ||
            !($info = $this->parseComment($comment))) {
            return '';
        }

        $data = [];
        $data[] = $this->formatTitle($info['title'] ?? '');
        $data[] = $this->formatDescription($info['description'] ?? '');
        $data[] = $this->formatNote($info['note'] ?? '');
        $data[] = $this->formatBody($method, $info['lang'] ?? 'php');

        return implode(PHP_EOL, $data);
    }

    /**
     * 格式化标题.
     *
     * @param string $title
     * @param bool   $isHeader
     *
     * @return string
     */
    protected function formatTitle(string $title, bool $isHeader = false): string
    {
        if ($title) {
            $title = ($isHeader ? '#' : '##')." {$title}".PHP_EOL;
        }

        return $title;
    }

    /**
     * 格式化 uses.
     *
     * @param \ReflectionClass $reflection
     *
     * @return string
     */
    protected function formatUsers(ReflectionClass $reflection): string
    {
        $uses = $this->parseUseDefined($this->lines, $reflection);

        if ($uses) {
            $uses = <<<eot
** 引入相关类 **

{$uses}

eot;
        }

        return $uses;
    }

    /**
     * 格式化描述.
     *
     * @param string $description
     *
     * @return string
     */
    protected function formatDescription(string $description): string
    {
        if ($description) {
            $description = $description.PHP_EOL;
        }

        return $description;
    }

    /**
     * 格式化注意事项.
     *
     * @param string $note
     *
     * @return string
     */
    protected function formatNote(string $note): string
    {
        if ($note) {
            $note = <<<eot
::: tip
{$note}
:::
    
eot;
        }

        return $note;
    }

    /**
     * 格式化内容.
     *
     * @param \ReflectionMethod $method
     * @param string            $lang
     *
     * @return string
     */
    protected function formatBody(ReflectionMethod $method, string $lang): string
    {
        $body = $this->parseMethodBody($this->lines, $method, 0 === strpos($method->getName(), 'doc'));

        if ($body) {
            $body = <<<eot
``` {$lang}
{$body}
```
    
eot;
        }

        return $body;
    }

    /**
     * 解析 use 导入类.
     *
     * @param array            $lines
     * @param \ReflectionClass $classRef
     *
     * @return string
     */
    protected function parseUseDefined(array $lines, ReflectionClass $classRef): string
    {
        $startLine = $classRef->getStartLine() - 1;
        $result = [];

        foreach ($lines as $k => $v) {
            $v = trim($v);

            if ($k >= $startLine) {
                break;
            }

            if (0 === strpos($v, 'use ') && !in_array($v, ['use Tests\TestCase;'], true)) {
                $result[] = ' * '.$v;
            }
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * 解析方法内容.
     *
     * @param array             $lines
     * @param \ReflectionMethod $method
     * @param bool              $isDoc
     *
     * @return string
     */
    protected function parseMethodBody(array $lines, ReflectionMethod $method, bool $isDoc = false): string
    {
        $startLine = $method->getStartLine() - 1;
        $endLine = $method->getEndLine();
        $offsetLength = 4;
        $findEot = false;
        $result = [];

        // 文档类删除周围的函数定制
        // 并且删除掉注释 `/**/` 标记
        if ($isDoc) {
            $startLine += 3;
            $endLine -= 2;
            $offsetLength = 8;
        }

        foreach ($lines as $k => $v) {
            if ($k < $startLine || $k >= $endLine) {
                continue;
            }

            if (false !== strpos($v, 'eot')) {
                $findEot = true;

                break;
            }
        }

        foreach ($lines as $k => $v) {
            if ($k < $startLine || $k >= $endLine) {
                continue;
            }

            // 占位符行
            if ('#_' === trim($v)) {
                continue;
            }

            if (true === $findEot) {
                // eot 需要特别处理
                if ($k === $startLine ||
                    $k === $startLine + 1 ||
                    $k === $endLine - 1 ||
                    (strlen($v) - strlen(ltrim($v)) >= $offsetLength + 4)) {
                    $result[] = substr($v, $offsetLength);
                } else {
                    $result[] = $v;
                }
            } else {
                $result[] = substr($v, $offsetLength);
            }
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * 获取 API 注解信息.
     *
     * @param string $comment
     *
     * @return array
     */
    protected function parseComment(string $comment): array
    {
        $comments = explode(PHP_EOL, $comment);
        $findApi = false;
        $result = [];
        $code = ['$result = ['];

        foreach ($comments as $v) {
            $v = trim($v, '* ');
            $v = ltrim($v, '_'); // 删除占位符

            if ('@api(' === $v) {
                $findApi = true;
            } elseif (true === $findApi) {
                if (')' === $v) {
                    break;
                }
                // 匹配字段格式，以便于支持多行
                if (preg_match('/^\S+=\"/', $v)) {
                    $pos = strpos($v, '=');
                    $left = '"'.substr($v, 0, $pos).'"';
                    $right = substr($v, $pos + 1);
                    $right = str_replace('$', '\$', $right); // 转义变量

                    $code[] = $left.'=>'.$right;
                } else {
                    // 空行加两个换行符撑开
                    if ('' === $v) {
                        $code[] = PHP_EOL.PHP_EOL;
                    } else {
                        $code[] = str_replace('$', '\$', $v).PHP_EOL; // 转义变量
                    }
                }
            }
        }

        $code[] = '];';
        eval(implode('', $code));

        return $result;
    }

    /**
     * 写入缓存.
     *
     * @param string $cachePath
     * @param string $data
     */
    protected function writeCache(string $cachePath, string $data): void
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
}
