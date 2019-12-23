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

namespace Leevel\Kernel\Utils;

use Leevel\Filesystem\Fso\create_file;
use function Leevel\Filesystem\Fso\create_file;
use Leevel\Support\Str\ends_with;
use function Leevel\Support\Str\ends_with;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

/**
 * 文档解析 Markdown.
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
    protected string $basePath;

    /**
     * 解析文档的 Git 仓库.
     *
     * @var string
     */
    protected string $git;

    /**
     * 国际化.
     *
     * @var string
     */
    protected string $i18n;

    /**
     * 默认语言.
     *
     * @var string
     */
    protected string $defaultI18n;

    /**
     * 解析文档保存路径.
     *
     * @var string
     */
    protected ?string $savePath = null;

    /**
     * 解析文档行内容.
     *
     * @var array
     */
    protected array $lines = [];

    /**
     * 解析文档的对应的地址.
     *
     * @var string
     */
    protected string $filePath;

    /**
     * 构造函数.
     */
    public function __construct(string $path, string $git, string $i18n, string $defaultI18n = 'zh-CN')
    {
        $this->basePath = $path;
        $this->git = $git;
        $this->i18n = $i18n;
        $this->defaultI18n = $defaultI18n;
    }

    /**
     * 解析文档.
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
     * @return array|bool
     */
    public function handleAndSave(string $className, ?string $path = null)
    {
        $this->setSavePath($path);

        $markdown = trim($this->handle($className));
        if (!$markdown || !$this->savePath) {
            return false;
        }

        $this->writeCache($this->savePath, $markdown);

        return [$this->savePath, $markdown];
    }

    /**
     * 获取方法内容.
     */
    public static function getMethodBody(string $className, string $method, bool $isDoc = false): string
    {
        $doc = new static('', '', '');
        $lines = $doc->parseFileContnet(new ReflectionClass($className));
        $method = new ReflectionMethod($className, $method);

        return $doc->parseMethodBody($lines, $method, $isDoc);
    }

    /**
     * 获取类内容.
     */
    public static function getClassBody(string $className): string
    {
        $lines = (new static('', '', ''))->parseFileContnet($reflectionClass = new ReflectionClass($className));
        $startLine = $reflectionClass->getStartLine() - 1;
        $endLine = $reflectionClass->getEndLine();
        $hasUse = false;
        $isOneFileOneClass = static::isOneFileOneClass($lines);

        $result = [];
        $result[] = 'namespace '.$reflectionClass->getNamespaceName().';';
        $result[] = '';

        foreach ($lines as $k => $v) {
            if ($k < $startLine || $k >= $endLine) {
                if ($k < $startLine && 0 === strpos($v, 'use ') && $isOneFileOneClass) {
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
     * 是否一个文件一个类.
     *
     * - 多个文件在同一个类，因为 Psr4 查找规则，只可能在当前文件，则可以共享 use 文件导入
     */
    protected static function isOneFileOneClass(array $contentLines): bool
    {
        $content = implode(PHP_EOL, $contentLines);

        return strpos($content, 'class ') === strrpos($content, 'class ');
    }

    /**
     * 设置保存路径.
     */
    protected function setSavePath(?string $path = null): void
    {
        if (null === $path) {
            $this->savePath = null;
        } else {
            $basePath = str_replace('{i18n}', $this->i18n ? '/'.$this->i18n : '', $this->basePath);
            $this->savePath = $basePath.'/'.$path.'.md';
        }
    }

    /**
     * 方法是否需要被解析.
     */
    protected function isMethodNeedParsed(ReflectionMethod $method): bool
    {
        $name = $method->getName();

        return 0 === strpos($name, 'test') || 0 === strpos($name, 'doc');
    }

    /**
     * 解析文档内容.
     */
    protected function parseFileContnet(ReflectionClass $reflection): array
    {
        return explode(PHP_EOL, file_get_contents($reflection->getFileName()));
    }

    /**
     * 解析文档注解内容.
     */
    protected function parseClassContent(ReflectionClass $reflection): string
    {
        if (!($comment = $reflection->getDocComment()) ||
            !($info = $this->parseComment($comment))) {
            return '';
        }

        $data = [];
        $data[] = $this->formatTitle($this->parseDocItem($info, 'title'), '#');
        $data[] = $this->formatFrom($this->git, $this->filePath);
        $data[] = $this->formatDescription($this->parseDocItem($info, 'description'));
        $data[] = $this->formatUsers($reflection);
        $data = array_filter($data);

        // 指定保存路径
        if (isset($info['path'])) {
            $this->setSavePath($info['path']);
        }

        return implode(PHP_EOL, $data).PHP_EOL;
    }

    /**
     * 解析所有方法注解内容.
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
     */
    protected function parseMethodContent(ReflectionMethod $method): string
    {
        if (!($comment = $method->getDocComment()) ||
            !($info = $this->parseComment($comment))) {
            return '';
        }

        $data = [];
        $data[] = $this->formatTitle($this->parseDocItem($info, 'title'), $this->parseDocItem($info, 'level', '##'));
        $data[] = $this->formatDescription($this->parseDocItem($info, 'description'));
        $data[] = $this->formatBody($method, $this->parseDocItem($info, 'lang', 'php'));
        $data[] = $this->formatNote($this->parseDocItem($info, 'note'));
        $data = array_filter($data);

        return implode(PHP_EOL, $data).PHP_EOL;
    }

    /**
     * 解析文档项.
     */
    protected function parseDocItem(array $info, string $name, string $defaultValue = ''): string
    {
        $i18n = $this->i18n ? $this->i18n.':' : '';
        $defaultI18n = $this->defaultI18n ? $this->defaultI18n.':' : '';

        return $info[$i18n.$name] ??
            ($info[$defaultI18n.$name] ??
                ($info[$name] ?? $defaultValue));
    }

    /**
     * 格式化标题.
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
     */
    protected function formatFrom(string $git, string $filePath): string
    {
        return <<<EOT
            ::: tip Testing Is Documentation
            [{$filePath}]({$git}/{$filePath})
            :::
                
            EOT;
    }

    /**
     * 格式化 uses.
     */
    protected function formatUsers(ReflectionClass $reflection): string
    {
        $uses = $this->parseUseDefined($this->lines, $reflection);
        if ($uses) {
            $uses = <<<EOT
                **Uses**
                
                {$uses}

                EOT;
        }

        return $uses;
    }

    /**
     * 格式化描述.
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
     */
    protected function parseComment(string $comment): array
    {
        $findApi = $inMultiComment = false;
        $result = [];
        $code = ['$result = ['];

        foreach (explode(PHP_EOL, $comment) as $v) {
            $originalV = $v;
            $v = trim($v, '* ');

            // @api 开始
            if ('@api(' === $v) {
                $findApi = true;
            } elseif (true === $findApi) {
                // @api 结尾
                if (')' === $v) {
                    break;
                }

                // 匹配字段格式，以便于支持多行
                if (false === $inMultiComment && preg_match('/^[a-zA-Z:-]+=\"/', $v)) {
                    $code[] = $this->parseSingleComment($v);
                } else {
                    list($content, $inMultiComment) = $this->parseMultiComment($v, $originalV);
                    $code[] = $content;
                }
            }
        }

        $code[] = '];';
        $code = implode('', $code);

        try {
            eval($code);
            file_put_contents(__DIR__.'/doc.rightlog.php', '<?php'.PHP_EOL.$code);
        } catch (Throwable $th) {
            file_put_contents(__DIR__.'/doc.errorlog.php', '<?php'.PHP_EOL.$code);

            throw $th;
        }

        return $result;
    }

    /**
     * 分析多行注释.
     */
    protected function parseMultiComment(string $content, string $originalContent): array
    {
        $inMultiComment = true;
        if ('' === $content) {
            return [PHP_EOL, $inMultiComment];
        }

        $content = $originalContent;
        if (0 === strpos($content, '     * ')) {
            $content = substr($content, 7);
        }
        if (0 === strpos($content, ' * ')) {
            $content = substr($content, 3);
        }

        // 多行结尾必须独立以便于区分
        if ('",' !== trim($content)) {
            $content = $this->parseExecutableCode($content);
        } else {
            $inMultiComment = false;
        }

        $content = str_replace('$', '\$', $content).PHP_EOL;

        return [$content, $inMultiComment];
    }

    /**
     * 分析单行注释.
     */
    protected function parseSingleComment(string $content): string
    {
        $pos = strpos($content, '=');
        $left = '"'.substr($content, 0, $pos).'"';
        $right = $this->normalizeSinggleRight(substr($content, $pos + 1));

        return $left.'=>'.$right;
    }

    /**
     * 整理单行注释右边值.
     */
    protected function normalizeSinggleRight(string $content): string
    {
        $content = $this->parseExecutableCode($content);
        if (0 === strpos($content, '\"')) {
            $content = substr($content, 1);
        }
        if (ends_with($content, '\",')) {
            $content = substr($content, 0, strlen($content) - 3).'",';
        }

        return str_replace('$', '\$', $content);
    }

    /**
     * 分析可执行代码.
     */
    protected function parseExecutableCode(string $content): string
    {
        if (preg_match_all('/\{\[(.+)\]\}/', $content, $matches)) {
            $content = str_replace(
                $matches[1][0],
                base64_encode($matches[1][0]),
                $content,
            );
        }

        // 保护单引号不被转义
        $content = str_replace($singleQuote = '\'', $singleQuoteEncoded = base64_encode('single-quote'), $content);
        $content = addslashes($content);
        $content = str_replace($singleQuoteEncoded, $singleQuote, $content);

        if (!empty($matches)) {
            foreach ($matches[1] as $tmp) {
                $content = str_replace('{['.base64_encode($tmp).']}', '".'.$tmp.'."', $content);
            }
        }

        return $content;
    }

    /**
     * 写入缓存.
     */
    protected function writeCache(string $cachePath, string $data): void
    {
        create_file($cachePath, $data);
    }
}

// import fn.
class_exists(create_file::class); // @codeCoverageIgnore
class_exists(ends_with::class); // @codeCoverageIgnore
