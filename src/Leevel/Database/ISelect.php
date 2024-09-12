<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Support\IFlowControl;

/**
 * 数据库查询器接口.
 */
interface ISelect extends ICondition, IFlowControl {}
