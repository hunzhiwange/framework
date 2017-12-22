<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\console;

use Symfony\Component\Console\{
    Helper\Table,
    Input\ArrayInput,
    Question\Question,
    Input\InputOption,
    Style\SymfonyStyle,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface,
    Helper\DescriptorHelper,
    Question\ChoiceQuestion,
    Formatter\OutputFormatterStyle,
    Command\Command as SymfonyCommand
};

/**
 * 命令抽象类 <from lavarel>
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.28
 * @version 1.0
 */
abstract class command extends SymfonyCommand
{
    
    /**
     * 项目容器
     *
     * @var \queryyetsimple\bootstrap\project
     */
    protected $objProject;
    
    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName;
    
    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription;
    
    /**
     * 命令帮助
     *
     * @var string
     */
    protected $strHelp = '';
    
    /**
     * 输出映射
     *
     * @var array
     */
    protected static $arrVerbosityMap = [
        'v' => OutputInterface::VERBOSITY_VERBOSE, 
        'vv' => OutputInterface::VERBOSITY_VERY_VERBOSE, 
        'vvv' => OutputInterface::VERBOSITY_DEBUG, 
        'quiet' => OutputInterface::VERBOSITY_QUIET, 
        'normal' => OutputInterface::VERBOSITY_NORMAL
    ];
    
    /**
     * 默认输出映射
     *
     * @var int
     */
    protected $intVerbosity = OutputInterface::VERBOSITY_NORMAL;
    
    /**
     * 输入接口
     *
     * @var object
     */
    protected $objInput;
    
