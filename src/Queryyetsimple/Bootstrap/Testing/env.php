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

/*
 * phpunit 环境变量设置
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.28
 * @version 1.0
 */
putenv('app_name=phpunittesting');
putenv('controller_name=bootstrap');
putenv('action_name=index');
putenv('app_bootstrap=' . __DIR__ . '/bootstrap.php');
