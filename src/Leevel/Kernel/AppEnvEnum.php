<?php

declare(strict_types=1);

namespace Leevel\Kernel;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

/**
 * 应用环境枚举.
 */
enum AppEnvEnum: string
{
    use Enum;

    #[Msg('开发')]
    case DEVELOPMENT = 'development';

    #[Msg('测试')]
    case TESTING = 'testing';

    #[Msg('生产')]
    case PRODUCTION = 'production';
}
