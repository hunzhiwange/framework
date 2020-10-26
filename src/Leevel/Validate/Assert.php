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

namespace Leevel\Validate;

use BadMethodCallException;
use Closure;
use InvalidArgumentException;
use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;
use Traversable;

/**
 * 断言.
 *
 * - 提供一套精简版本的断言方便业务中很愉快地使用。
 * - 链式操作没有第一个参数 `$value`，此时值为 `$this->value`。
 *
 * @see https://github.com/beberlei/assert 参考这里，断言和验证器复用一致的规则
 *
 * @method static \Leevel\Validate\Assert|void accepted($value, ?string $message = null)                                                                     是否可接受的（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void allowedIp($value, array $param, ?string $message = null)                                                      验证 IP 许可（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void alpha($value, ?string $message = null)                                                                        是否为英文字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void alphaDash($value, ?string $message = null)                                                                    字符串是否为数字、下划线、短横线和字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void alphaLower($value, ?string $message = null)                                                                   是否为小写英文字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void alphaNum($value, ?string $message = null)                                                                     字符串是否为数字和字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void alphaUpper($value, ?string $message = null)                                                                   是否为大写英文字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void between($value, array $param, ?string $message = null)                                                        处于 between 范围，不包含等于（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void betweenEqual($value, array $param, ?string $message = null)                                                   处于 betweenEqual 范围，包含全等（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void boolean($value, ?string $message = null)                                                                      验证是否为布尔值（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void checkdnsrr($value, ?string $message = null)                                                                   验证是否为有效的域名（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void chinese($value, ?string $message = null)                                                                      是否为中文（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void chineseAlphaDash($value, ?string $message = null)                                                             是否为中文、数字、下划线、短横线和字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void chineseAlphaNum($value, ?string $message = null)                                                              是否为中文、数字和字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void date($value, ?string $message = null)                                                                         是否为日期（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void dateFormat($value, array $param, ?string $message = null)                                                     是否为时间（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void denyIp($value, array $param, ?string $message = null)                                                         验证 IP 许可（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void different($value, array $param, \Leevel\Validate\IValidator $validator, ?string $message = null)              两个字段是否不同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void digit($value, ?string $message = null)                                                                        检测字符串中的字符是否都是数字，负数和小数会检测不通过（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void double($value, ?string $message = null)                                                                       是否双精度浮点数（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void email($value, ?string $message = null)                                                                        是否为电子邮件（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void equal($value, array $param, ?string $message = null)                                                          两个值是否相同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void equalGreaterThan($value, array $param, ?string $message = null)                                               大于或者全等（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void equalLessThan($value, array $param, ?string $message = null)                                                  小于或者全等（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void equalTo($value, array $param, \Leevel\Validate\IValidator $validator, ?string $message = null)                两个字段是否相同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void greaterThan($value, array $param, ?string $message = null)                                                    大于（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void idCard($value, ?string $message = null)                                                                       是否为大陆身份证（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void in($value, array $param, ?string $message = null)                                                             是否处于某个范围（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void integer($value, ?string $message = null)                                                                      是否整型数字（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void ip($value, ?string $message = null)                                                                           是否为合法的 IP 地址（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void ipv4($value, ?string $message = null)                                                                         是否为 ipv4（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void ipv6($value, ?string $message = null)                                                                         是否为 ipv6（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void isArray($value, ?string $message = null)                                                                      验证是否为数组（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void isEmpty($value, ?string $message = null)                                                                      值是否为空（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void isFloat($value, ?string $message = null)                                                                      验证是否为浮点数（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void isNull($value, ?string $message = null)                                                                       是否为 null（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void json($value, ?string $message = null)                                                                         验证是否为正常的 JSON 字符串（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void lessThan($value, array $param, ?string $message = null)                                                       小于（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void lower($value, ?string $message = null)                                                                        验证是否都是小写（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void luhn($value, ?string $message = null)                                                                         值是否为银行卡等符合 luhn 算法（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void max($value, array $param, ?string $message = null)                                                            验证值上限（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void maxLength($value, array $param, ?string $message = null)                                                      验证数据最大长度（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void min($value, array $param, ?string $message = null)                                                            验证值下限（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void minLength($value, array $param, ?string $message = null)                                                      验证数据最小长度（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void mobile($value, ?string $message = null)                                                                       值是否为手机号码（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void notBetween($value, array $param, ?string $message = null)                                                     未处于 between 范围，不包含等于（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void notBetweenEqual($value, array $param, ?string $message = null)                                                未处于 betweenEqual 范围，包含等于（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void notEmpty($value, ?string $message = null)                                                                     值是否不为空（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void notEqual($value, array $param, ?string $message = null)                                                       两个值是否不相同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void notIn($value, array $param, ?string $message = null)                                                          是否不处于某个范围（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void notNull($value, ?string $message = null)                                                                      是否不为 null（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void notSame($value, array $param, ?string $message = null)                                                        两个值是否不完全相同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void number($value, ?string $message = null)                                                                       是否为数字（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void phone($value, ?string $message = null)                                                                        值是否为电话号码或者手机号码（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void qq($value, ?string $message = null)                                                                           是否为 QQ 号码（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void regex($value, array $param, ?string $message = null)                                                          数据是否满足正则条件（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void required($value, ?string $message = null)                                                                     不能为空（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void same($value, array $param, ?string $message = null)                                                           两个值是否完全相同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void strlen($value, array $param, ?string $message = null)                                                         长度验证（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void telephone($value, ?string $message = null)                                                                    值是否为电话号码（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void timezone($value, ?string $message = null)                                                                     是否为正确的时区（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void type($value, array $param, ?string $message = null)                                                           数据类型验证（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void upper($value, ?string $message = null)                                                                        验证是否都是大写（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void url($value, ?string $message = null)                                                                          验证是否为 URL 地址（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void zipCode($value, ?string $message = null)                                                                      是否为中国邮政编码（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalAccepted($value, ?string $message = null)                                                             是否可接受的（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalAllowedIp($value, array $param, ?string $message = null)                                              验证 IP 许可（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalAlpha($value, ?string $message = null)                                                                是否为英文字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalAlphaDash($value, ?string $message = null)                                                            字符串是否为数字、下划线、短横线和字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalAlphaLower($value, ?string $message = null)                                                           是否为小写英文字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalAlphaNum($value, ?string $message = null)                                                             字符串是否为数字和字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalAlphaUpper($value, ?string $message = null)                                                           是否为大写英文字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalBetween($value, array $param, ?string $message = null)                                                处于 between 范围，不包含等于（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalBetweenEqual($value, array $param, ?string $message = null)                                           处于 betweenEqual 范围，包含全等（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalBoolean($value, ?string $message = null)                                                              验证是否为布尔值（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalCheckdnsrr($value, ?string $message = null)                                                           验证是否为有效的域名（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalChinese($value, ?string $message = null)                                                              是否为中文（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalChineseAlphaDash($value, ?string $message = null)                                                     是否为中文、数字、下划线、短横线和字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalChineseAlphaNum($value, ?string $message = null)                                                      是否为中文、数字和字母（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalDate($value, ?string $message = null)                                                                 是否为日期（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalDateFormat($value, array $param, ?string $message = null)                                             是否为时间（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalDenyIp($value, array $param, ?string $message = null)                                                 验证 IP 许可（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalDifferent($value, array $param, \Leevel\Validate\IValidator $validator, ?string $message = null)      两个字段是否不同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalDigit($value, ?string $message = null)                                                                检测字符串中的字符是否都是数字，负数和小数会检测不通过（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalDouble($value, ?string $message = null)                                                               是否双精度浮点数（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalEmail($value, ?string $message = null)                                                                是否为电子邮件（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalEqual($value, array $param, ?string $message = null)                                                  两个值是否相同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalEqualGreaterThan($value, array $param, ?string $message = null)                                       大于或者全等（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalEqualLessThan($value, array $param, ?string $message = null)                                          小于或者全等（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalEqualTo($value, array $param, \Leevel\Validate\IValidator $validator, ?string $message = null)        两个字段是否相同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalGreaterThan($value, array $param, ?string $message = null)                                            大于（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalIdCard($value, ?string $message = null)                                                               是否为大陆身份证（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalIn($value, array $param, ?string $message = null)                                                     是否处于某个范围（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalInteger($value, ?string $message = null)                                                              是否整型数字（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalIp($value, ?string $message = null)                                                                   是否为合法的 IP 地址（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalIpv4($value, ?string $message = null)                                                                 是否为 ipv4（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalIpv6($value, ?string $message = null)                                                                 是否为 ipv6（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalIsArray($value, ?string $message = null)                                                              验证是否为数组（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalIsEmpty($value, ?string $message = null)                                                              值是否为空（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalIsFloat($value, ?string $message = null)                                                              验证是否为浮点数（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalIsNull($value, ?string $message = null)                                                               是否为 null（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalJson($value, ?string $message = null)                                                                 验证是否为正常的 JSON 字符串（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalLessThan($value, array $param, ?string $message = null)                                               小于（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalLower($value, ?string $message = null)                                                                验证是否都是小写（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalLuhn($value, ?string $message = null)                                                                 值是否为银行卡等符合 luhn 算法（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalMax($value, array $param, ?string $message = null)                                                    验证值上限（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalMaxLength($value, array $param, ?string $message = null)                                              验证数据最大长度（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalMin($value, array $param, ?string $message = null)                                                    验证值下限（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalMinLength($value, array $param, ?string $message = null)                                              验证数据最小长度（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalMobile($value, ?string $message = null)                                                               值是否为手机号码（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalNotBetween($value, array $param, ?string $message = null)                                             未处于 between 范围，不包含等于（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalNotBetweenEqual($value, array $param, ?string $message = null)                                        未处于 betweenEqual 范围，包含等于（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalNotEmpty($value, ?string $message = null)                                                             值是否不为空（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalNotEqual($value, array $param, ?string $message = null)                                               两个值是否不相同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalNotIn($value, array $param, ?string $message = null)                                                  是否不处于某个范围（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalNotNull($value, ?string $message = null)                                                              是否不为 null（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalNotSame($value, array $param, ?string $message = null)                                                两个值是否不完全相同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalNumber($value, ?string $message = null)                                                               是否为数字（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalPhone($value, ?string $message = null)                                                                值是否为电话号码或者手机号码（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalQq($value, ?string $message = null)                                                                   是否为 QQ 号码（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalRegex($value, array $param, ?string $message = null)                                                  数据是否满足正则条件（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalRequired($value, ?string $message = null)                                                             不能为空（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalSame($value, array $param, ?string $message = null)                                                   两个值是否完全相同（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalStrlen($value, array $param, ?string $message = null)                                                 长度验证（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalTelephone($value, ?string $message = null)                                                            值是否为电话号码（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalTimezone($value, ?string $message = null)                                                             是否为正确的时区（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalType($value, array $param, ?string $message = null)                                                   数据类型验证（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalUpper($value, ?string $message = null)                                                                验证是否都是大写（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalUrl($value, ?string $message = null)                                                                  验证是否为 URL 地址（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void optionalZipCode($value, ?string $message = null)                                                              是否为中国邮政编码（链式操作没有参数 $value）.
 * @method static \Leevel\Validate\Assert|void multiAccepted($value, ?string $message = null)                                                                是否可接受的（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiAllowedIp($value, array $param, ?string $message = null)                                                 验证 IP 许可（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiAlpha($value, ?string $message = null)                                                                   是否为英文字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiAlphaDash($value, ?string $message = null)                                                               字符串是否为数字、下划线、短横线和字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiAlphaLower($value, ?string $message = null)                                                              是否为小写英文字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiAlphaNum($value, ?string $message = null)                                                                字符串是否为数字和字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiAlphaUpper($value, ?string $message = null)                                                              是否为大写英文字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiBetween($value, array $param, ?string $message = null)                                                   处于 between 范围，不包含等于（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiBetweenEqual($value, array $param, ?string $message = null)                                              处于 betweenEqual 范围，包含全等（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiBoolean($value, ?string $message = null)                                                                 验证是否为布尔值（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiCheckdnsrr($value, ?string $message = null)                                                              验证是否为有效的域名（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiChinese($value, ?string $message = null)                                                                 是否为中文（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiChineseAlphaDash($value, ?string $message = null)                                                        是否为中文、数字、下划线、短横线和字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiChineseAlphaNum($value, ?string $message = null)                                                         是否为中文、数字和字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiDate($value, ?string $message = null)                                                                    是否为日期（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiDateFormat($value, array $param, ?string $message = null)                                                是否为时间（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiDenyIp($value, array $param, ?string $message = null)                                                    验证 IP 许可（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiDifferent($value, array $param, \Leevel\Validate\IValidator $validator, ?string $message = null)         两个字段是否不同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiDigit($value, ?string $message = null)                                                                   检测字符串中的字符是否都是数字，负数和小数会检测不通过（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiDouble($value, ?string $message = null)                                                                  是否双精度浮点数（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiEmail($value, ?string $message = null)                                                                   是否为电子邮件（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiEqual($value, array $param, ?string $message = null)                                                     两个值是否相同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiEqualGreaterThan($value, array $param, ?string $message = null)                                          大于或者全等（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiEqualLessThan($value, array $param, ?string $message = null)                                             小于或者全等（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiEqualTo($value, array $param, \Leevel\Validate\IValidator $validator, ?string $message = null)           两个字段是否相同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiGreaterThan($value, array $param, ?string $message = null)                                               大于（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiIdCard($value, ?string $message = null)                                                                  是否为大陆身份证（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiIn($value, array $param, ?string $message = null)                                                        是否处于某个范围（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiInteger($value, ?string $message = null)                                                                 是否整型数字（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiIp($value, ?string $message = null)                                                                      是否为合法的 IP 地址（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiIpv4($value, ?string $message = null)                                                                    是否为 ipv4（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiIpv6($value, ?string $message = null)                                                                    是否为 ipv6（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiIsArray($value, ?string $message = null)                                                                 验证是否为数组（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiIsEmpty($value, ?string $message = null)                                                                 值是否为空（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiIsFloat($value, ?string $message = null)                                                                 验证是否为浮点数（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiIsNull($value, ?string $message = null)                                                                  是否为 null（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiJson($value, ?string $message = null)                                                                    验证是否为正常的 JSON 字符串（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiLessThan($value, array $param, ?string $message = null)                                                  小于（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiLower($value, ?string $message = null)                                                                   验证是否都是小写（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiLuhn($value, ?string $message = null)                                                                    值是否为银行卡等符合 luhn 算法（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiMax($value, array $param, ?string $message = null)                                                       验证值上限（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiMaxLength($value, array $param, ?string $message = null)                                                 验证数据最大长度（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiMin($value, array $param, ?string $message = null)                                                       验证值下限（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiMinLength($value, array $param, ?string $message = null)                                                 验证数据最小长度（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiMobile($value, ?string $message = null)                                                                  值是否为手机号码（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiNotBetween($value, array $param, ?string $message = null)                                                未处于 between 范围，不包含等于（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiNotBetweenEqual($value, array $param, ?string $message = null)                                           未处于 betweenEqual 范围，包含等于（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiNotEmpty($value, ?string $message = null)                                                                值是否不为空（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiNotEqual($value, array $param, ?string $message = null)                                                  两个值是否不相同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiNotIn($value, array $param, ?string $message = null)                                                     是否不处于某个范围（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiNotNull($value, ?string $message = null)                                                                 是否不为 null（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiNotSame($value, array $param, ?string $message = null)                                                   两个值是否不完全相同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiNumber($value, ?string $message = null)                                                                  是否为数字（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiPhone($value, ?string $message = null)                                                                   值是否为电话号码或者手机号码（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiQq($value, ?string $message = null)                                                                      是否为 QQ 号码（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiRegex($value, array $param, ?string $message = null)                                                     数据是否满足正则条件（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiRequired($value, ?string $message = null)                                                                不能为空（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiSame($value, array $param, ?string $message = null)                                                      两个值是否完全相同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiStrlen($value, array $param, ?string $message = null)                                                    长度验证（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiTelephone($value, ?string $message = null)                                                               值是否为电话号码（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiTimezone($value, ?string $message = null)                                                                是否为正确的时区（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiType($value, array $param, ?string $message = null)                                                      数据类型验证（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiUpper($value, ?string $message = null)                                                                   验证是否都是大写（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiUrl($value, ?string $message = null)                                                                     验证是否为 URL 地址（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void multiZipCode($value, ?string $message = null)                                                                 是否为中国邮政编码（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiAccepted($value, ?string $message = null)                                                        是否可接受的（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiAllowedIp($value, array $param, ?string $message = null)                                         验证 IP 许可（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiAlpha($value, ?string $message = null)                                                           是否为英文字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiAlphaDash($value, ?string $message = null)                                                       字符串是否为数字、下划线、短横线和字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiAlphaLower($value, ?string $message = null)                                                      是否为小写英文字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiAlphaNum($value, ?string $message = null)                                                        字符串是否为数字和字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiAlphaUpper($value, ?string $message = null)                                                      是否为大写英文字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiBetween($value, array $param, ?string $message = null)                                           处于 between 范围，不包含等于（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiBetweenEqual($value, array $param, ?string $message = null)                                      处于 betweenEqual 范围，包含全等（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiBoolean($value, ?string $message = null)                                                         验证是否为布尔值（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiCheckdnsrr($value, ?string $message = null)                                                      验证是否为有效的域名（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiChinese($value, ?string $message = null)                                                         是否为中文（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiChineseAlphaDash($value, ?string $message = null)                                                是否为中文、数字、下划线、短横线和字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiChineseAlphaNum($value, ?string $message = null)                                                 是否为中文、数字和字母（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiDate($value, ?string $message = null)                                                            是否为日期（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiDateFormat($value, array $param, ?string $message = null)                                        是否为时间（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiDenyIp($value, array $param, ?string $message = null)                                            验证 IP 许可（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiDifferent($value, array $param, \Leevel\Validate\IValidator $validator, ?string $message = null) 两个字段是否不同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiDigit($value, ?string $message = null)                                                           检测字符串中的字符是否都是数字，负数和小数会检测不通过（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiDouble($value, ?string $message = null)                                                          是否双精度浮点数（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiEmail($value, ?string $message = null)                                                           是否为电子邮件（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiEqual($value, array $param, ?string $message = null)                                             两个值是否相同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiEqualGreaterThan($value, array $param, ?string $message = null)                                  大于或者全等（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiEqualLessThan($value, array $param, ?string $message = null)                                     小于或者全等（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiEqualTo($value, array $param, \Leevel\Validate\IValidator $validator, ?string $message = null)   两个字段是否相同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiGreaterThan($value, array $param, ?string $message = null)                                       大于（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiIdCard($value, ?string $message = null)                                                          是否为大陆身份证（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiIn($value, array $param, ?string $message = null)                                                是否处于某个范围（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiInteger($value, ?string $message = null)                                                         是否整型数字（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiIp($value, ?string $message = null)                                                              是否为合法的 IP 地址（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiIpv4($value, ?string $message = null)                                                            是否为 ipv4（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiIpv6($value, ?string $message = null)                                                            是否为 ipv6（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiIsArray($value, ?string $message = null)                                                         验证是否为数组（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiIsEmpty($value, ?string $message = null)                                                         值是否为空（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiIsFloat($value, ?string $message = null)                                                         验证是否为浮点数（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiIsNull($value, ?string $message = null)                                                          是否为 null（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiJson($value, ?string $message = null)                                                            验证是否为正常的 JSON 字符串（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiLessThan($value, array $param, ?string $message = null)                                          小于（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiLower($value, ?string $message = null)                                                           验证是否都是小写（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiLuhn($value, ?string $message = null)                                                            值是否为银行卡等符合 luhn 算法（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiMax($value, array $param, ?string $message = null)                                               验证值上限（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiMaxLength($value, array $param, ?string $message = null)                                         验证数据最大长度（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiMin($value, array $param, ?string $message = null)                                               验证值下限（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiMinLength($value, array $param, ?string $message = null)                                         验证数据最小长度（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiMobile($value, ?string $message = null)                                                          值是否为手机号码（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiNotBetween($value, array $param, ?string $message = null)                                        未处于 between 范围，不包含等于（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiNotBetweenEqual($value, array $param, ?string $message = null)                                   未处于 betweenEqual 范围，包含等于（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiNotEmpty($value, ?string $message = null)                                                        值是否不为空（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiNotEqual($value, array $param, ?string $message = null)                                          两个值是否不相同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiNotIn($value, array $param, ?string $message = null)                                             是否不处于某个范围（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiNotNull($value, ?string $message = null)                                                         是否不为 null（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiNotSame($value, array $param, ?string $message = null)                                           两个值是否不完全相同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiNumber($value, ?string $message = null)                                                          是否为数字（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiPhone($value, ?string $message = null)                                                           值是否为电话号码或者手机号码（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiQq($value, ?string $message = null)                                                              是否为 QQ 号码（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiRegex($value, array $param, ?string $message = null)                                             数据是否满足正则条件（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiRequired($value, ?string $message = null)                                                        不能为空（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiSame($value, array $param, ?string $message = null)                                              两个值是否完全相同（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiStrlen($value, array $param, ?string $message = null)                                            长度验证（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiTelephone($value, ?string $message = null)                                                       值是否为电话号码（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiTimezone($value, ?string $message = null)                                                        是否为正确的时区（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiType($value, array $param, ?string $message = null)                                              数据类型验证（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiUpper($value, ?string $message = null)                                                           验证是否都是大写（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiUrl($value, ?string $message = null)                                                             验证是否为 URL 地址（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 * @method static \Leevel\Validate\Assert|void optionalMultiZipCode($value, ?string $message = null)                                                         是否为中国邮政编码（链式操作没有参数 $value，多个校验场景参数 $value 为多个待校验数据数组）.
 */
