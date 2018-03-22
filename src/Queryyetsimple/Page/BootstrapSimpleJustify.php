<?php
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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Page;

/**
 * BootstrapSimpleJustify 分页渲染
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.14
 * @version 1.0
 */
class BootstrapSimpleJustify extends BootstrapSimple
{

    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Page\IPage $objPage
     * @param array $arrOption
     * @return void
     */
    public function __construct(IPage $objPage, array $arrOption = [])
    {
        parent::__construct($objPage, $arrOption);
        $this->option('align', 'justify');
    }
}
