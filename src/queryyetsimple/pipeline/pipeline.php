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
namespace queryyetsimple\pipeline;

use Closure;
use Generator;
use InvalidArgumentException;
use queryyetsimple\support\icontainer;

/**
 * 管道实现类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.25
 * @version 1.0
 */
class pipeline implements ipipeline
{

    /**
     * 容器
     *
     * @var \queryyetsimple\support\icontainer
     */
    protected $objContainer;

    /**
     * 管道传递的对象
     *
     * @var array
     */
    protected $arrPassed = [];

    /**
     * 管道中所有执行工序
     *
     * @var array
     */
    protected $arrStage = [];

    /**
     * 迭代器
     *
     * @var \Generator
     */
    protected $objGenerator;

    /**
     * 创建一个管道
     *
     * @param \queryyetsimple\support\icontainer $objContainer
     * @return void
     */
    public function __construct(icontainer $objContainer)
    {
        $this->objContainer = $objContainer;
    }

    /**
     * 管道初始化
     *
     * @return $this
     */
    public function reset()
    {
        $this->arrPassed = [];
        $this->arrStage = [];
        $this->objGenerator = null;

        return $this;
    }

    /**
     * 将传输对象传入管道
     *
     * @param mixed $mixPassed
     * @return $this
     */
    public function send($mixPassed)
    {
        $mixPassed = is_array($mixPassed) ? $mixPassed : func_get_args();
        foreach ($mixPassed as $mixItem) {
            $this->arrPassed[] = $mixItem;
        }

        return $this;
    }

    /**
     * 设置管道中的执行工序
     *
     * @param dynamic|array $mixStage
     * @return $this
     */
    public function through($mixStage)
    {
        $mixStage = is_array($mixStage) ? $mixStage : func_get_args();
        foreach ($mixStage as $mixItem) {
            $this->arrStage[] = $mixItem;
        }

        return $this;
    }

    /**
     * 执行管道工序响应结果
     *
     * @param callable $calEnd
     * @since 2018.01.03
     * @return void
     */
    public function then(callable $calEnd = null)
    {
        $arrStage = $this->arrStage;
        if ($calEnd) {
            $arrStage[] = $calEnd;
        }
        $this->objGenerator = $this->stageGenerator($arrStage);

        $this->traverseGenerator(...$this->arrPassed);
    }

    /**
     * 遍历迭代器
     *
     * @since 2018.01.03
     * @return void
     */
    protected function traverseGenerator() {
        if(! $this->objGenerator->valid() || $this->objGenerator->next() || ! $this->objGenerator->valid()) {
           return;
        }

        $aArgs = func_get_args();
        array_unshift($aArgs, function() {
            $this->traverseGenerator(...func_get_args());
        });

        $this->objGenerator->current()(...$aArgs);
    }

    /**
     * 工序迭代器
     * 添加一个空的迭代器，第一次迭代 next 自动移除
     *
     * @param array $arrStage
     * @return \Generator
     */
    protected function stageGenerator(array $arrStage) {
        array_unshift($arrStage, null);
        
        foreach ($arrStage as $mixStage) {
           yield $this->stageCallback($mixStage);
        }
    }

    /**
     * 工序回调
     *
     * @param mixed $mixStage
     * @return null|callable
     */
    protected function stageCallback($mixStage)
    {   
        if(is_null($mixStage)) {
            return;
        }

        if (is_callable($mixStage)) {
            return $mixStage;
        } else {
            list($strStage, $arrParams) = $this->parse($mixStage);

            if (strpos($strStage, '@') !== false) {
                list($strStage, $strMethod) = explode('@', $strStage);
            } else {
                $strMethod = 'handle';
            }

            if (($objStage = $this->objContainer->make($strStage)) === false) {
                throw new InvalidArgumentException(sprintf('Stage %s is not valid.', $strStage));
            }

            return [
                $objStage,
                $strMethod
            ];
        }
    }

    /**
     * 解析工序
     *
     * @param string $strStage
     * @return array
     */
    protected function parse(string $strStage)
    {
        list($strName, $arrArgs) = array_pad(explode(':', $strStage, 2), 2, []);
        if (is_string($arrArgs)) {
            $arrArgs = explode(',', $arrArgs);
        }

        return [
            $strName,
            $arrArgs
        ];
    }
}
