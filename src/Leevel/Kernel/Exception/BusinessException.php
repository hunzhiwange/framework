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

namespace Leevel\Kernel\Exception;

/**
 * 业务操作异常.
 *
 * - 业务异常与系统异常不同，一般不需要捕捉写入日志.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.14
 *
 * @version 1.0
 */
class BusinessException extends BadRequestHttpException
{
    /**
     * 业务逻辑异常重要程度.
     *
     * - 不同重要程度的业务需要针对性处理日志.
     * - 默认 0 表示不是很重要的业务日志.
     *
     * @var int
     */
    protected $importance = 0;

    /**
     * 构造函数.
     *
     * @param null|string $message
     * @param int         $code
     * @param int         $importance
     */
    public function __construct(?string $message = null, int $code = 0, int $importance = 0)
    {
        parent::__construct($message, $code);

        $this->importance = $importance;
    }

    /**
     * 设置业务逻辑异常重要性.
     *
     * @param int $importance
     */
    public function setImportance(int $importance): void
    {
        $this->importance = $importance;
    }

    /**
     * 返回业务逻辑异常重要性.
     *
     * @return int
     */
    public function getImportance(): int
    {
        return $this->importance;
    }
}
