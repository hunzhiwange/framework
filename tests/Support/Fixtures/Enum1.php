<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Enum;
use Leevel\Support\Msg;

class Enum1
{
    use Enum;

    #[Msg('错误类型一')]
    public const int ERROR_ONE = 100010;

    #[Msg('自定义错误')]
    public const int CUSTOM_ERROR = 100011;

    public const int NO_ATTRIBUTES = 100013;

    #[Msg]
    public const int NO_MSG = 100014;

    #[Msg('Hello %s world')]
    public const int PARAMS_INVALID = 100015;

    #[Msg('相同错误1')]
    public const int SAME_ERROR1 = 100016;

    #[Msg('相同错误2')]
    public const int SAME_ERROR2 = 100016;

    #[status('Status enabled')]
    public const int STATUS_ENABLE = 1;

    #[status('Status disabled')]
    public const int STATUS_DISABLE = 0;

    #[accounts_type('管理员账号')]
    public const string ACCOUNTS_TYPE_MANAGER = 'manager';

    #[accounts_type('供应商账号')]
    public const string ACCOUNTS_TYPE_SUPPLIER = 'supplier';

    #[accounts_type('经销商账号')]
    public const string ACCOUNTS_TYPE_AGENCY = 'agency';

    #[type('Type enabled')]
    public const int TYPE_ENABLE = 1;

    #[type('Type disabled')]
    public const int TYPE_DISABLE = 0;

    #[type('Type bool true')]
    public const bool TYPE_BOOL_TRUE = true;

    #[type('Type bool false')]
    public const bool TYPE_BOOL_FALSE = false;

    #[type('Type int')]
    public const int TYPE_INT = 11;

    #[type('Type float')]
    public const float TYPE_FLOAT = 1.1;

    #[type('Type string float')]
    public const string TYPE_STRING_FLOAT = '1.1';

    #[type('Type string')]
    public const string TYPE_STRING = 'string';

    #[type('Type null')]
    public const null TYPE_NULL = null;
}
