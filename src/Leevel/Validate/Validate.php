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
use Leevel\I18n\Helper\gettext;
use function Leevel\I18n\Helper\gettext as __;

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
        Validator::initMessages(['required'                => __('{field} 不能为空'),
            'number'                                       => __('{field} 必须是数字'),
            'float'                                        => __('{field} 必须是浮点数'),
            'double'                                       => __('{field} 必须是双精度浮点数'),
            'boolean'                                      => __('{field} 必须是布尔值'),
            'array'                                        => __('{field} 必须是数组'),
            'accepted'                                     => __('{field} 必须是 yes、on、true 或者 1'),
            'date'                                         => __('{field} 不是正确的日期格式'),
            'date_format'                                  => __('{field} 必须使用日期格式 {rule}'),
            'timezone'                                     => __('{field} 不是正确的时区'),
            'alpha'                                        => __('{field} 只能是字母'),
            'alpha_upper'                                  => __('{field} 只能是大写字母'),
            'alpha_lower'                                  => __('{field} 只能是小写字母'),
            'alpha_num'                                    => __('{field} 只能是字母和数字'),
            'alpha_dash'                                   => __('{field} 只能是字母、数字、短横线和下划线'),
            'chinese'                                      => __('{field} 只能是汉字'),
            'chinese_alpha_num'                            => __('{field} 只能是汉字、字母、数字'),
            'chinese_alpha_dash'                           => __('{field} 只能是汉字、字母、数字、短横线和下划线'),
            'url'                                          => __('{field} 不是有效的 URL 地址'),
            'active_url'                                   => __('{field} 不是有效的域名或者 IP'),
            'ip'                                           => __('{field} 不是有效的 IP 地址'),
            'ipv4'                                         => __('{field} 不是有效的 IPV4 地址'),
            'ipv6'                                         => __('{field} 不是有效的 IPV6 地址'),
            'in'                                           => __('{field} 必须在 {rule} 范围内'),
            'not_in'                                       => __('{field} 不能在 {rule} 范围内'),
            'between'                                      => __('{field} 只能在 {rule} 和 {rule1} 之间，不包含等于'),
            'not_between'                                  => __('{field} 不在 {rule} 和 {rule1} 之间，不包含等于'),
            'between_equal'                                => __('{field} 只能在 {rule} 和 {rule1} 之间，包含等于'),
            'not_between_equal'                            => __('{field} 不在 {rule} 和 {rule1} 之间，包含等于'),
            'greater_than'                                 => __('{field} 必须大于 {rule}'),
            'equal_greater_than'                           => __('{field} 必须大于等于 {rule}'),
            'less_than'                                    => __('{field} 必须小于 {rule}'),
            'equal_less_than'                              => __('{field} 必须小于等于 {rule}'),
            'equal'                                        => __('{field} 必须等于 {rule}'),
            'not_equal'                                    => __('{field} 不能等于 {rule}'),
            'equal_to'                                     => __('{field} 必须等于字段 {rule}'),
            'different'                                    => __('{field} 不能等于字段 {rule}'),
            'same'                                         => __('{field} 必须完全等于 {rule}'),
            'not_same'                                     => __('{field} 不能完全等于 {rule}'),
            'empty'                                        => __('{field} 必须为空'),
            'not_empty'                                    => __('{field} 不能为空'),
            'null'                                         => __('{field} 必须 null'),
            'not_null'                                     => __('{field} 不能为 null'),
            'strlen'                                       => __('{field} 长度不符合要求 {rule}'),
            'max'                                          => __('{field} 长度不能超过 {rule}'),
            'min'                                          => __('{field} 长度不能小于 {rule}'),
            'digit'                                        => __('{field} 字符串中的字符必须都是数字'),
            'type'                                         => __('{field} 类型不符合要求 {rule}'),
            'lower'                                        => __('{field} 必须全部是小写'),
            'upper'                                        => __('{field} 必须全部是大写'),
            'min_length'                                   => __('{field} 不满足最小长度 {rule}'),
            'max_length'                                   => __('{field} 不满足最大长度 {rule}'),
            'id_card'                                      => __('{field} 必须是有效的中国大陆身份证'),
            'zip_code'                                     => __('{field} 必须是有效的中国邮政编码'),
            'qq'                                           => __('{field} 必须是有效的 QQ 号码'),
            'phone'                                        => __('{field} 必须是有效的电话号码或者手机号'),
            'mobile'                                       => __('{field} 必须是有效的手机号'),
            'telephone'                                    => __('{field} 必须是有效的电话号码'),
            'email'                                        => __('{field} 必须为正确的电子邮件格式'),
            'luhn'                                         => __('{field} 必须为正确的符合 luhn 格式算法银行卡'),
            'after'                                        => __('{field} 日期不能小于 {rule}'),
            'before'                                       => __('{field} 日期不能超过 {rule}'),
            'allowed_ip'                                   => __('{field} 不允许的 IP 访问 {rule}'),
            'deny_ip'                                      => __('{field} 禁止的 IP 访问 {rule}'),
            'json'                                         => __('{field} 不是有效的 JSON'),
            'unique'                                       => __('{field} 不能出现重复值'),
        ]);
    }
}

// import fn.
class_exists(gettext::class);
