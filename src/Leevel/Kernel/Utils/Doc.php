<?php

declare(strict_types=1);

namespace Leevel\Kernel\Utils;

use Leevel\Filesystem\Helper\CreateFile;

/**
 * 文档解析 Markdown.
 */
class Doc
{
    /**
     * 解析文档保存基础路径.
     */
    protected string $basePath;

    /**
     * 解析文档的 Git 仓库.
     */
    protected string $git;

    /**
     * 国际化.
     */
    protected string $i18n;

    /**
     * 默认语言.
     */
    protected string $defaultI18n;

    /**
     * 解析文档保存路径.
     */
    protected ?string $savePath = null;

    /**
     * 解析文档行内容.
     */
    protected array $lines = [];

    /**
     * 解析文档的对应的地址.
     */
    protected string $filePath = '';

    /**
     * 文档生成日志路径.
     */
    protected ?string $logPath = null;

    /**
     * 构造函数.
     */
    public function __construct(string $path, string $i18n, string $defaultI18n, string $git)
    {
        $this->basePath = $path;
        $this->i18n = $i18n;
        $this->defaultI18n = $defaultI18n;
        $this->git = $git;
    }

    /**
     * 解析文档.
     */
    public function handle(string $className): string
    {
        // @phpstan-ignore-next-line
        if (false === ($lines = $this->parseFileContnet($reflection = new \ReflectionClass($className)))) {
            return '';
        }

        $this->lines = $lines;
        $this->filePath = str_replace(['\\', 'Tests'], ['/', 'tests'], $className).'.php';

        if (!($markdown = $this->parseClassContent($reflection))) {
            return '';
        }

        $markdown .= $this->parseMethodContents($reflection);

        return $markdown;
    }

    /**
     * 解析文档并保存.
     */
    public function handleAndSave(string $className, ?string $path = null): array|bool
    {
        $markdown = trim($this->handle($className));
        $this->setSavePath($path);
        if (!$markdown || !$this->savePath) {
            return false;
        }

        $this->writeCache($this->savePath, $markdown);

        return [$this->savePath, $markdown];
    }

    /**
     * 设置文档生成日志路径.
     */
    public function setLogPath(string $logPath): void
    {
        $this->logPath = $logPath;
    }

    /**
     * 获取方法内容.
     */
    public static function getMethodBody(string $className, string $method, string $type = '', bool $withMethodName = true): string
    {
        $doc = new static('', '', '', '');
        // @phpstan-ignore-next-line
        if (false === ($lines = $doc->parseFileContnet(new \ReflectionClass($className)))) {
            return '';
        }

        $methodInstance = new \ReflectionMethod($className, $method);
        $result = $doc->parseMethodBody($lines, $methodInstance, $type);
        if ($withMethodName) {
            $result = '# '.$className.'::'.$method.PHP_EOL.$result;
        }

        return $result;
    }

