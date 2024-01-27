<?php

declare(strict_types=1);

namespace Leevel\View;

/**
 * 视图抽象类.
 */
abstract class View implements IView
{
    /**
     * 变量值.
     */
    protected array $vars = [];

    /**
     * 配置.
     */
    protected array $config = [];

    /**
     * 构造函数.
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function setVar(array|string $name, mixed $value = null): void
    {
        if (\is_array($name)) {
            if ($name) {
                $this->vars = array_merge($this->vars, $name);
            }
        } else {
            $this->vars[$name] = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getVar(?string $name = null): mixed
    {
        if (null === $name) {
            return $this->vars;
        }

        return $this->vars[$name] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteVar(array $name): void
    {
        foreach ($name as $item) {
            if (isset($this->vars[$item])) {
                unset($this->vars[$item]);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function clearVar(): void
    {
        $this->vars = [];
    }

    /**
     * 提取变量并加载文件.
     */
    protected function extractVarsAndIncludeFile(string $file): string
    {
        ob_start();
        extract($this->vars);

        include $file;
        $result = ob_get_contents() ?: '';
        ob_end_clean();

        return $result;
    }

    /**
     * 分析展示的视图文件.
     *
     * @throws \RuntimeException
     */
    protected function parseDisplayFile(string $file, ?string $ext = null): string
    {
        if (!$file) {
            throw new \RuntimeException('Template file must be set.');
        }
        if (!is_file($file)) {
            $file = $this->parseFile($file, $ext);
        }
        if (!is_file($file)) {
            throw new \RuntimeException(sprintf('Template file `%s` does not exist.', $file));
        }

        // PHAR模式，文件路径像这样 phar:///User/dyhb/leevel.phar/vendor
        // 不能将两个斜杠替换为一个斜杆，否则会导致路径错误
        $file = str_replace('\\', '/', $file);

        return realpath($file) ?: $file;
    }

    /**
     * 分析模板真实路径.
     *
     * @throws \Exception
     */
    protected function parseFile(string $file, ?string $ext = null): string
    {
        if (preg_match('/^{(.*)}$/', $file, $matches)) {
            if (empty($matches[1])) {
                throw new \Exception('Template file must be set.');
            }

            $code = 'return '.$matches[1].';';

            try {
                return eval($code);
            } catch (\Throwable $e) {
                $message = sprintf('Eval [%s]: %s', $code, $e->getMessage());

                throw new \Exception($message);
            }
        }

        return $this->getThemePath().'/'.$file.
            ($ext ?: $this->config['suffix']);
    }

    /**
     * 获取主题路径.
     *
     * @throws \RuntimeException
     */
    protected function getThemePath(): string
    {
        if (!$this->config['theme_path']) {
            throw new \RuntimeException('Theme path must be set.');
        }

        return $this->config['theme_path'];
    }
}
