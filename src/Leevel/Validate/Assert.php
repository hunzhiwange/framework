<?php

declare(strict_types=1);

namespace Leevel\Validate;

use Leevel\I18n\Gettext;
use Leevel\Support\Str\UnCamelize;

/**
 * 断言.
 *
 * - 提供一套精简版本的断言方便业务中很愉快地使用。
 * - 断言和验证器复用同样的验证规则。
 */
class Assert
{
    /**
     * 校验值.
     */
    protected mixed $value = null;

    /**
     * 字段名字.
     */
    protected ?string $field = null;

    /**
     * 是否延后提示错误.
     */
    protected bool $lazy = false;

    /**
     * 是否验证所有.
     */
    protected bool $all = true;

    /**
     * 验证错误消息.
     */
    protected array $error = [];

    /**
     * 是否为多选值.
     */
    protected bool $multiValue = false;

    /**
     * 默认验证提示信息.
     */
    protected static array $defaultMessages = [];

    /**
     * 构造函数.
     */
    public function __construct(mixed $value, bool $multiValue = false, ?string $field = null, bool $all = true, bool $lazy = false)
    {
        $this->value = $value;
        $this->multiValue = $multiValue;
        $this->field = $field;
        $this->all = $all;
        $this->lazy = $lazy;
    }

