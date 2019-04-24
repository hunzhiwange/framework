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

namespace Leevel\Database;

use PDOException;

/**
 * Replace 异常.
 *
 * 用于模拟数据库 replace.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.21
 * @since v1.0.0-beta.1@2019.04.24 如果是插入出现 unique 的唯一值重复也并入这样的错误
 *
 * @version 1.0
 */
class ReplaceException extends PDOException
{
}
