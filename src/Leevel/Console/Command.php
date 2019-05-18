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

namespace Leevel\Console;

use Leevel\Di\IContainer;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 命令抽象类
 * This class borrows heavily from the Lavavel Framework and is part of the lavavel package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.28
 *
 * @version 1.0
 *
 * @see Illuminate/Console (https://github.com/laravel/framework)
 */
abstract class Command extends SymfonyCommand
{
    /**
     * 默认输出映射.
     *
     * @var int
     */
    const DEFAULT_VERBOSITY = OutputInterface::VERBOSITY_NORMAL;

    /**
     * 应用容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected IContainer $container;

    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name;

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description;

    /**
     * 命令帮助.
     *
     * @var string
     */
    protected $help;

    /**
     * 输出映射.
     *
     * @var array
     */
    protected static $verbosityMap = [
        'v'      => OutputInterface::VERBOSITY_VERBOSE,
        'vv'     => OutputInterface::VERBOSITY_VERY_VERBOSE,
        'vvv'    => OutputInterface::VERBOSITY_DEBUG,
        'quiet'  => OutputInterface::VERBOSITY_QUIET,
        'normal' => OutputInterface::VERBOSITY_NORMAL,
    ];

    /**
     * 输入接口.
     *
     * @var object
     */
    protected $input;

    /**
     * 输入接口.
     *
     * @var object
     */
    protected $output;

    /**
     * 构造函数.
     */
    public function __construct()
    {
        parent::__construct($this->name);

        $this->setDescription($this->description);
        $this->setHelp($this->help);
        $this->specifyParameters();
    }

    /**
     * 运行命令.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = new SymfonyStyle($input, $output);

        return parent::run($input, $output);
    }

    /**
     * 调用其他命令.
     *
     * @param string $command
     * @param array  $arguments
     *
     * @return int
     */
    public function call($command, array $arguments = []): int
    {
        $arguments['command'] = $command;

        return $this->getApplication()->find($command)->run(
            new ArrayInput($arguments), $this->output
        );
    }

    /**
     * 获取输入参数.
     *
     * @param string $key
     *
     * @return array|string
     */
    public function argument(?string $key = null)
    {
        if (null === $key) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * 获取配置信息.
     *
     * @param string $key
     *
     * @return array|bool|string
     */
    public function option(?string $key = null)
    {
        if (null === $key) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * 确认用户的问题.
     * 等待与用户进行交互，无法被测试.
     *
     * @param string $question
     * @param bool   $defaults
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function confirm($question, $defaults = false): bool
    {
        return $this->output->confirm($question, $defaults);
    }

    /**
     * 提示用户输入.
     * 等待与用户进行交互，无法被测试.
     *
     * @param string $question
     * @param string $defaults
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function ask($question, $defaults = null): string
    {
        return $this->output->ask($question, $defaults);
    }

    /**
     * 输出一个表格文本.
     *
     * @param array  $headers
     * @param array  $rows
     * @param string $style
     */
    public function table(array $headers, array $rows, $style = 'default'): void
    {
        (new Table($this->output))->

        setHeaders($headers)->

        setRows($rows)->

        setStyle($style)->

        render();
    }

    /**
     * 输出一个一般信息.
     *
     * @param string          $message
     * @param null|int|string $verbosity
     */
    public function info($message, $verbosity = null): void
    {
        $this->line($message, 'info', $verbosity);
    }

    /**
     * 返回一个带有时间的消息.
     *
     * @param string $message
     * @param string $format
     *
     * @return string
     */
    public function time($message, $format = 'H:i:s'): string
    {
        return ($format ? sprintf('[%s]', date($format)) : '').$message;
    }

    /**
     * 输出一个注释信息.
     *
     * @param string          $message
     * @param null|int|string $verbosity
     */
    public function comment($message, $verbosity = null): void
    {
        $this->line($message, 'comment', $verbosity);
    }

    /**
     * 输出一个问题信息.
     *
     * @param string          $message
     * @param null|int|string $verbosity
     */
    public function question($message, $verbosity = null): void
    {
        $this->line($message, 'question', $verbosity);
    }

    /**
     * 提示用户输入根据返回结果自动完成一些功能.
     * 等待与用户进行交互，无法被测试.
     *
     * @param string $question
     * @param array  $choices
     * @param string $defaults
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function askWithCompletion($question, array $choices, $defaults = null): string
    {
        $question = new Question($question, $defaults);
        $question->setAutocompleterValues($choices);

        return $this->output->askQuestion($question);
    }

    /**
     * 提示用户输入但是控制台隐藏答案.
     * 等待与用户进行交互，无法被测试.
     *
     * @param string $question
     * @param bool   $fallback
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function secret($question, bool $fallback = true): string
    {
        $question = new Question($question);
        $question->setHidden(true)->setHiddenFallback($fallback);

        return $this->output->askQuestion($question);
    }

    /**
     * 给用户一个问题组选择.
     * 等待与用户进行交互，无法被测试.
     *
     * @param string $question
     * @param array  $choices
     * @param string $defaults
     * @param mixed  $attempts
     * @param bool   $multiple
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function choice($question, array $choices, $defaults = null, $attempts = null, $multiple = null): string
    {
        $question = new ChoiceQuestion($question, $choices, $defaults);
        $question->setMaxAttempts($attempts)->setMultiselect($multiple);

        return $this->output->askQuestion($question);
    }

    /**
     * 输出一个错误信息.
     *
     * @param string          $message
     * @param null|int|string $verbosity
     */
    public function error($message, $verbosity = null): void
    {
        $this->line($message, 'error', $verbosity);
    }

    /**
     * 输出一个警告信息.
     *
     * @param string          $message
     * @param null|int|string $verbosity
     */
    public function warn($message, $verbosity = null): void
    {
        if (!$this->output->getFormatter()->hasStyle('warning')) {
            $this->output->getFormatter()->setStyle('warning', new OutputFormatterStyle('yellow'));
        }

        $this->line($message, 'warning', $verbosity);
    }

    /**
     * 输出一条独立的信息.
     *
     * @param string          $message
     * @param string          $style
     * @param null|int|string $verbosity
     */
    public function line($message, $style = null, $verbosity = null): void
    {
        $message = $style ? "<{$style}>{$message}</{$style}>" : $message;

        $this->output->writeln($message, $this->parseVerbosity($verbosity));
    }

    /**
     * 设置服务容器.
     *
     * @param \Leevel\Di\IContainer $container
     */
    public function setContainer(IContainer $container): void
    {
        $this->container = $container;
    }

    /**
     * 返回服务容器.
     *
     * @return \Leevel\Di\IContainer
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * 响应命令.
     *
     * @param object $input
     * @param object $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->container->call([
            $this,
            'handle',
        ]);
    }

    /**
     * 命令参数.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * 命令配置.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [];
    }

    /**
     * 定义参数和配置.
     */
    protected function specifyParameters(): void
    {
        foreach ($this->getArguments() as $argument) {
            $this->addArgument(...$argument);
        }

        foreach ($this->getOptions() as $option) {
            $this->addOption(...$option);
        }
    }

    /**
     * 获取输入信息级别.
     *
     * @param int|string $level
     *
     * @return int
     */
    protected function parseVerbosity($level = null): int
    {
        return static::$verbosityMap[$level] ??
            (!is_int($level) ? static::DEFAULT_VERBOSITY : $level);
    }
}
