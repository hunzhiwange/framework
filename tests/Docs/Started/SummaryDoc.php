<?php

declare(strict_types=1);

namespace Tests\Docs\Started;

use Leevel\Kernel\Utils\Api;

#[Api([
    'title' => 'Summary',
    'zh-CN:title' => '概述',
    'zh-TW:title' => '概述',
    'path' => 'started/index',
])]
class SummaryDoc
{
}
