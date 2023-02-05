<?php

declare(strict_types=1);

namespace Leevel\Console;

use Leevel\Di\IContainer;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 命令抽象类.
 */
abstract class Command extends SymfonyCommand
{
    /**
     * 应用容器.
     */
    protected IContainer $container;

    /**
     * 命令名字.
     */
    protected string $name;

    /**
     * 命令行描述.
     */
    protected string $description;

    /**
     * 命令帮助.
     */
    protected string $help = '';

    /**
     * 输入接口.
     */
    protected InputInterface $input;

    /**
     * 输出接口.
     */
    protected SymfonyStyle $output;

    /**
     * 构造函数.
     */
    public function __construct()
    {
        parent::__construct($this->name);
        $this->setDescription($this->description);
        $this->setHelp($this->help);
        $this->setArgumentsAndOptions();
    }

    /**
     * 运行命令.
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = new SymfonyStyle($input, $output);

        return parent::run($input, $output);
    }

    /**
     * 调用其他命令.
     */
    public function call(string $command, array $arguments = []): int
    {
        $arguments['command'] = $command;

        return $this
            ->getApplication()
            ->find($command)
            ->run(
                new ArrayInput($arguments),
                $this->output
            )
        ;
    }

    /**
     * 获取输入参数.
     */
    public function getArgument(?string $key = null): null|array|string
    {
        if (null === $key) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * 获取配置信息.
     */
    public function getOption(?string $key = null): null|array|bool|string
    {
        if (null === $key) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * 输出一个表格文本.
     */
    public function table(array $headers, array $rows, string $style = 'default'): void
    {
        (new Table($this->output))
            ->setHeaders($headers)
            ->setRows($rows)
            ->setStyle($style)
            ->render()
        ;
    }

    /**
     * 返回一个带有时间的消息.
     */
    public function time(string $message, string $format = 'H:i:s'): string
    {
        return ($format ? sprintf('[%s]', date($format)) : '').$message;
    }

    /**
     * 输出一个一般信息.
     */
    public function info(string $message, int $verbosity = OutputInterface::VERBOSITY_NORMAL): void
    {
        $this->line($message, 'info', $verbosity);
    }

    /**
     * 输出一个注释信息.
     */
    public function comment(string $message, int $verbosity = OutputInterface::VERBOSITY_NORMAL): void
    {
        $this->line($message, 'comment', $verbosity);
    }

    /**
     * 输出一个问题信息.
     */
    public function question(string $message, int $verbosity = OutputInterface::VERBOSITY_NORMAL): void
    {
        $this->line($message, 'question', $verbosity);
    }

    /**
     * 输出一个错误信息.
     */
    public function error(string $message, int $verbosity = OutputInterface::VERBOSITY_NORMAL): void
    {
        $this->line($message, 'error', $verbosity);
    }

    /**
     * 输出一个警告信息.
     */
    public function warn(string $message, int $verbosity = OutputInterface::VERBOSITY_NORMAL): void
    {
        if (!$this->output->getFormatter()->hasStyle('warning')) {
            $this->output->getFormatter()->setStyle('warning', new OutputFormatterStyle('yellow'));
        }
        $this->line($message, 'warning', $verbosity);
    }

    /**
     * 输出一条独立的信息.
     */
    public function line(string $message, ?string $style = null, int $verbosity = OutputInterface::VERBOSITY_NORMAL): void
    {
        $message = $style ? "<{$style}>{$message}</{$style}>" : $message;
        $this->output->writeln($message, $verbosity);
    }

    /**
     * 设置服务容器.
     */
    public function setContainer(IContainer $container): void
    {
        $this->container = $container;
    }

    /**
     * 返回服务容器.
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * 响应命令.
     */
    protected function execute(InputInterface $input, OutputInterface $output): mixed
    {
        return $this->container->call([$this, 'handle']);
    }

    /**
     * 命令参数.
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * 命令配置.
     */
    protected function getOptions(): array
    {
        return [];
    }

    /**
     * 定义参数和配置.
     */
    protected function setArgumentsAndOptions(): void
    {
        foreach ($this->getArguments() as $argument) {
            $this->addArgument(...$argument);
        }

        foreach ($this->getOptions() as $option) {
            $this->addOption(...$option);
        }
    }
}