    /**
     * 输入接口
     *
     * @var object
     */
    protected $objOutput;
    
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct($this->getNames());
        $this->setDescription($this->getDescriptions());
        $this->setHelp($this->getHelps());
        $this->specifyParameters();
    }
    
    /**
     * 运行命令
     *
     * @param \Symfony\Component\Console\Input\InputInterface $objInput
     * @param \Symfony\Component\Console\Output\OutputInterface $objOutput
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->objInput = $input;
        $this->objOutput = new SymfonyStyle($input, $output);
        return parent::run($input, $output);
    }
    
    /**
     * 响应命令
     *
     * @param object $input
     * @param object $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->objProject->call([
            $this, 
            'handle'
        ]);
    }
    
    /**
     * 调用其他命令
     *
     * @param string $strCommand
     * @param array $arrArguments
     * @return int
     */
    public function call($strCommand, array $arrArguments = [])
    {
        $arrArguments['command'] = $strCommand;
        return $this->objProject->make('command_' . $strCommand)->run(new ArrayInput($arrArguments), $this->objOutput);
    }
    
    /**
     * 获取输入参数
     *
     * @param string $strKey
     * @return string|array
     */
    public function argument($strKey = null)
    {
        if (is_null($strKey)) {
            return $this->objInput->getArguments();
        }
        return $this->objInput->getArgument($strKey);
    }
    
    /**
     * 获取配置信息
     *
     * @param string $strKey
     * @return string|array
     */
    public function option($strKey = null)
    {
        if (is_null($strKey)) {
            return $this->objInput->getOptions();
        }
        return $this->objInput->getOption($strKey);
    }
    
    /**
     * 确认用户的问题
     *
     * @param string $strQuestion
     * @param bool $booDefault
     * @return bool
     */
    public function confirm($strQuestion, $booDefault = false)
    {
        return $this->objOutput->confirm($strQuestion, $booDefault);
    }
    
    /**
     * 提示用户输入
     *
     * @param string $strQuestion
     * @param string $booDefault
     * @return string
     */
    public function ask($strQuestion, $booDefault = null)
    {
        return $this->objOutput->ask($strQuestion, $booDefault);
    }
    
    /**
     * 输出一个表格文本
     *
     * @param array $arrHeaders
     * @param array $arrRows
     * @param string $strStyle
     * @return void
     */
    public function table(array $arrHeaders, array $arrRows, $strStyle = 'default')
    {
        $objTable = new Table($this->objOutput);
        $objTable->setHeaders($arrHeaders)->setRows($arrRows)->setStyle($strStyle)->render();
    }
    
    /**
     * 输出一个一般信息
     *
     * @param string $strMessage
     * @param null|int|string $intVerbosity
     * @return void
     */
    public function info($strMessage, $intVerbosity = null)
    {
        $this->line($strMessage, 'info', $intVerbosity);
    }
    
    /**
     * 返回一个带有时间的消息
     *
     * @param string $strMessage
     * @param string $strFormat
     * @return string
     */
    protected function time($strMessage, $strFormat = 'H:i:s')
    {
        return sprintf('[%s]', date($strFormat)) . $strMessage;
    }
    
    /**
     * 输出一个注释信息
     *
     * @param string $strMessage
     * @param null|int|string $intVerbosity
     * @return void
     */
    public function comment($strMessage, $intVerbosity = null)
    {
        $this->line($strMessage, 'comment', $intVerbosity);
    }
    
    /**
     * 输出一个问题信息
     *
     * @param string $strMessage
     * @param null|int|string $intVerbosity
     * @return void
     */
    public function question($strMessage, $intVerbosity = null)
    {
        $this->line($strMessage, 'question', $intVerbosity);
    }
    
    /**
     * 提示用户输入根据返回结果自动完成一些功能
     *
     * @param string $strQuestion
     * @param array $arrChoices
     * @param string $strDefault
     * @return string
     */
    public function askWithCompletion($strQuestion, array $arrChoices, $strDefault = null)
    {
        $strQuestion = new Question($strQuestion, $strDefault);
        $strQuestion->setAutocompleterValues($arrChoices);
        return $this->objOutput->askQuestion($strQuestion);
    }
    
    /**
     * 提示用户输入但是控制台隐藏答案
     *
     * @param string $strQuestion
     * @param bool $booFallback
     * @return string
     */
    public function secret($strQuestion, $booFallback = true)
    {
        $strQuestion = new Question($strQuestion);
        $strQuestion->setHidden(true)->setHiddenFallback($booFallback);
        return $this->objOutput->askQuestion($strQuestion);
    }
    
    /**
     * 给用户一个问题组选择
     *
     * @param string $strQuestion
     * @param array $arrChoices
     * @param string $strDefault
     * @param mixed $mixAttempts
     * @param bool $booMultiple
     * @return string
     */
    public function choice($strQuestion, array $arrChoices, $strDefault = null, $mixAttempts = null, $booMultiple = null)
    {
        $strQuestion = new ChoiceQuestion($strQuestion, $arrChoices, $strDefault);
        $strQuestion->setMaxAttempts($mixAttempts)->setMultiselect($booMultiple);
        return $this->objOutput->askQuestion($strQuestion);
    }
    
    /**
     * 输出一个错误信息
     *
     * @param string $strMessage
     * @param null|int|string $intVerbosity
     * @return void
     */
    public function error($strMessage, $intVerbosity = null)
    {
        $this->line($strMessage, 'error', $intVerbosity);
    }
    
    /**
     * 输出一个警告信息
     *
     * @param string $strMessage
     * @param null|int|string $intVerbosity
     * @return void
     */
    public function warn($strMessage, $intVerbosity = null)
    {
        if (! $this->objOutput->getFormatter()->hasStyle('warning')) {
            $this->objOutput->getFormatter()->setStyle('warning', new OutputFormatterStyle('yellow'));
        }
        $this->line($strMessage, 'warning', $intVerbosity);
    }
    
    /**
     * 输出一条独立的信息
     *
     * @param string $strMessage
     * @param string $strStyle
     * @param null|int|string $intVerbosity
     * @return void
     */
    public function line($strMessage, $strStyle = null, $intVerbosity = null)
    {
        $strMessage = $strStyle ? "<{$strStyle}>{$strMessage}</{$strStyle}>" : $strMessage;
        $this->objOutput->writeln($strMessage, $this->parseVerbosity($intVerbosity));
    }
    
    /**
     * 获取输入对象
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->objOutput;
    }
    
    /**
     * 设置或者返回服务容器
     *
     * @param \queryyetsimple\bootstrap\project $objProject
     * @return void
     */
    public function project($objProject = null)
    {
        if (is_null($objProject)) {
            return $this->objProject;
        } else {
            $this->objProject = $objProject;
            return $this;
        }
    }
    
    /**
     * 命令参数
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }
    
    /**
     * 命令配置
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
    
    /**
     * 设置默认输出级别
     *
     * @param string|int $mixLevel
     * @return void
     */
    protected function setVerbosity($mixLevel)
    {
        $this->intVerbosity = $this->parseVerbosity($mixLevel);
    }
    
    /**
     * 定义参数和配置
     *
     * @return void
     */
    protected function specifyParameters()
    {
        foreach ($this->getArguments() as $arrArgument) {
            $this->{'addArgument'}(...$arrArgument)
            
            ;
        }
        
        foreach ($this->getOptions() as $arrOption) {
            $this->{'addOption'}(...$arrOption)
            
            ;
        }
    }
    
    /**
     * 获取输入信息级别
     *
     * @param string|int $mixLevel
     * @return int
     */
    protected function parseVerbosity($mixLevel = null)
    {
        if (isset(static::$arrVerbosityMap[$mixLevel])) {
            $mixLevel = static::$arrVerbosityMap[$mixLevel];
        } elseif (! is_int($mixLevel)) {
            $mixLevel = $this->intVerbosity;
        }
        return $mixLevel;
    }
    
    /**
     * 返回命令名字
     *
     * @return string
     */
    protected function getNames()
    {
        return $this->strName;
    }
    
    /**
     * 返回命令描述
     *
     * @return string
     */
    protected function getDescriptions()
    {
        return $this->strDescription;
    }
    
    /**
     * 返回命令帮助
     *
     * @return string
     */
    protected function getHelps()
    {
        return $this->strHelp;
    }
}
