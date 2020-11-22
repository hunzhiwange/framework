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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database\Ddd;

/**
 * 实体 Getter Setter 属性风格.
 */
trait GetterSetterProp
{
    /**
     * Database connect.
     */
    private static ?string $connect = null;

    /**
     * Setter.
     */
    public function setter(string $prop, mixed $value): Entity
    {
        $this->{'_'.$this->realProp($prop)} = $value;

        return $this;
    }

    /**
     * Getter.
     */
    public function getter(string $prop): mixed
    {
        return $this->{'_'.$this->realProp($prop)};
    }

    /**
     * Set database connect.
     */
    public static function withConnect(?string $connect = null): void
    {
        static::$connect = $connect;
    }

    /**
     * Get database connect.
     */
    public static function connect(): ?string
    {
        return static::$connect;
    }
}
