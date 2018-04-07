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
namespace Leevel\Filesystem\Provider;

use Leevel\{
    Di\Provider,
    Filesystem\Manager
};

/**
 * filesystem 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
class Register extends Provider
{

    /**
     * 是否延迟载入
     *
     * @var boolean
     */
    public static $defer = true;

    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        $this->filesystems();
        $this->filesystem();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'filesystems' => [
                'Leevel\Filesystem\Manager',
                'Qys\Filesystem\Manager'
            ],
            'filesystem' => [
                'Leevel\Filesystem\Filesystem',
                'Leevel\Filesystem\IFilesystem',
                'Qys\Filesystem\Filesystem',
                'Qys\Filesystem\IFilesystem'
            ]
        ];
    }

    /**
     * 注册 filesystems 服务
     *
     * @return void
     */
    protected function filesystems()
    {
        $this->singleton('filesystems', function ($project) {
            return new Manager($project);
        });
    }

    /**
     * 注册 filesystem 服务
     *
     * @return void
     */
    protected function filesystem()
    {
        $this->singleton('filesystem', function ($project) {
            return $project['filesystems']->connect();
        });
    }
}