    /**
     * 获取类内容.
     */
    public static function getClassBody(string $className): string
    {
        /** @phpstan-ignore-next-line */
        $lines = (new static('', '', '', ''))->parseFileContnet($reflectionClass = new \ReflectionClass($className));
        if (false === $lines) {
            return '';
        }

        $startLine = $reflectionClass->getStartLine() - 1;
        $endLine = $reflectionClass->getEndLine();
        $hasUse = false;
        $isOneFileOneClass = static::isOneFileOneClass($lines);

        $result = [];
        $result[] = 'namespace '.$reflectionClass->getNamespaceName().';';
        $result[] = '';
        foreach ($lines as $k => $v) {
            if ($k < $startLine || $k >= $endLine) {
                if ($k < $startLine && str_starts_with($v, 'use ') && $isOneFileOneClass) {
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
            return;
        }

        $basePath = str_replace('{i18n}', $this->i18n ? '/'.$this->i18n : '', $this->basePath);
        $this->savePath = $basePath.'/'.$path.'.md';
    }

    /**
     * 方法是否需要被解析.
     */
    protected function isMethodNeedParsed(\ReflectionMethod $method): bool
    {
        $name = $method->getName();

        return str_starts_with($name, 'test') || str_starts_with($name, 'doc');
    }

    /**
     * 解析文档内容.
     */
    protected function parseFileContnet(\ReflectionClass $reflection): array|false
    {
        if (!$fileName = $reflection->getFileName()) {
            return false;
        }

        return explode(PHP_EOL, file_get_contents($fileName) ?: '');
    }

    /**
     * 解析文档注解内容.
     */
    protected function parseClassContent(\ReflectionClass $reflection): string
    {
        if (!($info = $this->parseClassOrMethodApi($reflection))) {
            return '';
        }

        if (!($info = $this->parseComment($info, $reflection->getName()))) {
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
    protected function parseMethodContents(\ReflectionClass $reflection): string
    {
        $markdown = '';
        foreach ($reflection->getMethods() as $method) {
            if (!$this->isMethodNeedParsed($method)) {
                continue;
            }
            $markdown .= $this->parseMethodContent($method, $reflection);
        }

        return $markdown;
    }

    /**
     * 解析方法注解内容.
     */
    protected function parseMethodContent(\ReflectionMethod $method, \ReflectionClass $reflectionClass): string
    {
        if (!($info = $this->parseClassOrMethodApi($method))) {
            return '';
        }

        if (!($info = $this->parseComment($info, $reflectionClass->getName().'/'.$method->getName()))) {
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

    protected function parseClassOrMethodApi(\ReflectionMethod|\ReflectionClass $reflection): array
    {
        if (!($methodAttributes = $reflection->getAttributes())) {
            return [];
        }

        $info = [];
        foreach ($methodAttributes as $attribute) {
            if (Api::class === $attribute->getName()) {
                $info = $attribute->getArguments()[0] ?? [];

                break;
            }
        }

        if (!$info) {
            return [];
        }

        return $info;
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
    protected function formatTitle(string $title, string $level = '##'): string
    {
        if ($title) {
            $title = $level." {$title}".PHP_EOL;
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
    protected function formatUsers(\ReflectionClass $reflection): string
    {
        $uses = $this->parseUseDefined($this->lines, $reflection);
        if ($uses) {
            $uses = <<<EOT
                **Uses**

                ``` php
                <?php

                {$uses}
                ```

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
    protected function formatBody(\ReflectionMethod $method, string $lang): string
    {
        $type = str_starts_with($method->getName(), 'doc') ? 'doc' : '';
        $body = $this->parseMethodBody($this->lines, $method, $type);
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
    protected function parseUseDefined(array $lines, \ReflectionClass $classRef): string
    {
        $startLine = $classRef->getStartLine() - 1;
        $result = [];

        foreach ($lines as $k => $v) {
            $v = trim($v);

            if ($k >= $startLine) {
                break;
            }

            if (str_starts_with($v, 'use ')
                && !\in_array($v, ['use Tests\TestCase;'], true)
                  && !str_contains($v, '\\Fixtures\\')) {
                $result[] = $v;
            }
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * 解析方法内容.
     */
    protected function parseMethodBody(array $lines, \ReflectionMethod $method, string $type = ''): string
    {
        $startLine = $method->getStartLine() - 1;
        $endLine = $method->getEndLine();
        $offsetLength = 4;
        $result = [];

        // 文档类删除周围的函数定义
        // 删除内容上下的 NowDoc 标记
        if ('doc' === $type) {
            $startLine += 3;
            $endLine -= 2;
            $offsetLength = 12;
        }

        // 返回函数定义
        if ('define' === $type) {
            $commentLine = $this->computeMethodCommentLine($lines, $startLine);
            $startLine -= $commentLine;
            $endLine = $startLine + 1 + $commentLine;
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

        $result = implode(PHP_EOL, $result);
        if ('define' === $type && !str_ends_with($result, ';')) {
            $result .= ';';
        }

        return $result;
    }

    /**
     * 计算方法的注释开始位置.
     */
    protected function computeMethodCommentLine(array $lines, int $startLine): int
    {
        if (!(isset($lines[$startLine - 1])
            && '     */' === $lines[$startLine - 1])) {
            return 0;
        }

        $commentIndex = $startLine - 2;
        while (isset($lines[$commentIndex]) && '    /**' !== $lines[$commentIndex]) {
            --$commentIndex;
        }

        return $startLine - $commentIndex;
    }

    /**
     * 分析 API 注解信息.
     *
     * @throws \RuntimeException
     */
    protected function parseComment(array $info, string $logName): array
    {
        $logName = str_replace('\\', '/', $logName).'.php';

        foreach ($info as &$v) {
            $v = $this->parseExecutableCode($v);
            $code = "\$v = \"{$v}\";";

            try {
                eval($code);
                if ($this->logPath) {
                    $this->writeCache($this->logPath.'/logs/'.$logName, '<?php'.PHP_EOL.$code);
                }
            } catch (\Throwable $exception) {
                if ($this->logPath) {
                    $this->writeCache($errorsLogPath = $this->logPath.'/errors/'.$logName, '<?php'.PHP_EOL.$code);

                    throw new \RuntimeException(sprintf('Documentation error was found and report at %s and error message is %s.', $errorsLogPath, $exception->getMessage()));
                }

                throw new \RuntimeException('Documentation error was found and error message is '.$exception->getMessage().PHP_EOL.PHP_EOL.'<?php'.PHP_EOL.$code);
            }
        }

        return $info;
    }

    /**
     * 分析可执行代码.
     */
    protected function parseExecutableCode(string $content): string
    {
        if (preg_match_all('/\{\[(.+)\]\}/', $content, $matches)) {
            foreach ($matches[1] as $tmp) {
                $content = str_replace(
                    $tmp,
                    base64_encode($tmp),
                    $content,
                );
            }
        }

        // 保护单引号不被转义
        $content = str_replace($singleQuote = '\'', $singleQuoteEncoded = base64_encode('single-quote'), $content);
        $content = addslashes($content);
        $content = str_replace($singleQuoteEncoded, $singleQuote, $content);
        $content = str_replace('$', '\$', $content);

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
        CreateFile::handle($cachePath, $data);
    }
}
