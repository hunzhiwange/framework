<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Validator.url",
 *     zh-CN:title="验证器.验证是否为 URL 地址",
 *     path="validate/validator/url",
 *     zh-CN:description="",
 * )
 */
final class UrlTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     *
     * @api(
     *     zh-CN:title="验证通过的数据",
     *     zh-CN:description="
     * 以下是通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\UrlTest::class, 'baseUseProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse($value): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'url',
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        // http://php.net/manual/en/filter.filters.validate.php#110411
        return [
            ['http://www.google.com'],
            ['http://queryphp.com'],
            ['http://baidu.com'],
            ['ftp://ftp.is.co.za.example.org/rfc/rfc1808.txt'],
            ['gopher://spinaltap.micro.umn.example.edu/00/Weather/California/Los%20Angeles'],
            ['http://www.math.uio.no.example.net/faq/compression-faq/part1.html'],
            ['mailto:mduerst@ifi.unizh.example.gov'],
            ['news:comp.infosystems.www.servers.unix'],
            ['telnet://melvyl.ucop.example.edu/'],
            ['http://www.ietf.org/rfc/rfc2396.txt'],
            ['ldap://[2001:db8::7]/c=GB?objectClass?one'],
            ['mailto:John.Doe@example.com'],
            ['news:comp.infosystems.www.servers.unix'],
            ['telnet://192.0.2.16:80/'],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     *
     * @api(
     *     zh-CN:title="未验证通过的数据",
     *     zh-CN:description="
     * 以下是未通过的校验数据示例。
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\UrlTest::class, 'badProvider')]}
     * ```
     *
     * 上面的数据是测试的数据提供者。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBad($value): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'url',
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['not numeric'],
            [[]],
            [new \stdClass()],
            [['foo', 'bar']],
            [[1, 2]],
            ['tel:+1-816-555-1212'],
            ['foo'],
            ['bar'],
            ['urn:oasis:names:specification:docbook:dtd:xml:4.1.2'],
            ['world'],
            [null],
        ];
    }
}
