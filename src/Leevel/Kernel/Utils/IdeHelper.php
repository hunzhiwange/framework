<?php

declare(strict_types=1);

namespace Leevel\Kernel\Utils;

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
     * 解析组手方法 handle 方法签名.
     */
    public function handleClassFunction(array $classNames): string
    {
        $result = [];
        foreach ($this->normalizeClassFunction($classNames) as $v) {
            $result[] = $this->packageMethod($v);
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * 整理函数内容.
     */
    protected function normalizeClassFunction(array $classNames): array
    {
        $result = [];
        foreach ($classNames as $v) {
            $result[] = $this->getReflectorInfo(new \ReflectionMethod($v, 'handle'), true);
        }

        return $result;
    }

    /**
     * 整理方法内容.
     */
    protected function normalizeMethod(string $className): array
    {
        $result = [];
        // @phpstan-ignore-next-line
        foreach ((new \ReflectionClass($className))->getMethods() as $method) {
            if (!$method->isPublic() || str_starts_with($method->getName(), '__')) {
                continue;
            }
            $result[] = $this->getReflectorInfo($method);
        }

        return $result;
    }

    /**
     * 获取反射信息.
     */
    protected function getReflectorInfo(\ReflectionFunctionAbstract $reflector, bool $isClassFunction = false): array
    {
        return [
            'name' => $this->getReflectorName($reflector, $isClassFunction),
            'params' => $this->getReflectorParams($reflector),
            'params_name' => $this->getReflectorParams($reflector, true),
            'return_type' => $this->getReflectorReturnType($reflector),
            'description' => $this->getReflectorDescription($reflector),
            'define' => !$isClassFunction ? Doc::getMethodBody($this->convertReflectionMethod($reflector)->class, $reflector->getName(), 'define', false) : '',
        ];
    }

    /**
     * 转换为 \ReflectionMethod.
     */
    protected function convertReflectionMethod(\ReflectionFunctionAbstract $reflectionMethod): \ReflectionMethod
    {
        // @phpstan-ignore-next-line
        return $reflectionMethod;
    }

    /**
     * 获取反射名字.
     */
    protected function getReflectorName(\ReflectionFunctionAbstract $reflector, bool $isClassFunction = false): string
    {
        if (!$isClassFunction) {
            return $reflector->getName();
        }

        $name = $reflector->class;
        $name = explode('\\', $name);
        $name = (string) array_pop($name);

        return lcfirst($name);
    }

    /**
     * 获取反射参数.
     */
    protected function getReflectorParams(\ReflectionFunctionAbstract $reflector, bool $onlyReturnName = false): array
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
    protected function getReflectorDescription(\ReflectionFunctionAbstract $reflector): string
    {
        return $this->parseDescription($reflector->getDocComment() ?: '');
    }

    /**
     * 获取反射返回值类型.
     */
    protected function getReflectorReturnType(\ReflectionFunctionAbstract $reflector): string
    {
        if (!($returnType = $reflector->getReturnType())) {
            return '';
        }

        // \ReflectionUnionType 不存在 isBuiltin 和 getName 方法
        if ($returnType instanceof \ReflectionUnionType) {
            return (string) $returnType;
        }

        return ($returnType->allowsNull() && 'mixed' !== $returnType->getName() ? '?' : ''). // @phpstan-ignore-line
            (!$returnType->isBuiltin() ? '\\' : ''). // @phpstan-ignore-line
            $returnType->getName(); // @phpstan-ignore-line
    }

    /**
     * 整理方法参数.
     */
    protected function normalizeParam(\ReflectionParameter $param, bool $onlyReturnName = false): string
    {
        if ($onlyReturnName) {
            return $param->name;
        }

        // \ReflectionUnionType 不存在 isBuiltin 和 getName 方法
        $paramClassName = null;
        if (($reflectionType = $param->getType())
            && !$reflectionType instanceof \ReflectionUnionType
            && false === $reflectionType->isBuiltin()) { /** @phpstan-ignore-line */
            $paramClassName = $reflectionType->getName(); // @phpstan-ignore-line
        }

        $result = (string) $param;

        /** @phpstan-ignore-next-line */
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

        if (str_contains($result, 'or NULL')) {
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
