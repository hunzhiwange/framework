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

use Leevel\I18n\Load;
use Tests\TestCase;

/**
 * load test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.20
 *
 * @version 1.0
 */
class LoadTest extends TestCase
{
    public function testBaseUse()
    {
        $load = $this->createLoad('zh-CN');

        $result = $load->loadData();

        $this->assertSame([], $result);

        $result = $load->loadData();

        $this->assertSame([], $result);
    }

    public function testEnus()
    {
        $load = $this->createLoad('en-US');

        $result = $load->loadData();

        $data = <<<'eot'
array (
  '太多请求' => 'Too Many Requests',
  '对于需要登录的网页，服务器可能返回此响应' => 'For web pages that need to be logged in, the server may return this response',
  '方法禁用' => 'Method Not Allowed',
  '无法处理的实体' => 'Unprocessable Entity',
  '服务器不理解请求的语法' => 'The server does not understand the syntax of the request',
  '服务器内部错误' => 'Internal Server Error',
  '服务器拒绝请求' => 'Server refusal the request',
  '服务器遇到错误，无法完成请求' => 'Could not complete request',
  '未授权' => 'Unauthorized',
  '用户发出的请求针对的是不存在的页面' => 'The user’s request is for a page that does not exist',
  '用户在给定的时间内发送了太多的请求' => 'The user sends too many requests within a given time',
  '禁止' => 'Forbidden',
  '禁用请求中指定的方法' => 'Disable the method specified in the request',
  '请求格式正确，但是由于含有语义错误，无法响应' => 'The request format is correct, but because of semantic errors, it cannot respond',
  '重试' => 'Retry',
  '错误请求' => 'Bad Request',
  '页面未找到' => 'Page Not Found',
  '首页' => 'Home',
  '上一页' => 'Previous',
  '下一页' => 'Next',
  '共 %d 条' => 'Total %d',
  '前往' => 'Go to',
  '页' => 'Page',
  '{field} 不允许的 IP 访问 {rule}' => 'IP not allowed by {field} to access {rule}',
  '{field} 不在 {rule} 和 {rule1} 之间，不包含等于' => '{field} is not between {rule} and {rule1}, does not contain equal',
  '{field} 不在 {rule} 和 {rule1} 之间，包含等于' => '{field} is not between {rule} and {rule1}, contains equals',
  '{field} 不是有效的 IP 地址' => '{field} is not a valid IP address',
  '{field} 不是有效的 IPV4 地址' => '{field} is not a valid IPV4 address',
  '{field} 不是有效的 IPV6 地址' => '{field} is not a valid IPV6 address',
  '{field} 不是有效的 JSON' => '{field} is not a valid JSON',
  '{field} 不是有效的 URL 地址' => '{field} is not a valid URL address',
  '{field} 不是有效的域名或者 IP' => '{field} is not a valid domain name or IP',
  '{field} 不是正确的日期格式' => '{field} is not the correct date',
  '{field} 不是正确的时区' => '{field} is not the correct time zone',
  '{field} 不满足最大长度 {rule}' => '{field} does not satisfy the maximum length {rule}',
  '{field} 不满足最小长度 {rule}' => '{field} does not satisfy the minimum length {rule}',
  '{field} 不能为 null' => '{field} can not be null',
  '{field} 不能为空' => '{field} can not be empty',
  '{field} 不能在 {rule} 范围内' => '{field} is not within {rule}',
  '{field} 不能完全等于 {rule}' => '{field} is not exactly equal to {rule}',
  '{field} 不能等于 {rule}' => '{field} cannot be equal to {rule}',
  '{field} 不能等于字段 {rule}' => '{field} cannot be equal to field {rule}',
  '{field} 只能在 {rule} 和 {rule1} 之间，不包含等于' => '{field} can only be between {rule} and {rule1}, and does not contain equal',
  '{field} 只能在 {rule} 和 {rule1} 之间，包含等于' => '{field} can only be between {rule} and {rule1}, and contains equal',
  '{field} 只能是大写字母' => '{field} can only be upper alpha',
  '{field} 只能是字母' => '{field} can only be alpha',
  '{field} 只能是字母、数字、短横线和下划线' => '{field} can only be alpha, numbers, short lines and underscores',
  '{field} 只能是字母和数字' => '{field} can only be alpha and number',
  '{field} 只能是小写字母' => '{field} can only be lower alpha',
  '{field} 只能是汉字' => '{field} can only be Chinese characters',
  '{field} 只能是汉字、字母、数字' => '{field} can only be Chinese characters, alpha, numbers',
  '{field} 只能是汉字、字母、数字、短横线和下划线' => '{field} can only be chinese characters,alpha, numbers, short lines and underscores',
  '{field} 字符串中的字符必须都是数字' => 'The characters in the {field} string must be all numeric',
  '{field} 必须 null' => '{field} must be null',
  '{field} 必须为正确的电子邮件格式' => '{field} must be in the right e-mail format',
  '{field} 必须为正确的符合 luhn 格式算法银行卡' => '{field} must be the correct bank card in accordance with the Luhn format algorithm',
  '{field} 必须为空' => '{field} must be empty',
  '{field} 必须使用日期格式 {rule}' => '{field} must use date format {rule}',
  '{field} 必须全部是大写' => '{field} must all be capitalized',
  '{field} 必须全部是小写' => '{field} must be all lowercase',
  '{field} 必须在 {rule} 范围内' => '{field} must be in the range of {rule}',
  '{field} 必须大于 {rule}' => '{field} must be greater than {rule}',
  '{field} 必须大于等于 {rule}' => '{field} must be greater than or equal to {rule}',
  '{field} 必须完全等于 {rule}' => '{field} must be exactly equal to {rule}',
  '{field} 必须小于 {rule}' => '{field} must be less than {rule}',
  '{field} 必须小于等于 {rule}' => '{field} must be less than or equal to {rule}',
  '{field} 必须是 yes、on、true 或者 1' => '{field} must be yes, on, true or 1',
  '{field} 必须是双精度浮点数' => '{field} must be double',
  '{field} 必须是布尔值' => '{field} must be boolean',
  '{field} 必须是数字' => '{field} must be number',
  '{field} 必须是数组' => '{field} must be an array',
  '{field} 必须是有效的 QQ 号码' => '{field} must be a valid QQ number',
  '{field} 必须是有效的中国大陆身份证' => '{field} must be valid Chinese mainland ID card',
  '{field} 必须是有效的中国邮政编码' => '{field} must be valid Chinese postal code',
  '{field} 必须是有效的手机号' => '{field} must be a valid cell phone number',
  '{field} 必须是有效的电话号码' => '{field} must be a valid phone number',
  '{field} 必须是有效的电话号码或者手机号' => '{field} must be a valid phone number or cell phone number',
  '{field} 必须是浮点数' => '{field} must be float',
  '{field} 必须等于 {rule}' => '{field} must be equal to {rule}',
  '{field} 必须等于字段 {rule}' => '{field} must be equal to field {rule}',
  '{field} 日期不能小于 {rule}' => '{field} date must not be less than {rule}',
  '{field} 日期不能超过 {rule}' => '{field} date must not exceed {rule}',
  '{field} 禁止的 IP 访问 {rule}' => '{field} prohibited IP access to {rule}',
  '{field} 类型不符合要求 {rule}' => '{field} type does not meet the requirement {rule}',
  '{field} 长度不符合要求 {rule}' => '{field} length does not meet the requirements of {rule}',
  '{field} 长度不能小于 {rule}' => '{field} length must not be less than {rule}',
  '{field} 长度不能超过 {rule}' => '{field} length must not exceed {rule}',
  '无效的请求类型 {rule}' => 'Invalid request type {rule}',
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $result
            )
        );
    }

    public function testZhtw()
    {
        $load = $this->createLoad('zh-TW');

        $result = $load->loadData();

        $data = <<<'eot'
array (
  '太多请求' => '太多請求',
  '对于需要登录的网页，服务器可能返回此响应' => '對於需要登入的網頁，服務器可能返回此響應',
  '方法禁用' => '方法禁用',
  '无法处理的实体' => '無法處理的實體',
  '服务器不理解请求的语法' => '服務器不理解請求的語法',
  '服务器内部错误' => '服務器內部錯誤',
  '服务器拒绝请求' => '服務器拒絕請求',
  '服务器遇到错误，无法完成请求' => '服務器遇到錯誤，無法完成請求',
  '未授权' => '未授權',
  '用户发出的请求针对的是不存在的页面' => '用戶發出的請求針對的是不存在的頁面',
  '用户在给定的时间内发送了太多的请求' => '用戶在給定的時間內發送了太多的請求',
  '禁止' => '禁止',
  '禁用请求中指定的方法' => '禁用請求中指定的方法',
  '请求格式正确，但是由于含有语义错误，无法响应' => '請求格式正確，但是由於含有語義錯誤，無法響應',
  '重试' => '重試',
  '错误请求' => '錯誤請求',
  '页面未找到' => '頁面未找到',
  '首页' => '首頁',
  '上一页' => '上一頁',
  '下一页' => '下一頁',
  '共 %d 条' => '共 %d 條',
  '前往' => '前往',
  '页' => '頁',
  '{field} 不允许的 IP 访问 {rule}' => '{field} 不允許的 IP 訪問 {rule}',
  '{field} 不在 {rule} 和 {rule1} 之间，不包含等于' => '{field} 不在 {rule} 和 {rule1} 之間，不包含等於',
  '{field} 不在 {rule} 和 {rule1} 之间，包含等于' => '{field} 不在 {rule} 和 {rule1} 之間，包含等於',
  '{field} 不是有效的 IP 地址' => '{field} 不是有效的 IP 地址',
  '{field} 不是有效的 IPV4 地址' => '{field} 不是有效的 IPV4 地址',
  '{field} 不是有效的 IPV6 地址' => '{field} 不是有效的 IPV6 地址',
  '{field} 不是有效的 JSON' => '{field} 不是有效的 JSON',
  '{field} 不是有效的 URL 地址' => '{field} 不是有效的 URL 地址',
  '{field} 不是有效的域名或者 IP' => '{field} 不是有效的功能變數名稱或者 IP',
  '{field} 不是正确的日期格式' => '{field} 不是正確的日期格式',
  '{field} 不是正确的时区' => '{field} 不是正確的時區',
  '{field} 不满足最大长度 {rule}' => '{field} 不滿足最大長度 {rule}',
  '{field} 不满足最小长度 {rule}' => '{field} 不滿足最小長度 {rule}',
  '{field} 不能为 null' => '{field} 不能為 null',
  '{field} 不能为空' => '{field} 不能為空',
  '{field} 不能在 {rule} 范围内' => '{field} 不能在 {rule} 範圍內',
  '{field} 不能完全等于 {rule}' => '{field} 不能完全等於 {rule}',
  '{field} 不能等于 {rule}' => '{field} 不能等於 {rule}',
  '{field} 不能等于字段 {rule}' => '{field} 不能等於欄 位{rule}',
  '{field} 只能在 {rule} 和 {rule1} 之间，不包含等于' => '{field} 只能在 {rule} 和 {rule1} 之間，不包含等於',
  '{field} 只能在 {rule} 和 {rule1} 之间，包含等于' => '{field} 只能在 {rule} 和 {rule1} 之間，包含等於',
  '{field} 只能是大写字母' => '{field} 只能是大寫字母',
  '{field} 只能是字母' => '{field} 只能是字母',
  '{field} 只能是字母、数字、短横线和下划线' => '{field} 只能是字母、數位、短橫線和底線',
  '{field} 只能是字母和数字' => '{field} 只能是字母和數位',
  '{field} 只能是小写字母' => '{field} 只能是小寫字母',
  '{field} 只能是汉字' => '{field} 只能是漢字',
  '{field} 只能是汉字、字母、数字' => '{field} 只能是漢字、字母、數位',
  '{field} 只能是汉字、字母、数字、短横线和下划线' => '{field} 只能是漢字、字母、數位、短橫線和底線',
  '{field} 字符串中的字符必须都是数字' => '{field} 字串中的字元必須都是數位',
  '{field} 必须 null' => '{field} 必須 null',
  '{field} 必须为正确的电子邮件格式' => '{field} 必須為正確的電子郵件格式',
  '{field} 必须为正确的符合 luhn 格式算法银行卡' => '{field} 必須為正確的符合 luhn 格式算灋銀行卡',
  '{field} 必须为空' => '{field} 必須為空',
  '{field} 必须使用日期格式 {rule}' => '{field} 必須使用日期格式 {rule}',
  '{field} 必须全部是大写' => '{field} 必須全部是大寫',
  '{field} 必须全部是小写' => '{field} 必須全部是小寫',
  '{field} 必须在 {rule} 范围内' => '{field} 必須在 {rule} 範圍內',
  '{field} 必须大于 {rule}' => '{field} 必須大於 {rule}',
  '{field} 必须大于等于 {rule}' => '{field} 必須大於等於 {rule}',
  '{field} 必须完全等于 {rule}' => '{field} 必須完全等於 {rule}',
  '{field} 必须小于 {rule}' => '{field} 必須小於 {rule}',
  '{field} 必须小于等于 {rule}' => '{field} 必須小於等於 {rule}',
  '{field} 必须是 yes、on、true 或者 1' => '{field} 必須是 yes、on、true 或者 1',
  '{field} 必须是双精度浮点数' => '{field} 必須是雙精度浮點數',
  '{field} 必须是布尔值' => '{field} 必須是布林值',
  '{field} 必须是数字' => '{field} 必須是數位',
  '{field} 必须是数组' => '{field} 必須是數組',
  '{field} 必须是有效的 QQ 号码' => '{field} 必須是有效的 QQ 號碼',
  '{field} 必须是有效的中国大陆身份证' => '{field} 必須是有效的中國大陸身份證',
  '{field} 必须是有效的中国邮政编码' => '{field} 必須是有效的中國郵遞區號',
  '{field} 必须是有效的手机号' => '{field} 必須是有效的手機號',
  '{field} 必须是有效的电话号码' => '{field} 必須是有效的電話號碼',
  '{field} 必须是有效的电话号码或者手机号' => '{field} 必須是有效的電話號碼或者手機號',
  '{field} 必须是浮点数' => '{field} 必須是浮點數',
  '{field} 必须等于 {rule}' => '{field} 必須等於 {rule}',
  '{field} 必须等于字段 {rule}' => '{field} 必須等於欄位 {rule}',
  '{field} 日期不能小于 {rule}' => '{field} 日期不能小於 {rule}',
  '{field} 日期不能超过 {rule}' => '{field} 日期不能超過 {rule}',
  '{field} 禁止的 IP 访问 {rule}' => '{field} 禁止的IP訪問 {rule}',
  '{field} 类型不符合要求 {rule}' => '{field} 類型不符合要求 {rule}',
  '{field} 长度不符合要求 {rule}' => '{field} 長度不符合要求 {rule}',
  '{field} 长度不能小于 {rule}' => '{field} 長度不能小於 {rule}',
  '{field} 长度不能超过 {rule}' => '{field} 長度不能超過 {rule}',
  '无效的请求类型 {rule}' => '無效的請求類型 {rule}',
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $result
            )
        );
    }

    public function testLoadDirNotExists()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'I18n load dir is not exits.'
        );

        (new Load([__DIR__.'/i18nNotExists']))->loadData();
    }

    public function createLoad(string $lang)
    {
        return (new Load([__DIR__.'/i18n']))->
        setI18n($lang)->

        addDir([__DIR__.'/i18n/extend']);
    }
}
