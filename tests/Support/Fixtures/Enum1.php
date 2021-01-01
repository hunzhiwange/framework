<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;

class Enum1 extends Enum
{
    #[msg('错误类型一')]
    const ERROR_ONE = 100010;

    #[msg('自定义错误')]
    const CUSTOM_ERROR = 100011;

    const NO_ATTRIBUTES = 100013;

    #[msg]
    const NO_MSG = 100014;

    #[msg('Hello %s world')]
    const PARAMS_INVALID = 100015;

    #[msg('相同错误1')]
    const SAME_ERROR1 = 100016;

    #[msg('相同错误2')]
    const SAME_ERROR2 = 100016;

    #[status('Status enabled')]
    const STATUS_ENABLE = 1;

    #[status('Status disabled')]
    const STATUS_DISABLE = 0;

    #[accounts_type('管理员账号')]
    const ACCOUNTS_TYPE_MANAGER = 'manager';

    #[accounts_type('供应商账号')]
    const ACCOUNTS_TYPE_SUPPLIER = 'supplier';

    #[accounts_type('经销商账号')]
    const ACCOUNTS_TYPE_AGENCY = 'agency';

    #[type('Type enabled')]
    const TYPE_ENABLE = 1;

    #[type('Type disabled')]
    const TYPE_DISABLE = 0;

    #[type('Type bool true')]
    const TYPE_BOOL_TRUE = true;

    #[type('Type bool false')]
    const TYPE_BOOL_FALSE = false;

    #[type('Type int')]
    const TYPE_INT = 11;

    #[type('Type float')]
    const TYPE_FLOAT = 1.1;

    #[type('Type string float')]
    const TYPE_STRING_FLOAT = '1.1';

    #[type('Type string')]
    const TYPE_STRING = 'string';

    #[type('Type null')]
    const TYPE_NULL = null;
}
