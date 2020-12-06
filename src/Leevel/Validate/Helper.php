<?php

declare(strict_types=1);

namespace Leevel\Validate;

use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;

/**
 * 助手类.
 *
 * @method static bool accepted($value)                                                        是否可接受的.
 * @method static bool allowedIp($value, array $param)                                         验证 IP 许可.
 * @method static bool alpha($value)                                                           是否为英文字母.
 * @method static bool alphaDash($value)                                                       字符串是否为数字、下划线、短横线和字母.
 * @method static bool alphaLower($value)                                                      是否为小写英文字母.
 * @method static bool alphaNum($value)                                                        字符串是否为数字和字母.
 * @method static bool alphaUpper($value)                                                      是否为大写英文字母.
 * @method static bool between($value, array $param)                                           处于 between 范围，不包含等于.
 * @method static bool betweenEqual($value, array $param)                                      处于 betweenEqual 范围，包含全等.
 * @method static bool boolean($value)                                                         验证是否为布尔值.
 * @method static bool checkdnsrr($value)                                                      验证是否为有效的域名.
 * @method static bool chinese($value)                                                         是否为中文.
 * @method static bool chineseAlphaDash($value)                                                是否为中文、数字、下划线、短横线和字母.
 * @method static bool chineseAlphaNum($value)                                                 是否为中文、数字和字母.
 * @method static bool date($value)                                                            是否为日期.
 * @method static bool dateFormat($value, array $param)                                        是否为时间.
 * @method static bool denyIp($value, array $param)                                            验证 IP 许可.
 * @method static bool different($value, array $param, \Leevel\Validate\IValidator $validator) 两个字段是否不同.
 * @method static bool digit($value)                                                           检测字符串中的字符是否都是数字，负数和小数会检测不通过.
 * @method static bool double($value)                                                          是否双精度浮点数.
 * @method static bool email($value)                                                           是否为电子邮件.
 * @method static bool equal($value, array $param)                                             两个值是否相同.
 * @method static bool equalGreaterThan($value, array $param)                                  大于或者全等.
 * @method static bool equalLessThan($value, array $param)                                     小于或者全等.
 * @method static bool equalTo($value, array $param, \Leevel\Validate\IValidator $validator)   两个字段是否相同.
 * @method static bool greaterThan($value, array $param)                                       大于.
 * @method static bool idCard($value)                                                          是否为大陆身份证.
 * @method static bool in($value, array $param)                                                是否处于某个范围.
 * @method static bool integer($value)                                                         是否整型数字.
 * @method static bool ip($value)                                                              是否为合法的 IP 地址.
 * @method static bool ipv4($value)                                                            是否为 ipv4.
 * @method static bool ipv6($value)                                                            是否为 ipv6.
 * @method static bool isArray($value)                                                         验证是否为数组.
 * @method static bool isEmpty($value)                                                         值是否为空.
 * @method static bool isFloat($value)                                                         验证是否为浮点数.
 * @method static bool isNull($value)                                                          是否为 null.
 * @method static bool json($value)                                                            验证是否为正常的 JSON 字符串.
 * @method static bool lessThan($value, array $param)                                          小于.
 * @method static bool lower($value)                                                           验证是否都是小写.
 * @method static bool luhn($value)                                                            值是否为银行卡等符合 luhn 算法.
 * @method static bool max($value, array $param)                                               验证值上限.
 * @method static bool maxLength($value, array $param)                                         验证数据最大长度.
 * @method static bool min($value, array $param)                                               验证值下限.
 * @method static bool minLength($value, array $param)                                         验证数据最小长度.
 * @method static bool mobile($value)                                                          值是否为手机号码.
 * @method static bool notBetween($value, array $param)                                        未处于 between 范围，不包含等于.
 * @method static bool notBetweenEqual($value, array $param)                                   未处于 betweenEqual 范围，包含等于.
 * @method static bool notEmpty($value)                                                        值是否不为空.
 * @method static bool notEqual($value, array $param)                                          两个值是否不相同.
 * @method static bool notIn($value, array $param)                                             是否不处于某个范围.
 * @method static bool notNull($value)                                                         是否不为 null.
 * @method static bool notSame($value, array $param)                                           两个值是否不完全相同.
 * @method static bool number($value)                                                          是否为数字.
 * @method static bool phone($value)                                                           值是否为电话号码或者手机号码.
 * @method static bool qq($value)                                                              是否为 QQ 号码.
 * @method static bool regex($value, array $param)                                             数据是否满足正则条件.
 * @method static bool required($value)                                                        不能为空.
 * @method static bool same($value, array $param)                                              两个值是否完全相同.
 * @method static bool strlen($value, array $param)                                            长度验证.
 * @method static bool telephone($value)                                                       值是否为电话号码.
 * @method static bool timezone($value)                                                        是否为正确的时区.
 * @method static bool type($value, array $param)                                              数据类型验证.
 * @method static bool upper($value)                                                           验证是否都是大写.
 * @method static bool url($value)                                                             验证是否为 URL 地址.
 * @method static bool zipCode($value)                                                         是否为中国邮政编码.
 */
class Helper
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $fn = __NAMESPACE__.'\\Helper\\'.un_camelize($method);
        if (!function_exists($fn)) {
            class_exists($fn);
        }

        return $fn(...$args);
    }
}

// import fn.
class_exists(un_camelize::class);