    /**
     * 是否可接受的.
     */
    public function accepted(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证 IP 许可.
     */
    public function allowedIp(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为英文字母.
     */
    public function alpha(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 字符串是否为数字、下划线、短横线和字母.
     */
    public function alphaDash(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为小写英文字母.
     */
    public function alphaLower(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 字符串是否为数字和字母.
     */
    public function alphaNum(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为大写英文字母.
     */
    public function alphaUpper(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 处于 between 范围，不包含等于.
     */
    public function between(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 处于 betweenEqual 范围，包含全等.
     */
    public function betweenEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否为布尔值
     */
    public function boolean(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为有效的域名.
     */
    public function checkdnsrr(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文.
     */
    public function chinese(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文、数字、下划线、短横线和字母.
     */
    public function chineseAlphaDash(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文、数字和字母.
     */
    public function chineseAlphaNum(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为日期
     */
    public function date(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为时间.
     */
    public function dateFormat(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证 IP 许可.
     */
    public function denyIp(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 检测字符串中的字符是否都是数字，负数和小数会检测不通过.
     */
    public function digit(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否双精度浮点数.
     */
    public function double(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为电子邮件.
     */
    public function email(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否相同.
     */
    public function equal(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 大于或者全等.
     */
    public function equalGreaterThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 小于或者全等.
     */
    public function equalLessThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 大于.
     */
    public function greaterThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为大陆身份证
     */
    public function idCard(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否处于某个范围.
     */
    public function in(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否整型数字.
     */
    public function integer(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为合法的 IP 地址
     */
    public function ip(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 ipv4.
     */
    public function ipv4(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 ipv6.
     */
    public function ipv6(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为数组.
     */
    public function isArray(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为空.
     */
    public function isEmpty(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为浮点数.
     */
    public function isFloat(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 null.
     */
    public function isNull(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为正常的 JSON 字符串.
     */
    public function json(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 小于.
     */
    public function lessThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否都是小写.
     */
    public function lower(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为银行卡等符合 luhn 算法.
     */
    public function luhn(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证值上限.
     */
    public function max(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证数据最大长度.
     */
    public function maxLength(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证值下限.
     */
    public function min(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证数据最小长度.
     */
    public function minLength(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否为手机号码
     */
    public function mobile(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 未处于 between 范围，不包含等于.
     */
    public function notBetween(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 未处于 betweenEqual 范围，包含等于.
     */
    public function notBetweenEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否不为空.
     */
    public function notEmpty(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否不相同.
     */
    public function notEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否不处于某个范围.
     */
    public function notIn(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否不为 null.
     */
    public function notNull(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否不完全相同.
     */
    public function notSame(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为数字.
     */
    public function number(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为电话号码或者手机号码
     */
    public function phone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 QQ 号码
     */
    public function qq(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 数据是否满足正则条件.
     */
    public function regex(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 不能为空.
     */
    public function required(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否完全相同.
     */
    public function same(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 长度验证
     */
    public function strlen(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否为电话号码
     */
    public function telephone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为正确的时区.
     */
    public function timezone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 数据类型验证
     */
    public function type(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否都是大写.
     */
    public function upper(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为 URL 地址
     */
    public function url(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中国邮政编码
     */
    public function zipCode(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否可接受的（可选验证）.
     */
    public function optionalAccepted(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证 IP 许可（可选验证）.
     */
    public function optionalAllowedIp(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为英文字母（可选验证）.
     */
    public function optionalAlpha(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 字符串是否为数字、下划线、短横线和字母（可选验证）.
     */
    public function optionalAlphaDash(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为小写英文字母（可选验证）.
     */
    public function optionalAlphaLower(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 字符串是否为数字和字母（可选验证）.
     */
    public function optionalAlphaNum(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为大写英文字母（可选验证）.
     */
    public function optionalAlphaUpper(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 处于 between 范围，不包含等于（可选验证）.
     */
    public function optionalBetween(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 处于 betweenEqual 范围，包含全等（可选验证）.
     */
    public function optionalBetweenEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否为布尔值（可选验证）.
     */
    public function optionalBoolean(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为有效的域名（可选验证）.
     */
    public function optionalCheckdnsrr(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文（可选验证）.
     */
    public function optionalChinese(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文、数字、下划线、短横线和字母（可选验证）.
     */
    public function optionalChineseAlphaDash(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文、数字和字母（可选验证）.
     */
    public function optionalChineseAlphaNum(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为日期（可选验证）.
     */
    public function optionalDate(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为时间（可选验证）.
     */
    public function optionalDateFormat(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证 IP 许可（可选验证）.
     */
    public function optionalDenyIp(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 检测字符串中的字符是否都是数字，负数和小数会检测不通过（可选验证）.
     */
    public function optionalDigit(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否双精度浮点数（可选验证）.
     */
    public function optionalDouble(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为电子邮件（可选验证）.
     */
    public function optionalEmail(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否相同（可选验证）.
     */
    public function optionalEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 大于或者全等（可选验证）.
     */
    public function optionalEqualGreaterThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 小于或者全等（可选验证）.
     */
    public function optionalEqualLessThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 大于（可选验证）.
     */
    public function optionalGreaterThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为大陆身份证（可选验证）.
     */
    public function optionalIdCard(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否处于某个范围（可选验证）.
     */
    public function optionalIn(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否整型数字（可选验证）.
     */
    public function optionalInteger(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为合法的 IP 地址（可选验证）.
     */
    public function optionalIp(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 ipv4（可选验证）.
     */
    public function optionalIpv4(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 ipv6（可选验证）.
     */
    public function optionalIpv6(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为数组（可选验证）.
     */
    public function optionalIsArray(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为空（可选验证）.
     */
    public function optionalIsEmpty(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为浮点数（可选验证）.
     */
    public function optionalIsFloat(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 null（可选验证）.
     */
    public function optionalIsNull(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为正常的 JSON 字符串（可选验证）.
     */
    public function optionalJson(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 小于（可选验证）.
     */
    public function optionalLessThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否都是小写（可选验证）.
     */
    public function optionalLower(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为银行卡等符合 luhn 算法（可选验证）.
     */
    public function optionalLuhn(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证值上限（可选验证）.
     */
    public function optionalMax(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证数据最大长度（可选验证）.
     */
    public function optionalMaxLength(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证值下限（可选验证）.
     */
    public function optionalMin(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证数据最小长度（可选验证）.
     */
    public function optionalMinLength(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否为手机号码（可选验证）.
     */
    public function optionalMobile(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 未处于 between 范围，不包含等于（可选验证）.
     */
    public function optionalNotBetween(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 未处于 betweenEqual 范围，包含等于（可选验证）.
     */
    public function optionalNotBetweenEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否不为空（可选验证）.
     */
    public function optionalNotEmpty(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否不相同（可选验证）.
     */
    public function optionalNotEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否不处于某个范围（可选验证）.
     */
    public function optionalNotIn(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否不为 null.
     */
    public function optionalNotNull(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否不完全相同（可选验证）.
     */
    public function optionalNotSame(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为数字（可选验证）.
     */
    public function optionalNumber(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为电话号码或者手机号码（可选验证）.
     */
    public function optionalPhone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 QQ 号码（可选验证）.
     */
    public function optionalQq(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 数据是否满足正则条件（可选验证）.
     */
    public function optionalRegex(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 不能为空（可选验证）.
     */
    public function optionalRequired(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否完全相同（可选验证）.
     */
    public function optionalSame(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 长度验证（可选验证）.
     */
    public function optionalStrlen(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否为电话号码（可选验证）.
     */
    public function optionalTelephone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为正确的时区（可选验证）.
     */
    public function optionalTimezone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 数据类型验证（可选验证）.
     */
    public function optionalType(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否都是大写（可选验证）.
     */
    public function optionalUpper(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为 URL 地址（可选验证）.
     */
    public function optionalUrl(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中国邮政编码（可选验证）.
     */
    public function optionalZipCode(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否可接受的（多校验场景）.
     */
    public function multiAccepted(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证 IP 许可（多校验场景）.
     */
    public function multiAllowedIp(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为英文字母（多校验场景）.
     */
    public function multiAlpha(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 字符串是否为数字、下划线、短横线和字母（多校验场景）.
     */
    public function multiAlphaDash(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为小写英文字母（多校验场景）.
     */
    public function multiAlphaLower(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 字符串是否为数字和字母（多校验场景）.
     */
    public function multiAlphaNum(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为大写英文字母（多校验场景）.
     */
    public function multiAlphaUpper(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 处于 between 范围，不包含等于（多校验场景）.
     */
    public function multiBetween(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 处于 betweenEqual 范围，包含全等（多校验场景）.
     */
    public function multiBetweenEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否为布尔值（多校验场景）.
     */
    public function multiBoolean(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为有效的域名（多校验场景）.
     */
    public function multiCheckdnsrr(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文（多校验场景）.
     */
    public function multiChinese(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文、数字、下划线、短横线和字母（多校验场景）.
     */
    public function multiChineseAlphaDash(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文、数字和字母（多校验场景）.
     */
    public function multiChineseAlphaNum(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为日期（多校验场景）.
     */
    public function multiDate(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为时间（多校验场景）.
     */
    public function multiDateFormat(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证 IP 许可（多校验场景）.
     */
    public function multiDenyIp(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 检测字符串中的字符是否都是数字，负数和小数会检测不通过（多校验场景）.
     */
    public function multiDigit(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否双精度浮点数（多校验场景）.
     */
    public function multiDouble(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为电子邮件（多校验场景）.
     */
    public function multiEmail(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否相同（多校验场景）.
     */
    public function multiEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 大于或者全等（多校验场景）.
     */
    public function multiEqualGreaterThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 小于或者全等（多校验场景）.
     */
    public function multiEqualLessThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 大于（多校验场景）.
     */
    public function multiGreaterThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为大陆身份证（多校验场景）.
     */
    public function multiIdCard(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否处于某个范围（多校验场景）.
     */
    public function multiIn(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否整型数字（多校验场景）.
     */
    public function multiInteger(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为合法的 IP 地址（多校验场景）.
     */
    public function multiIp(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 ipv4（多校验场景）.
     */
    public function multiIpv4(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 ipv6（多校验场景）.
     */
    public function multiIpv6(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为数组（多校验场景）.
     */
    public function multiIsArray(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为空（多校验场景）.
     */
    public function multiIsEmpty(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为浮点数（多校验场景）.
     */
    public function multiIsFloat(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 null（多校验场景）.
     */
    public function multiIsNull(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为正常的 JSON 字符串（多校验场景）.
     */
    public function multiJson(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 小于（多校验场景）.
     */
    public function multiLessThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否都是小写（多校验场景）.
     */
    public function multiLower(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为银行卡等符合 luhn 算法（多校验场景）.
     */
    public function multiLuhn(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证值上限（多校验场景）.
     */
    public function multiMax(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证数据最大长度（多校验场景）.
     */
    public function multiMaxLength(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证值下限（多校验场景）.
     */
    public function multiMin(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证数据最小长度（多校验场景）.
     */
    public function multiMinLength(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否为手机号码（多校验场景）.
     */
    public function multiMobile(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 未处于 between 范围，不包含等于（多校验场景）.
     */
    public function multiNotBetween(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 未处于 betweenEqual 范围，包含等于（多校验场景）.
     */
    public function multiNotBetweenEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否不为空（多校验场景）.
     */
    public function multiNotEmpty(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否不相同（多校验场景）.
     */
    public function multiNotEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否不处于某个范围（多校验场景）.
     */
    public function multiNotIn(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否不为 null（多校验场景）.
     */
    public function multiNotNull(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否不完全相同（多校验场景）.
     */
    public function multiNotSame(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为数字（多校验场景）.
     */
    public function multiNumber(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为电话号码或者手机号码（多校验场景）.
     */
    public function multiPhone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 QQ 号码（多校验场景）.
     */
    public function multiQq(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 数据是否满足正则条件（多校验场景）.
     */
    public function multiRegex(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 不能为空（多校验场景）.
     */
    public function multiRequired(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否完全相同（多校验场景）.
     */
    public function multiSame(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 长度验证（多校验场景）.
     */
    public function multiStrlen(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否为电话号码（多校验场景）.
     */
    public function multiTelephone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为正确的时区（多校验场景）.
     */
    public function multiTimezone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 数据类型验证（多校验场景）.
     */
    public function multiType(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否都是大写（多校验场景）.
     */
    public function multiUpper(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为 URL 地址（多校验场景）.
     */
    public function multiUrl(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中国邮政编码（多校验场景）.
     */
    public function multiZipCode(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否可接受的（可选验证，多校验场景）.
     */
    public function optionalMultiAccepted(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证 IP 许可（可选验证，多校验场景）.
     */
    public function optionalMultiAllowedIp(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为英文字母（可选验证，多校验场景）.
     */
    public function optionalMultiAlpha(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 字符串是否为数字、下划线、短横线和字母（可选验证，多校验场景）.
     */
    public function optionalMultiAlphaDash(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为小写英文字母（可选验证，多校验场景）.
     */
    public function optionalMultiAlphaLower(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 字符串是否为数字和字母（可选验证，多校验场景）.
     */
    public function optionalMultiAlphaNum(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为大写英文字母（可选验证，多校验场景）.
     */
    public function optionalMultiAlphaUpper(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 处于 between 范围，不包含等于（可选验证，多校验场景）.
     */
    public function optionalMultiBetween(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 处于 betweenEqual 范围，包含全等（可选验证，多校验场景）.
     */
    public function optionalMultiBetweenEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否为布尔值（可选验证，多校验场景）.
     */
    public function optionalMultiBoolean(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为有效的域名（可选验证，多校验场景）.
     */
    public function optionalMultiCheckdnsrr(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文（可选验证，多校验场景）.
     */
    public function optionalMultiChinese(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文、数字、下划线、短横线和字母（可选验证，多校验场景）.
     */
    public function optionalMultiChineseAlphaDash(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中文、数字和字母（可选验证，多校验场景）.
     */
    public function optionalMultiChineseAlphaNum(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为日期（可选验证，多校验场景）.
     */
    public function optionalMultiDate(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为时间（可选验证，多校验场景）.
     */
    public function optionalMultiDateFormat(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证 IP 许可（可选验证，多校验场景）.
     */
    public function optionalMultiDenyIp(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 检测字符串中的字符是否都是数字，负数和小数会检测不通过（可选验证，多校验场景）.
     */
    public function optionalMultiDigit(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否双精度浮点数（可选验证，多校验场景）.
     */
    public function optionalMultiDouble(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为电子邮件（可选验证，多校验场景）.
     */
    public function optionalMultiEmail(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否相同（可选验证，多校验场景）.
     */
    public function optionalMultiEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 大于或者全等（可选验证，多校验场景）.
     */
    public function optionalMultiEqualGreaterThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 小于或者全等（可选验证，多校验场景）.
     */
    public function optionalMultiEqualLessThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 大于（可选验证，多校验场景）.
     */
    public function optionalMultiGreaterThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为大陆身份证（可选验证，多校验场景）.
     */
    public function optionalMultiIdCard(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否处于某个范围（可选验证，多校验场景）.
     */
    public function optionalMultiIn(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否整型数字（可选验证，多校验场景）.
     */
    public function optionalMultiInteger(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为合法的 IP 地址（可选验证，多校验场景）.
     */
    public function optionalMultiIp(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 ipv4（可选验证，多校验场景）.
     */
    public function optionalMultiIpv4(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 ipv6（可选验证，多校验场景）.
     */
    public function optionalMultiIpv6(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为数组（可选验证，多校验场景）.
     */
    public function optionalMultiIsArray(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为空（可选验证，多校验场景）.
     */
    public function optionalMultiIsEmpty(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为浮点数（可选验证，多校验场景）.
     */
    public function optionalMultiIsFloat(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 null（可选验证，多校验场景）.
     */
    public function optionalMultiIsNull(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为正常的 JSON 字符串（可选验证，多校验场景）.
     */
    public function optionalMultiJson(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 小于（可选验证，多校验场景）.
     */
    public function optionalMultiLessThan(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否都是小写（可选验证，多校验场景）.
     */
    public function optionalMultiLower(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为银行卡等符合 Luhn 算法（可选验证，多校验场景）.
     */
    public function optionalMultiLuhn(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证值上限（可选验证，多校验场景）.
     */
    public function optionalMultiMax(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证数据最大长度（可选验证，多校验场景）.
     */
    public function optionalMultiMaxLength(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证值下限（可选验证，多校验场景）.
     */
    public function optionalMultiMin(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证数据最小长度（可选验证，多校验场景）.
     */
    public function optionalMultiMinLength(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否为手机号码（可选验证，多校验场景）.
     */
    public function optionalMultiMobile(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 未处于 between 范围，不包含等于（可选验证，多校验场景）.
     */
    public function optionalMultiNotBetween(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 未处于 betweenEqual 范围，包含等于（可选验证，多校验场景）.
     */
    public function optionalMultiNotBetweenEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否不为空（可选验证，多校验场景）.
     */
    public function optionalMultiNotEmpty(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否不相同（可选验证，多校验场景）.
     */
    public function optionalMultiNotEqual(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否不处于某个范围（可选验证，多校验场景）.
     */
    public function optionalMultiNotIn(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否不为 null（可选验证，多校验场景）.
     */
    public function optionalMultiNotNull(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否不完全相同（可选验证，多校验场景）.
     */
    public function optionalMultiNotSame(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 是否为数字（可选验证，多校验场景）.
     */
    public function optionalMultiNumber(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 值是否为电话号码或者手机号码（可选验证，多校验场景）.
     */
    public function optionalMultiPhone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为 QQ 号码（可选验证，多校验场景）.
     */
    public function optionalMultiQq(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 数据是否满足正则条件（可选验证，多校验场景）.
     */
    public function optionalMultiRegex(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 不能为空（可选验证，多校验场景）.
     */
    public function optionalMultiRequired(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 两个值是否完全相同（可选验证，多校验场景）.
     */
    public function optionalMultiSame(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 长度验证（可选验证，多校验场景）.
     */
    public function optionalMultiStrlen(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 值是否为电话号码（可选验证，多校验场景）.
     */
    public function optionalMultiTelephone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为正确的时区（可选验证，多校验场景）.
     */
    public function optionalMultiTimezone(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 数据类型验证（可选验证，多校验场景）.
     */
    public function optionalMultiType(array $param, ?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $param, $message);
    }

    /**
     * 验证是否都是大写（可选验证，多校验场景）.
     */
    public function optionalMultiUpper(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 验证是否为 URL 地址（可选验证，多校验场景）.
     */
    public function optionalMultiUrl(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * 是否为中国邮政编码（可选验证，多校验场景）.
     */
    public function optionalMultiZipCode(?string $message = null): self
    {
        return $this->callMethodChain(__FUNCTION__, $message);
    }

    /**
     * @throws AssertException
     */
    public function callMethodChain(string $method, mixed ...$args): self
    {
        if (false === $this->all && $this->error) {
            return $this;
        }

        array_unshift($args, $this->value);

        if (false === self::validateAssert($method, $args, $this->multiValue)) {
            $message = self::normalizeMessage($method, $args, $this->field);
            if (false === $this->lazy) {
                throw new AssertException($message);
            }
            $this->error[] = $message;
        }

        return $this;
    }

    /**
     * 创建断言对象已支持链式表达式.
     *
     * @example
     * Assert::make($value)
     *     ->notEmpty()
     *     ->lessThan([7]);
     */
    public static function make(mixed $value, bool $multiValue = false, ?string $field = null, bool $all = true, bool $lazy = false): static
    {
        return new static($value,  $multiValue,  $field ,  $all ,  $lazy);
    }

    /**
     * 创建断言对象延迟抛出错误.
     *
     * @example
     * Assert::lazy($value)
     *     ->notEmpty()
     *     ->lessThan([7])
     *     ->flush();
     */
    public static function lazy(mixed $value, bool $multiValue = false, ?string $field = null, bool $all = true): static
    {
        return new static($value,  $multiValue,  $field ,  $all ,  true);
    }

    /**
     * 释放并抛出验证.
     *
     * @throws AssertException
     */
    public function flush(?\Closure $format = null): bool
    {
        if ($this->error) {
            if (!$format) {
                $format = static function (array $error): string {
                    // @phpstan-ignore-next-line
                    return json_encode($error, JSON_UNESCAPED_UNICODE);
                };
            }

            throw new AssertException($format($this->error));
        }

        return true;
    }

    /**
     * 校验断言.
     *
     * @throws \InvalidArgumentException
     */
    protected static function validateAssert(string $method, array $args, bool $multiValue): bool
    {
        // 匹配可选
        if (true === ($result = self::matchOptional($method, $args))) {
            return true;
        }

        // @phpstan-ignore-next-line
        [$method, $optional] = $result;

        if (0 === stripos($method, 'multi')) {
            // 匹配多个值，可支持 optionalMulti
            if (true === ($result = self::matchMulti($method, $args, $optional, $multiValue))) {
                return true;
            }
        } else {
            // 匹配单个值
            if (true === ($result = self::matchSingle($method, $args, $optional, $multiValue))) {
                return true;
            }
        }

        // @phpstan-ignore-next-line
        [$method, $multi] = $result;

        // 验证
        if (false === self::validateRule($method, $multi)) {
            return false;
        }

        return true;
    }

    /**
     * 参数校验和消息整理.
     */
    protected static function normalizeMessage(string $method, array $args, ?string $field = null): string
    {
        $rule = UnCamelize::handle($method);
        $rule = str_replace(['optional_multi_', 'multi_', 'optional_'], ['', '', ''], $rule);

        if (\count($args) >= 2 && \is_string($args[array_key_last($args)])) {
            $message = array_pop($args);
        } else {
            $message = static::$defaultMessages[$rule] ?? 'No exception messsage specified.';
        }

        $replace = [
            'field' => $field ?? '',
        ];
        $param = $args[1] ?? [];
        if (static::isImplodeRuleParam($rule)) {
            $replace['rule'] = implode(',', $param);
        } else {
            foreach ($param as $key => $value) {
                $replace['rule'.($key ?: '')] = $value;
            }
        }

        $message = preg_replace_callback('/{(.+?)}/', static function (array $matches) use ($replace): string {
            return (string) ($replace[$matches[1]] ?? $matches[0]);
        }, $message);

        return trim($message);
    }

    /**
     * 返回需要合并的规则参数.
     */
    protected static function isImplodeRuleParam(string $rule): bool
    {
        return \in_array($rule, ['in', 'not_in', 'allow_ip', 'deny_ip'], true);
    }

    /**
     * 匹配可选规则.
     */
    protected static function matchOptional(string $method, array $args): array|bool
    {
        if (!str_starts_with($method, 'optional')) {
            return [$method, false];
        }

        if (null === $args[0]) {
            return true;
        }

        $method = substr($method, 8);

        return [$method, true];
    }

    /**
     * 匹配多个值.
     *
     * @throws \InvalidArgumentException
     */
    protected static function matchMulti(string $method, array $args, bool $optional, bool $multiValue): array|true
    {
        // 非多选值转换为多选值
        if (!$multiValue) {
            $args[0] = [$args[0]];
        }

        if (!\is_array($args[0]) && !$args[0] instanceof \Traversable) {
            throw new \InvalidArgumentException('Invalid first argument for multi assert.');
        }

        $multi = [];
        $argsSource = $args;
        foreach ($args[0] as $v) {
            if (null === $v && $optional) {
                continue;
            }
            $argsSource[0] = $v;
            $multi[] = $argsSource;
        }

        if (!$multi) {
            return true;
        }

        $method = substr($method, 5);

        return [$method, $multi];
    }

    /**
     * 匹配单个值.
     *
     * @throws \InvalidArgumentException
     */
    protected static function matchSingle(string $method, array $args, bool $optional, bool $multiValue): array|true
    {
        if (!$multiValue) {
            $args[0] = [$args[0]];
        }

        if (!\is_array($args[0]) && !$args[0] instanceof \Traversable) {
            throw new \InvalidArgumentException('Invalid first argument for multi assert.');
        }

        $multi = [];
        $argsSource = $args;
        foreach ($args[0] as $v) {
            if (null === $v && $optional) {
                continue;
            }
            $argsSource[0] = $v;
            $multi[] = $argsSource;
        }

        if (!$multi) {
            return true;
        }

        return [$method, $multi];
    }

    /**
     * 校验规则.
     *
     * @throws \BadMethodCallException
     */
    protected static function validateRule(string $method, array $multi): bool
    {
        $helperClass = __NAMESPACE__.'\\Helper\\'.ucfirst($method);
        if (!class_exists($helperClass)) {
            throw new \BadMethodCallException(sprintf('Class `%s` is not exits.', $helperClass));
        }

        // 验证器默认消息
        $rule = UnCamelize::handle($method);
        if (!isset(static::$defaultMessages[$rule])) {
            static::$defaultMessages[$rule] = \defined($helperClass.'::MESSAGE') ? __($helperClass::MESSAGE) : '';
        }

        foreach ($multi as $m) {
            if (false === $helperClass::handle(...$m)) {
                return false;
            }
        }

        return true;
    }
}

if (!\function_exists(__NAMESPACE__.'\\__')) {
    function __(string $text, ...$data): string
    {
        if (!class_exists(Gettext::class)) {
            return sprintf($text, ...$data); // @codeCoverageIgnore
        }

        return Gettext::handle($text, ...$data);
    }
}