class Assert
{
    /**
     * 校验值.
     *
     * @var mixed
     */
    protected mixed $value;

    /**
     * 默认消息.
     *
     * @var string
     */
    protected ?string $message = null;

    /**
     * 是否延后提示错误.
     *
     * @var bool
     */
    protected bool $lazy = false;

    /**
     * 是否验证所有.
     *
     * @var bool
     */
    protected bool $all = true;

    /**
     * 验证错误消息.
     *
     * @var array
     */
    protected array $error = [];

    /**
     * 构造函数.
     *
     * @param mixed $value
     */
    public function __construct(mixed $value, ?string $message = null, bool $lazy = false, bool $all = true)
    {
        $this->value = $value;
        $this->message = $message;
        $this->lazy = $lazy;
        $this->all = $all;
    }

    /**
     * call.
     *
     * @throws \Leevel\Validate\AssertException
     *
     * @return \Leevel\Validate\Assert
     */
    public function __call(string $method, array $args): self
    {
        if (false === $this->all && $this->error) {
            return $this;
        }

        array_unshift($args, $this->value);
        if (false === self::validateAssert($method, $args)) {
            $message = self::normalizeMessage($args, $this->message);
            if (false === $this->lazy) {
                throw new AssertException($message);
            }
            $this->error[] = $message;
        }

        return $this;
    }

