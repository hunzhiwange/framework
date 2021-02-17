<?php

declare(strict_types=1);

namespace Leevel\Kernel\Utils;

use function Leevel\Support\Str\camelize;
use Leevel\Support\Str\camelize;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionUnionType;

/**
 * IDE 生成.
 */
class IdeHelper
{
    /**
     * 解析类 @method 方法签名.
     */
    public function handle(string $className): string
    {
        $result = [];
        foreach ($this->normalizeMethod($className) as $v) {
            $result[] = $this->packageMethod($v);
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * 解析函数 @method 方法签名.
     */
    public function handleFunction(array $functionName): string
    {
        $result = [];
        foreach ($this->normalizeFunction($functionName) as $v) {
            $result[] = $this->packageMethod($v);
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * 整理函数内容.
     */
    protected function normalizeFunction(array $functionName): array
    {
        $result = [];
        foreach ($functionName as $v) {
            $result[] = $this->getReflectorInfo(new ReflectionFunction($v), true);
        }

        return $result;
    }

    /**
     * 整理方法内容.
     */
    protected function normalizeMethod(string $className): array
    {
        $result = [];
        foreach ((new ReflectionClass($className))->getMethods() as $method) {
            if (!$method->isPublic() || 0 === strpos($method->getName(), '__')) {
                continue;
            }
            $result[] = $this->getReflectorInfo($method);
        }

        return $result;
    }

    /**
     * 获取反射信息.
     */
    protected function getReflectorInfo(ReflectionFunctionAbstract $reflector, bool $isFunction = false): array
    {
        return [
            'name'        => $this->getReflectorName($reflector, $isFunction),
            'params'      => $this->getReflectorParams($reflector),
            'params_name' => $this->getReflectorParams($reflector, true),
            'return_type' => $this->getReflectorReturnType($reflector),
            'description' => $this->getReflectorDescription($reflector),
            'define'      => !$isFunction ? Doc::getMethodBody($this->convertReflectionMethod($reflector)->class, $reflector->getName(), 'define', false) : '',
        ];
    }

    /**
     * 转换为 \ReflectionMethod.
     */
    protected function convertReflectionMethod(ReflectionFunctionAbstract $reflectionMethod): ReflectionMethod
    {
        return $reflectionMethod;
    }

    /**
     * 获取反射名字.
     */
    protected function getReflectorName(ReflectionFunctionAbstract $reflector, bool $isFunction = false): string
    {
        $name = $reflector->getName();
        if (!$isFunction) {
            return $name;
        }

        $name = explode('\\', $name);
        $name = (string) array_pop($name);

        return camelize($name);
    }

    /**
     * 获取反射参数.
     */
    protected function getReflectorParams(ReflectionFunctionAbstract $reflector, bool $onlyReturnName = false): array
    {
        $params = [];
        foreach ($reflector->getParameters() as $param) {
            $params[] = $this->normalizeParam($param, $onlyReturnName);
        }

        return $params;
    }

    /**
     * 获取反射描述.
     */
    protected function getReflectorDescription(ReflectionFunctionAbstract $reflector): string
    {
        return $this->parseDescription($reflector->getDocComment() ?: '');
    }

    /**
     * 获取反射返回值类型.
     */
    protected function getReflectorReturnType(ReflectionFunctionAbstract $reflector): string
    {
        if (!($returnType = $reflector->getReturnType())) {
            return '';
        }

        // \ReflectionUnionType 不存在 isBuiltin 和 getName 方法
        if ($returnType instanceof ReflectionUnionType) {
            return (string) $returnType;
        }

        return ($returnType->allowsNull() && 'mixed' !== $returnType->getName() ? '?' : '').
            (!$returnType->isBuiltin() ? '\\' : '').
            $returnType->getName();
    }

    /**
     * 整理方法参数.
     */
    protected function normalizeParam(ReflectionParameter $param, bool $onlyReturnName = false): string
    {
        if ($onlyReturnName) {
            return $param->name;
        }

        // \ReflectionUnionType 不存在 isBuiltin 和 getName 方法
        $paramClassName = null;
        if (($reflectionType = $param->getType()) && 
            !$reflectionType instanceof ReflectionUnionType &&
            false === $reflectionType->isBuiltin()) {
            $paramClassName = $reflectionType->getName();
        }

        $result = (string) $param;
        $result = substr($result, strpos($result, '<'));
        $result = rtrim($result, '] ');
        $result = str_replace(
            ['<required> ', '<optional> ', ' = Array', '= NULL'],
            ['', '', ' = []', '= null'],
            $result,
        );
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
     */
    protected function packageMethod(array $method): string
    {
        $result = [];
        $result[] = ' * @method static';
        if ($method['return_type']) {
            $result[] = $method['return_type'];
        } else {
            $result[] = 'mixed';
        }
        $result[] = $method['name'].'('.implode(', ', $method['params']).')';
        $result[] = $method['description'];

        return implode(' ', $result);
    }

    /**
     * 获取一个方法的描述.
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

            $v = trim($v, '/* ');
            if ($v) {
                $description .= $v.' ';
            } else {
                break;
            }
        }

        return $description;
    }
}

// import fn.
class_exists(camelize::class);
