<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

class Filter
{
    /**
     * 返回过滤后的数据.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(array $input, array $rules): array
    {
        foreach ($input as $k => &$v) {
            if (\is_string($v)) {
                $v = trim($v);
            }

            if (isset($rules[$k])) {
                $rule = $rules[$k];
                if (!\is_array($rule)) {
                    $e = sprintf('Rule of `%s` must be an array.', $k);

                    throw new \InvalidArgumentException($e);
                }

                foreach ($rule as $r) {
                    if ('must' === $r) {
                        continue;
                    }

                    if (!\is_callable($r)) {
                        $e = sprintf('Rule item of `%s` must be a callback type.', $k);

                        throw new \InvalidArgumentException($e);
                    }

                    if (null !== $v || \in_array('must', $rule, true)) {
                        $v = $r($v);
                    }
                }
            }
        }

        return $input;
    }
}
