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

namespace Leevel\Database\Ddd;

use RuntimeException;

/**
 * 模型实体未找到异常.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.10
 *
 * @version 1.0
 */
class EntityNotFoundException extends RuntimeException
{
    /**
     * 模型实体名字.
     *
     * @var string
     */
    protected $entity;

    /**
     * 设置模型实体.
     *
     * @param string $entity
     *
     * @return $this
     */
    public function setEntity(string $entity)
    {
        $this->entity = $entity;
        $this->message = sprintf('Entity `%s` was not found.', $entity);

        return $this;
    }

    /**
     * 取回模型实体.
     *
     * @return string
     */
    public function entity(): string
    {
        return $this->entity;
    }
}
