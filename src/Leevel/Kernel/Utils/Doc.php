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

namespace Leevel\Kernel\Utils;

use Leevel\Filesystem\Fso\create_file;
use function Leevel\Filesystem\Fso\create_file;
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
     * 解析文档的 Git 仓库.
     *
     * @var string
     */
    protected $git;

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
     * 解析文档的对应的地址.
     *
     * @var string
     */
    protected $filePath;

    /**
     * 构造函数.
     *
     * @param string $path
     * @param string $git
     */
    public function __construct(string $path, string $git)
    {
        $this->basePath = $path;
        $this->git = $git;
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
        $this->lines = $this->parseFileContnet($reflection = new ReflectionClass($className));
        $this->filePath = str_replace(['\\', 'Tests'], ['/', 'tests'], $className).'.php';

        if (!($markdown = $this->parseClassContent($reflection))) {
            return '';
        }

        $markdown .= $this->parseMethodContents($reflection);

        return $markdown;
    }

    /**
     * 解析文档并保存.
     *
     * @param string      $className
     * @param null|string $path
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
     * 获取方法内容.
     *
     * @param string $className
     * @param string $method
     * @param bool   $isDoc
     *
     * @return string
     */
    public static function getMethodBody(string $className, string $method, bool $isDoc = false): string
    {
        $doc = new static('', '');

        $lines = $doc->parseFileContnet(new ReflectionClass($className));

        $method = new ReflectionMethod($className, $method);

        return $doc->parseMethodBody($lines, $method, $isDoc);
    }

    /**
     * 获取类内容.
     *
     * @param string $className
     *
     * @return string
     */
    public static function getClassBody(string $className): string
    {
        $doc = new static('', '');

        $lines = $doc->parseFileContnet($reflectionClass = new ReflectionClass($className));

        $startLine = $reflectionClass->getStartLine() - 1;
        $endLine = $reflectionClass->getEndLine();
        $hasUse = false;
        $result = [];
        $result[] = 'namespace '.$reflectionClass->getNamespaceName().';';
        $result[] = '';

        foreach ($lines as $k => $v) {
            if ($k < $startLine || $k >= $endLine) {
                // 适用于一个文件只有一个类的情况
                if ($k < $startLine && 0 === strpos($v, 'use ')) {
                    $result[] = $v;
                    $hasUse = true;
                }

                continue;
            }

            if ($k === $startLine && true === $hasUse) {
                $result[] = '';
            }

            $result[] = $v;
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * 设置保存路径.
     *
     * @param null|string $path
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
     *
     * @return array
     */
    protected function parseFileContnet(ReflectionClass $reflection): array
    {
        return explode(PHP_EOL, file_get_contents($reflection->getFileName()));
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
        $data[] = $this->formatTitle($info['title'] ?? '', '#');
        $data[] = $this->formatFrom($this->git, $this->filePath);
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
        $data[] = $this->formatTitle($info['title'] ?? '', $info['level'] ?? '##');
        $data[] = $this->formatDescription($info['description'] ?? '');
        $data[] = $this->formatBody($method, $info['lang'] ?? 'php');
        $data[] = $this->formatNote($info['note'] ?? '');

        return implode(PHP_EOL, $data).PHP_EOL;
    }

    /**
     * 格式化标题.
     *
     * @param string $title
     * @param string $level
     *
     * @return string
     */
    protected function formatTitle(string $title, string $leevel = '##'): string
    {
        if ($title) {
            $title = $leevel." {$title}".PHP_EOL;
        }

        return $title;
    }

    /**
     * 格式化来源.
     *
     * @param string $git
     * @param string $filePath
     *
     * @return string
     */
    protected function formatFrom(string $git, string $filePath): string
    {
        return <<<EOT
            ::: tip 单元测试即文档
            [基于原始文档 {$filePath} 自动构建]({$git}/{$filePath})
            :::
                
            EOT;
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
            $uses = <<<EOT
                **引入相关类**
                
                {$uses}

                EOT;
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
            $note = <<<EOT
                ::: tip
                {$note}
                :::
                    
                EOT;
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
            $body = <<<EOT
                ``` {$lang}
                {$body}
                ```
                    
                EOT;
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

            if (0 === strpos($v, 'use ') &&
                !in_array($v, ['use Tests\TestCase;'], true) &&
                false === strpos($v, '\\Fixtures\\')) {
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
        $result = [];

        // 文档类删除周围的函数定义
        // 删除内容上下的 NowDoc 标记
        if ($isDoc) {
            $startLine += 3;
            $endLine -= 2;
            $offsetLength = 12;
        }

        foreach ($lines as $k => $v) {
            if ($k < $startLine || $k >= $endLine) {
                continue;
            }
        }

        foreach ($lines as $k => $v) {
            if ($k < $startLine || $k >= $endLine) {
                continue;
            }

            $result[] = substr($v, $offsetLength);
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
            $backupV = $v;
            $v = trim($v, '* ');
            $v = trim($v, '_'); // 删除占位符

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
                        if (0 === strpos($backupV, '     * ')) {
                            $backupV = substr($backupV, 7);
                        }

                        if (0 === strpos($backupV, ' * ')) {
                            $backupV = substr($backupV, 3);
                        }

                        $code[] = str_replace('$', '\$', $backupV).PHP_EOL; // 转义变量
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
        create_file($cachePath, $data);
    }
}

// import fn.
class_exists(create_file::class);
