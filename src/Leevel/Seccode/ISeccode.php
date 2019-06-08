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

namespace Leevel\Seccode;

/**
 * ISeccode 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.07
 *
 * @version 1.0
 */
interface ISeccode
{
    /**
     * 图像最大宽度.
     *
     * @var int
     */
    const MAX_WIDTH = 999;

    /**
     * 图像最大高度.
     *
     * @var int
     */
    const MAX_HEIGHT = 999;

    /**
     * 图像最小宽度.
     *
     * @var int
     */
    const MIN_WIDTH = 16;

    /**
     * 图像最小高度.
     *
     * @var int
     */
    const MIN_HEIGHT = 16;

    /**
     * 随机字母数字.
     *
     * @var string
     */
    const ALPHA_NUM = 'alpha_num';

    /**
     * 随机小写字母数字.
     *
     * @var string
     */
    const ALPHA_NUM_LOWERCASE = 'alpha_num_lowercase';

    /**
     * 随机大写字母数字.
     *
     * @var string
     */
    const ALPHA_NUM_UPPERCASE = 'alpha_num_uppercase';

    /**
     * 随机字母.
     *
     * @var string
     */
    const ALPHA = 'alpha';

    /**
     * 随机小写字母.
     *
     * @var string
     */
    const ALPHA_LOWERCASE = 'alpha_lowercase';

    /**
     * 随机大写字母.
     *
     * @var string
     */
    const ALPHA_UPPERCASE = 'alpha_uppercase';

    /**
     * 随机数字.
     *
     * @var string
     */
    const NUM = 'num';

    /**
     * 随机字中文.
     *
     * @var string
     */
    const CHINESE = 'chinese';

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Seccode\ISeccode
     */
    public function setOption(string $name, $value): self;

    /**
     * 设置验证码
     *
     * @param mixed  $code
     * @param string $outPath
     * @param bool   $autoCode
     * @param string $autoType
     */
    public function display($code = null, ?string $outPath = null, bool $autoCode = true, string $autoType = self::ALPHA_UPPERCASE): void;

    /**
     * 设置验证码
     *
     * @param string $code
     *
     * @return \Leevel\Seccode\ISeccode
     */
    public function code(string $code): self;

    /**
     * 返回验证码
     *
     * @return \Leevel\Seccode\ISeccode
     */
    public function getCode(): ?string;
}
