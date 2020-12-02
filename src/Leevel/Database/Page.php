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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database;

use Leevel\Page\Page as BasePage;

/**
 * 数据库分页查询.
 */
class Page extends BasePage
{
    /**
     * 查询数据.
     */
    protected mixed $data = null;

    /**
     * 设置数据.
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     * 获取数据.
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'page' => parent::toArray(),
            'data' => $this->data,
        ];
    }
}
