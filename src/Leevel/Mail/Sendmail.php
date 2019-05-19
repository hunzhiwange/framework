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

namespace Leevel\Mail;

use Swift_SendmailTransport;

/**
 * sendmail 邮件.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.28
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Sendmail extends Mail implements IMail
{
    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'global_from' => [
            'address' => null,
            'name'    => null,
        ],
        'global_to' => [
            'address' => null,
            'name'    => null,
        ],
        'path' => '/usr/sbin/sendmail -bs',
    ];

    /**
     * 创建 transport.
     *
     * @return object
     */
    protected function makeTransport(): object
    {
        return new Swift_SendmailTransport($this->option['path']);
    }
}
