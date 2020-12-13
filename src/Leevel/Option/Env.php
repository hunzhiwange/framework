<?php

declare(strict_types=1);

namespace Leevel\Option;

use Closure;

/**
 * 环境变量.
 */
trait Env
{
    /**
     * 载入环境变量数据.
     */
    protected function setEnvVars(array $envVars, ?Closure $call = null): void
    {
        foreach ($envVars as $name => $value) {
            $this->setEnvVar($name, $value);
            if ($call) {
                $call($name, $value);
            }
        }
    }

    /**
     * 设置环境变量.
     */
    protected function setEnvVar(string $name, null|bool|string $value = null): void
    {
        if (is_bool($value)) {
            putenv($name.'='.($value ? '(true)' : '(false)'));
        } elseif (null === $value) {
            putenv($name.'(null)');
        } else {
            putenv($name.'='.$value);
        }
    }
}