    /**
     * call.
     *
     * @throws \Leevel\Validate\AssertException
     */
    public static function __callStatic(string $method, array $args): void
    {
        if (false === self::validateAssert($method, $args, false)) {
            $message = self::normalizeMessage($args);

            throw new AssertException($message);
        }
    }

    /**
     * 创建断言对象已支持链式表达式.
     *
     * @example
     * Assert::make($value)
     *     ->notEmpty()
     *     ->lessThan([7]);
     *
     * @param mixed $value
     *
     * @return \Leevel\Validate\Assert
     */
    public static function make(mixed $value, ?string $message = null, bool $lazy = false, bool $all = true): self
    {
        return new static($value, $message, $lazy, $all);
    }

    /**
     * 创建断言对象延迟抛出错误.
     *
     * @example
     * Assert::lazy($value)
     *     ->notEmpty()
     *     ->lessThan([7])
     *     ->flush();
     *
     * @param mixed $value
     *
     * @return \Leevel\Validate\Assert
     */
    public static function lazy(mixed $value, ?string $message = null, bool $all = true): self
    {
        return new static($value, $message, true, $all);
    }

    /**
     * 释放并抛出验证.
     *
     * @throws \Leevel\Validate\AssertException
     */
    public function flush(?Closure $format = null): bool
    {
        if ($this->error) {
            if (!$format) {
                $format = 'json_encode';
            }
            $e = $format($this->error);

            throw new AssertException($e);
        }

        return true;
    }

