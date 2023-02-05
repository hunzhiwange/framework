<?php

declare(strict_types=1);

namespace Leevel\Database;

/**
 * 主键或唯一键重复异常.
 *
 * 用于模拟数据库 replace.
 */
class DuplicateKeyException extends \PDOException
{
}
