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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\IMeta;

/**
 * TestEventEntity.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.05
 *
 * @version 1.0
 */
class TestEventEntity extends Entity
{
    const TABLE = 'test';

    /**
     * 存在复合主键.
     *
     * @var array
     */
    const PRIMARY_KEY = [
        'id',
    ];

    const AUTO_INCREMENT = 'id';

    const STRUCT = [
        'id' => [
            'name'           => 'id', // database
            'type'           => 'int', // database
            'length'         => 11, // database
            'primary_key'    => true, // database
            'auto_increment' => true, // database
            'default'        => null, // database
        ],
        'name' => [
            'name'           => 'name',
            'type'           => 'varchar',
            'length'         => 45,
            'primary_key'    => false,
            'auto_increment' => false,
            'default'        => null,
        ],
    ];
    protected $id;

    protected $name;

    /**
     * 返回模型类的 meta 对象
     *
     * @return Leevel\Database\Ddd\IMeta
     */
    public function meta(): IMeta
    {
        return new MockMeta();
    }

    /**
     * 模型快捷事件 selecting.
     */
    protected function runEventSelecting()
    {
    }

    /**
     * 模型快捷事件 selected.
     */
    protected function runEventSelected()
    {
    }

    /**
     * 模型快捷事件 finding.
     */
    protected function runEventFinding()
    {
    }

    /**
     * 模型快捷事件 finded.
     */
    protected function runEventFinded()
    {
    }

    /**
     * 模型快捷事件 saveing.
     */
    protected function runEventSaveing()
    {
        $_SERVER['model_runEventSaveing'] = true;
    }

    /**
     * 模型快捷事件 saved.
     */
    protected function runEventSaved()
    {
        $_SERVER['model_runEventSaved'] = true;
    }

    /**
     * 模型快捷事件 creating.
     */
    protected function runEventCreating()
    {
        $_SERVER['model_runEventCreating'] = true;
    }

    /**
     * 模型快捷事件 created.
     */
    protected function runEventCreated()
    {
        $_SERVER['model_runEventCreated'] = true;
    }

    /**
     * 模型快捷事件 updating.
     */
    protected function runEventUpdating()
    {
        $_SERVER['model_runEventUpdating'] = true;
    }

    /**
     * 模型快捷事件 updated.
     */
    protected function runEventUpdated()
    {
        $_SERVER['model_runEventUpdated'] = true;
    }

    /**
     * 模型快捷事件 deleting.
     */
    protected function runEventDeleting()
    {
    }

    /**
     * 模型快捷事件 deleted.
     */
    protected function runEventDeleted()
    {
    }

    /**
     * 模型快捷事件 softDeleting.
     */
    protected function runEventSoftDeleting()
    {
    }

    /**
     * 模型快捷事件 softDeleted.
     */
    protected function runEventSoftDeleted()
    {
    }

    /**
     * 模型快捷事件 softRestoring.
     */
    protected function runEventSoftRestoring()
    {
    }

    /**
     * 模型快捷事件 softRestored.
     */
    protected function runEventSoftRestored()
    {
    }
}

class MockMeta implements IMeta
{
    public function insert(array $data)
    {
        return 10;
    }

    public function update(array $condition, array $data)
    {
        return 1;
    }
}
