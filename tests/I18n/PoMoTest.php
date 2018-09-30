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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\I18n;

use Leevel\I18n\Mo;
use Leevel\I18n\Po;
use Tests\TestCase;

/**
 * po mo test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.03
 *
 * @version 1.0
 */
class PoMoTest extends TestCase
{
    public function testBaseUse()
    {
        $data = [
            '上一页'    => 'Previous',
            '下一页'    => 'Next',
            '共 %d 条' => 'Total %d',
            '前往'     => 'Go to',
            '页'      => 'Page',
        ];

        $lang = (new Po())->read([__DIR__.'/page.po']);

        $this->assertSame($lang, $data);

        $lang = (new Mo())->read([__DIR__.'/page.mo']);

        $this->assertSame($lang, $data);
    }

    public function testMore()
    {
        $data = <<<'eot'
{
    "{field} 不能为空": "{field} can not be empty",
    "{field} 必须是数字": "{field} must be number",
    "{field} 必须是浮点数": "{field} must be float",
    "{field} 必须是双精度浮点数": "{field} must be double",
    "{field} 必须是布尔值": "{field} must be boolean",
    "{field} 必须是数组": "{field} must be an array",
    "{field} 必须是 yes、on、true 或者 1": "{field} must be yes, on, true or 1",
    "{field} 不是正确的日期格式": "{field} is not the correct date",
    "{field} 必须使用日期格式 {rule}": "{field} must use date format {rule}",
    "{field} 不是正确的时区": "{field} is not the correct time zone",
    "{field} 只能是字母": "{field} can only be alpha",
    "{field} 只能是大写字母": "{field} can only be upper alpha",
    "{field} 只能是小写字母": "{field} can only be lower alpha",
    "{field} 只能是字母和数字": "{field} can only be alpha and number",
    "{field} 只能是字母、数字、短横线和下划线": "{field} can only be alpha, numbers, short lines and underscores",
    "{field} 只能是汉字": "{field} can only be Chinese characters",
    "{field} 只能是汉字、字母、数字": "{field} can only be Chinese characters, alpha, numbers",
    "{field} 只能是汉字、字母、数字、短横线和下划线": "{field} can only be chinese characters,alpha, numbers, short lines and underscores",
    "{field} 不是有效的 URL 地址": "{field} is not a valid URL address",
    "{field} 不是有效的域名或者 IP": "{field} is not a valid domain name or IP",
    "{field} 不是有效的 IP 地址": "{field} is not a valid IP address",
    "{field} 不是有效的 IPV4 地址": "{field} is not a valid IPV4 address",
    "{field} 不是有效的 IPV6 地址": "{field} is not a valid IPV6 address",
    "{field} 必须在 {rule} 范围内": "{field} must be in the range of {rule}",
    "{field} 不能在 {rule} 范围内": "{field} is not within {rule}",
    "{field} 只能在 {rule} 和 {rule1} 之间，不包含等于": "{field} can only be between {rule} and {rule1}, and does not contain equal",
    "{field} 不在 {rule} 和 {rule1} 之间，不包含等于": "{field} is not between {rule} and {rule1}, does not contain equal",
    "{field} 只能在 {rule} 和 {rule1} 之间，包含等于": "{field} can only be between {rule} and {rule1}, and contains equal",
    "{field} 不在 {rule} 和 {rule1} 之间，包含等于": "{field} is not between {rule} and {rule1}, contains equals",
    "{field} 必须大于 {rule}": "{field} must be greater than {rule}",
    "{field} 必须大于等于 {rule}": "{field} must be greater than or equal to {rule}",
    "{field} 必须小于 {rule}": "{field} must be less than {rule}",
    "{field} 必须小于等于 {rule}": "{field} must be less than or equal to {rule}",
    "{field} 必须等于 {rule}": "{field} must be equal to {rule}",
    "{field} 不能等于 {rule}": "{field} cannot be equal to {rule}",
    "{field} 必须等于字段 {rule}": "{field} must be equal to field {rule}",
    "{field} 不能等于字段 {rule}": "{field} cannot be equal to field {rule}",
    "{field} 必须完全等于 {rule}": "{field} must be exactly equal to {rule}",
    "{field} 不能完全等于 {rule}": "{field} is not exactly equal to {rule}",
    "{field} 必须为空": "{field} must be empty",
    "{field} 必须 null": "{field} must be null",
    "{field} 不能为 null": "{field} can not be null",
    "{field} 长度不符合要求 {rule}": "{field} length does not meet the requirements of {rule}",
    "{field} 长度不能超过 {rule}": "{field} length must not exceed {rule}",
    "{field} 长度不能小于 {rule}": "{field} length must not be less than {rule}",
    "{field} 字符串中的字符必须都是数字": "The characters in the {field} string must be all numeric",
    "{field} 类型不符合要求 {rule}": "{field} type does not meet the requirement {rule}",
    "{field} 必须全部是小写": "{field} must be all lowercase",
    "{field} 必须全部是大写": "{field} must all be capitalized",
    "{field} 不满足最小长度 {rule}": "{field} does not satisfy the minimum length {rule}",
    "{field} 不满足最大长度 {rule}": "{field} does not satisfy the maximum length {rule}",
    "{field} 必须是有效的中国大陆身份证": "{field} must be valid Chinese mainland ID card",
    "{field} 必须是有效的中国邮政编码": "{field} must be valid Chinese postal code",
    "{field} 必须是有效的 QQ 号码": "{field} must be a valid QQ number",
    "{field} 必须是有效的电话号码或者手机号": "{field} must be a valid phone number or cell phone number",
    "{field} 必须是有效的手机号": "{field} must be a valid cell phone number",
    "{field} 必须是有效的电话号码": "{field} must be a valid phone number",
    "{field} 必须为正确的电子邮件格式": "{field} must be in the right e-mail format",
    "{field} 必须为正确的符合 luhn 格式算法银行卡": "{field} must be the correct bank card in accordance with the Luhn format algorithm",
    "{field} 日期不能小于 {rule}": "{field} date must not be less than {rule}",
    "{field} 日期不能超过 {rule}": "{field} date must not exceed {rule}",
    "{field} 不允许的 IP 访问 {rule}": "IP not allowed by {field} to access {rule}",
    "{field} 禁止的 IP 访问 {rule}": "{field} prohibited IP access to {rule}",
    "无效的请求类型 {rule}": "Invalid request type {rule}",
    "{field} 不是有效的 JSON": "{field} is not a valid JSON"
}
eot;

        $lang = (new Po())->read([__DIR__.'/validate.po']);

        $this->assertSame(
            $data,
            $this->varJson(
                $lang
            )
        );

        $lang = (new Mo())->read([__DIR__.'/validate.mo']);

        $data = <<<'eot'
{
    "{field} 不允许的 IP 访问 {rule}": "IP not allowed by {field} to access {rule}",
    "{field} 不在 {rule} 和 {rule1} 之间，不包含等于": "{field} is not between {rule} and {rule1}, does not contain equal",
    "{field} 不在 {rule} 和 {rule1} 之间，包含等于": "{field} is not between {rule} and {rule1}, contains equals",
    "{field} 不是有效的 IP 地址": "{field} is not a valid IP address",
    "{field} 不是有效的 IPV4 地址": "{field} is not a valid IPV4 address",
    "{field} 不是有效的 IPV6 地址": "{field} is not a valid IPV6 address",
    "{field} 不是有效的 JSON": "{field} is not a valid JSON",
    "{field} 不是有效的 URL 地址": "{field} is not a valid URL address",
    "{field} 不是有效的域名或者 IP": "{field} is not a valid domain name or IP",
    "{field} 不是正确的日期格式": "{field} is not the correct date",
    "{field} 不是正确的时区": "{field} is not the correct time zone",
    "{field} 不满足最大长度 {rule}": "{field} does not satisfy the maximum length {rule}",
    "{field} 不满足最小长度 {rule}": "{field} does not satisfy the minimum length {rule}",
    "{field} 不能为 null": "{field} can not be null",
    "{field} 不能为空": "{field} can not be empty",
    "{field} 不能在 {rule} 范围内": "{field} is not within {rule}",
    "{field} 不能完全等于 {rule}": "{field} is not exactly equal to {rule}",
    "{field} 不能等于 {rule}": "{field} cannot be equal to {rule}",
    "{field} 不能等于字段 {rule}": "{field} cannot be equal to field {rule}",
    "{field} 只能在 {rule} 和 {rule1} 之间，不包含等于": "{field} can only be between {rule} and {rule1}, and does not contain equal",
    "{field} 只能在 {rule} 和 {rule1} 之间，包含等于": "{field} can only be between {rule} and {rule1}, and contains equal",
    "{field} 只能是大写字母": "{field} can only be upper alpha",
    "{field} 只能是字母": "{field} can only be alpha",
    "{field} 只能是字母、数字、短横线和下划线": "{field} can only be alpha, numbers, short lines and underscores",
    "{field} 只能是字母和数字": "{field} can only be alpha and number",
    "{field} 只能是小写字母": "{field} can only be lower alpha",
    "{field} 只能是汉字": "{field} can only be Chinese characters",
    "{field} 只能是汉字、字母、数字": "{field} can only be Chinese characters, alpha, numbers",
    "{field} 只能是汉字、字母、数字、短横线和下划线": "{field} can only be chinese characters,alpha, numbers, short lines and underscores",
    "{field} 字符串中的字符必须都是数字": "The characters in the {field} string must be all numeric",
    "{field} 必须 null": "{field} must be null",
    "{field} 必须为正确的电子邮件格式": "{field} must be in the right e-mail format",
    "{field} 必须为正确的符合 luhn 格式算法银行卡": "{field} must be the correct bank card in accordance with the Luhn format algorithm",
    "{field} 必须为空": "{field} must be empty",
    "{field} 必须使用日期格式 {rule}": "{field} must use date format {rule}",
    "{field} 必须全部是大写": "{field} must all be capitalized",
    "{field} 必须全部是小写": "{field} must be all lowercase",
    "{field} 必须在 {rule} 范围内": "{field} must be in the range of {rule}",
    "{field} 必须大于 {rule}": "{field} must be greater than {rule}",
    "{field} 必须大于等于 {rule}": "{field} must be greater than or equal to {rule}",
    "{field} 必须完全等于 {rule}": "{field} must be exactly equal to {rule}",
    "{field} 必须小于 {rule}": "{field} must be less than {rule}",
    "{field} 必须小于等于 {rule}": "{field} must be less than or equal to {rule}",
    "{field} 必须是 yes、on、true 或者 1": "{field} must be yes, on, true or 1",
    "{field} 必须是双精度浮点数": "{field} must be double",
    "{field} 必须是布尔值": "{field} must be boolean",
    "{field} 必须是数字": "{field} must be number",
    "{field} 必须是数组": "{field} must be an array",
    "{field} 必须是有效的 QQ 号码": "{field} must be a valid QQ number",
    "{field} 必须是有效的中国大陆身份证": "{field} must be valid Chinese mainland ID card",
    "{field} 必须是有效的中国邮政编码": "{field} must be valid Chinese postal code",
    "{field} 必须是有效的手机号": "{field} must be a valid cell phone number",
    "{field} 必须是有效的电话号码": "{field} must be a valid phone number",
    "{field} 必须是有效的电话号码或者手机号": "{field} must be a valid phone number or cell phone number",
    "{field} 必须是浮点数": "{field} must be float",
    "{field} 必须等于 {rule}": "{field} must be equal to {rule}",
    "{field} 必须等于字段 {rule}": "{field} must be equal to field {rule}",
    "{field} 日期不能小于 {rule}": "{field} date must not be less than {rule}",
    "{field} 日期不能超过 {rule}": "{field} date must not exceed {rule}",
    "{field} 禁止的 IP 访问 {rule}": "{field} prohibited IP access to {rule}",
    "{field} 类型不符合要求 {rule}": "{field} type does not meet the requirement {rule}",
    "{field} 长度不符合要求 {rule}": "{field} length does not meet the requirements of {rule}",
    "{field} 长度不能小于 {rule}": "{field} length must not be less than {rule}",
    "{field} 长度不能超过 {rule}": "{field} length must not exceed {rule}",
    "无效的请求类型 {rule}": "Invalid request type {rule}"
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $lang,
                1
            )
        );
    }
}
