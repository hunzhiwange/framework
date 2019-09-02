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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Kernel\Utils;

use ReflectionClass;
use ReflectionParameter;

/**
 * IDE 生成.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.09.01
 *
 * @version 1.0
 *
 * @todo 为本功能编写单元测试用例
 * @codeCoverageIgnore
 */
class IdeHelper
{
    /**
     * 解析类 @method 方法签名.
     *
     * @param string $className
     *
     * @return string
     */
    public function handle(string $className): string
    {
        $result = [];
        foreach ($this->normalizeMethod($className) as $method) {
            $result[] = $this->packageMethod($method);
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * 整理方法内容.
     *
     * @param string $className
     *
     * @return array
     */
    protected function normalizeMethod(string $className): array
    {
        $reflectionClass = new ReflectionClass($className);
        $methodsResult = [];

        foreach ($reflectionClass->getMethods() as $method) {
            if (!$method->isPublic()) {
                continue;
            }

            $description = $this->parseDescription($method->getDocComment());

            $params = [];
            foreach ($method->getParameters() as $param) {
                $params[] = $this->normalizeParam($param);
            }

            $returnTypeResult = null;
            if ($returnType = $method->getReturnType()) {
                $returnTypeResult = (string) $returnType;
                if (!$returnType->isBuiltin()) {
                    $returnTypeResult = '\\'.$returnTypeResult;
                }
            }

            $methodsResult[] = [
                'name'        => $method->name,
                'params'      => $params,
                'return_type' => $returnTypeResult,
                'description' => $description,
            ];
        }

        return $methodsResult;
    }

    /**
     * 整理方法参数.
     *
     * @param \ReflectionParameter $param
     *
     * @return string
     */
    protected function normalizeParam(ReflectionParameter $param): string
    {
        $paramClassName = null;
        if ($paramClass = $param->getClass()) {
            $paramClassName = $paramClass->getName();
        }

        $result = (string) $param;
        $result = substr($result, strpos($result, '<'));
        $result = rtrim($result, '] ');
        $result = str_replace(['<required> ', '<optional> ', ' = Array'], ['', '', ' = []'], $result);
        $result = str_replace('?...', '...', $result);

        if ($paramClassName) {
            $result = '\\'.$result;
        }

        if (false !== strpos($result, 'or NULL')) {
            $result = '?'.str_replace(' or NULL', '', $result);
        }

        return $result;
    }

    /**
     * 组装一个方法签名.
     *
     * - 用于 @method 标准签名
     *
     * @param array $method
     *
     * @return string
     */
    protected function packageMethod(array $method): string
    {
        $result = [];
        $result[] = ' * @method static';
        if (isset($method['return_type'])) {
            $result[] = $method['return_type'];
        }
        $result[] = $method['name'].'('.implode(', ', $method['params']).')';
        $result[] = $method['description'];

        return implode(' ', $result);
    }

    /**
     * 获取一个方法的描述.
     *
     * @param string $docComment
     *
     * @return string
     */
    protected function parseDescription(string $docComment): string
    {
        $docComment = trim($docComment);
        if (!$docComment) {
            return '';
        }

        $description = '';
        foreach (explode(PHP_EOL, $docComment) as $k => $v) {
            if (0 === $k) {
                continue;
            }

            $v = trim($v, '* ');
            if ($v) {
                $description .= $v.' ';
            } else {
                break;
            }
        }

        return $description;
    }
}