    /**
     * 校验断言.
     *
     * @throws \InvalidArgumentException
     */
    protected static function validateAssert(string $method, array $args, bool $multiForChain = true): bool
    {
        if (!array_key_exists(0, $args)) {
            $e = 'Missing the first argument.';

            throw new InvalidArgumentException($e);
        }

        // 匹配可选
        if (true === $result = self::matchOptional($method, $args)) {
            return true;
        }

        list($method, $optional) = $result;

        // 匹配多个值，可支持 optionalMulti
        if (true === $result = self::matchMulti($method, $args, $optional, $multiForChain)) {
            return true;
        }

        list($method, $multi) = $result;

        // 验证
        if (false === self::validateRule($method, $multi)) {
            return false;
        }

        return true;
    }

    /**
     * 参数校验和消息整理.
     */
    protected static function normalizeMessage(array $args, ?string $message = null): string
    {
        if (count($args) >= 2 && is_string($args[array_key_last($args)])) {
            return array_pop($args);
        }

        return $message ?? 'No exception messsage specified.';
    }

    /**
     * 匹配可选规则.
     *
     * @return array|bool
     */
    protected static function matchOptional(string $method, array $args)
    {
        if (0 !== strpos($method, 'optional')) {
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
     *
     * @return array|bool
     */
    protected static function matchMulti(string $method, array $args, bool $optional, bool $multiForChain = true)
    {
        if (0 !== stripos($method, 'multi')) {
            return [$method, [$args]];
        }

        if (true === $multiForChain) {
            $args[0] = [$args[0]];
        }
        if (!is_array($args[0]) && !$args[0] instanceof Traversable) {
            $e = sprintf('Invalid first argument for multi assert.');

            throw new InvalidArgumentException($e);
        }

        $multi = [];
        $argsSource = $args;
        foreach ($args[0] as $v) {
            if (null === $v && true === $optional) {
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
     * 校验规则.
     *
     * @throws \BadMethodCallException
     */
    protected static function validateRule(string $method, array $multi): bool
    {
        $fn = __NAMESPACE__.'\\Helper\\'.un_camelize($method);

        foreach ($multi as $m) {
            if (!function_exists($fn)) {
                class_exists($fn);
            }

            if (!function_exists($fn)) {
                $e = sprintf('Method `%s` is not exits.', $fn);

                throw new BadMethodCallException($e);
            }

            if (false === $fn(...$m)) {
                return false;
            }
        }

        return true;
    }
}

// import fn.
class_exists(un_camelize::class);
