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

namespace Leevel\Validate;

use Leevel\Di\IContainer;
use Leevel\I18n\Facade\I18n;

/**
 * 验证工厂.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.26
 *
 * @version 1.0
 */
class Validate implements IValidate
{
    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 构造函数.
     *
     * @param \Leevel\Di\IContainer $container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;

        $this->initMessages();
    }

    /**
     * 创建一个验证器.
     *
     * @param array $datas
     * @param array $rules
     * @param array $names
     * @param array $messages
     *
     * @return \Leevel\Validate\IValidator
     */
    public function make(array $datas = [], array $rules = [], array $names = [], array $messages = []): IValidator
    {
        $validator = new Validator($datas, $rules, $names, $messages);

        $validator->setContainer($this->container);

        return $validator;
    }

    /**
     * 初始化默认验证消息.
     */
    public static function initMessages(): void
    {
        Validator::initMessages(['required'                => I18n::__('{field} 不能为空'),
            'number'                                       => I18n::__('{field} 必须是数字'),
            'float'                                        => I18n::__('{field} 必须是浮点数'),
            'double'                                       => I18n::__('{field} 必须是双精度浮点数'),
            'boolean'                                      => I18n::__('{field} 必须是布尔值'),
            'array'                                        => I18n::__('{field} 必须是数组'),
            'accepted'                                     => I18n::__('{field} 必须是 yes、on、true 或者 1'),
            'date'                                         => I18n::__('{field} 不是正确的日期格式'),
            'date_format'                                  => I18n::__('{field} 必须使用日期格式 {rule}'),
            'timezone'                                     => I18n::__('{field} 不是正确的时区'),
            'alpha'                                        => I18n::__('{field} 只能是字母'),
            'alpha_upper'                                  => I18n::__('{field} 只能是大写字母'),
            'alpha_lower'                                  => I18n::__('{field} 只能是小写字母'),
            'alpha_num'                                    => I18n::__('{field} 只能是字母和数字'),
            'alpha_dash'                                   => I18n::__('{field} 只能是字母、数字、短横线和下划线'),
            'chinese'                                      => I18n::__('{field} 只能是汉字'),
            'chinese_alpha_num'                            => I18n::__('{field} 只能是汉字、字母、数字'),
            'chinese_alpha_dash'                           => I18n::__('{field} 只能是汉字、字母、数字、短横线和下划线'),
            'url'                                          => I18n::__('{field} 不是有效的 URL 地址'),
            'active_url'                                   => I18n::__('{field} 不是有效的域名或者 IP'),
            'ip'                                           => I18n::__('{field} 不是有效的 IP 地址'),
            'ipv4'                                         => I18n::__('{field} 不是有效的 IPV4 地址'),
            'ipv6'                                         => I18n::__('{field} 不是有效的 IPV6 地址'),
            'in'                                           => I18n::__('{field} 必须在 {rule} 范围内'),
            'not_in'                                       => I18n::__('{field} 不能在 {rule} 范围内'),
            'between'                                      => I18n::__('{field} 只能在 {rule} 和 {rule1} 之间，不包含等于'),
            'not_between'                                  => I18n::__('{field} 不在 {rule} 和 {rule1} 之间，不包含等于'),
            'between_equal'                                => I18n::__('{field} 只能在 {rule} 和 {rule1} 之间，包含等于'),
            'not_between_equal'                            => I18n::__('{field} 不在 {rule} 和 {rule1} 之间，包含等于'),
            'greater_than'                                 => I18n::__('{field} 必须大于 {rule}'),
            'equal_greater_than'                           => I18n::__('{field} 必须大于等于 {rule}'),
            'less_than'                                    => I18n::__('{field} 必须小于 {rule}'),
            'equal_less_than'                              => I18n::__('{field} 必须小于等于 {rule}'),
            'equal'                                        => I18n::__('{field} 必须等于 {rule}'),
            'not_equal'                                    => I18n::__('{field} 不能等于 {rule}'),
            'equal_to'                                     => I18n::__('{field} 必须等于字段 {rule}'),
            'different'                                    => I18n::__('{field} 不能等于字段 {rule}'),
            'same'                                         => I18n::__('{field} 必须完全等于 {rule}'),
            'not_same'                                     => I18n::__('{field} 不能完全等于 {rule}'),
            'empty'                                        => I18n::__('{field} 必须为空'),
            'not_empty'                                    => I18n::__('{field} 不能为空'),
            'null'                                         => I18n::__('{field} 必须 null'),
            'not_null'                                     => I18n::__('{field} 不能为 null'),
            'strlen'                                       => I18n::__('{field} 长度不符合要求 {rule}'),
            'max'                                          => I18n::__('{field} 长度不能超过 {rule}'),
            'min'                                          => I18n::__('{field} 长度不能小于 {rule}'),
            'digit'                                        => I18n::__('{field} 字符串中的字符必须都是数字'),
            'type'                                         => I18n::__('{field} 类型不符合要求 {rule}'),
            'lower'                                        => I18n::__('{field} 必须全部是小写'),
            'upper'                                        => I18n::__('{field} 必须全部是大写'),
            'min_length'                                   => I18n::__('{field} 不满足最小长度 {rule}'),
            'max_length'                                   => I18n::__('{field} 不满足最大长度 {rule}'),
            'id_card'                                      => I18n::__('{field} 必须是有效的中国大陆身份证'),
            'zip_code'                                     => I18n::__('{field} 必须是有效的中国邮政编码'),
            'qq'                                           => I18n::__('{field} 必须是有效的 QQ 号码'),
            'phone'                                        => I18n::__('{field} 必须是有效的电话号码或者手机号'),
            'mobile'                                       => I18n::__('{field} 必须是有效的手机号'),
            'telephone'                                    => I18n::__('{field} 必须是有效的电话号码'),
            'email'                                        => I18n::__('{field} 必须为正确的电子邮件格式'),
            'luhn'                                         => I18n::__('{field} 必须为正确的符合 luhn 格式算法银行卡'),
            'after'                                        => I18n::__('{field} 日期不能小于 {rule}'),
            'before'                                       => I18n::__('{field} 日期不能超过 {rule}'),
            'allowed_ip'                                   => I18n::__('{field} 不允许的 IP 访问 {rule}'),
            'deny_ip'                                      => I18n::__('{field} 禁止的 IP 访问 {rule}'),
            'json'                                         => I18n::__('{field} 不是有效的 JSON'),
            'unique'                                       => I18n::__('{field} 不能出现重复值'),
        ]);
    }
}
