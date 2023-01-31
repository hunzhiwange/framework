<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;

class Enum1
{
    use Enum;

    #[msg('错误类型一')]
    public const ERROR_ONE = 100010;

    #[msg('自定义错误')]
    public const CUSTOM_ERROR = 100011;

    public const NO_ATTRIBUTES = 100013;

    #[msg]
    public const NO_MSG = 100014;

    #[msg('Hello %s world')]
    public const PARAMS_INVALID = 100015;

    #[msg('相同错误1')]
    public const SAME_ERROR1 = 100016;

    #[msg('相同错误2')]
    public const SAME_ERROR2 = 100016;

    #[status('Status enabled')]
    public const STATUS_ENABLE = 1;

    #[status('Status disabled')]
    public const STATUS_DISABLE = 0;

    #[accounts_type('管理员账号')]
    public const ACCOUNTS_TYPE_MANAGER = 'manager';

    #[accounts_type('供应商账号')]
    public const ACCOUNTS_TYPE_SUPPLIER = 'supplier';

    #[accounts_type('经销商账号')]
    public const ACCOUNTS_TYPE_AGENCY = 'agency';

    #[type('Type enabled')]
    public const TYPE_ENABLE = 1;

    #[type('Type disabled')]
    public const TYPE_DISABLE = 0;

    #[type('Type bool true')]
    public const TYPE_BOOL_TRUE = true;

    #[type('Type bool false')]
    public const TYPE_BOOL_FALSE = false;

    #[type('Type int')]
    public const TYPE_INT = 11;

    #[type('Type float')]
    public const TYPE_FLOAT = 1.1;

    #[type('Type string float')]
    public const TYPE_STRING_FLOAT = '1.1';

    #[type('Type string')]
    public const TYPE_STRING = 'string';

    #[type('Type null')]
    public const TYPE_NULL = null;
}
