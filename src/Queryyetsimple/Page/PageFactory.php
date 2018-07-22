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

namespace Leevel\Page;

use Leevel\Http\IRequest;
use Leevel\Router\IUrl;

/**
 * 分页工厂
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.12
 *
 * @version 1.0
 */
class PageFactory implements IPageFactory
{
    /**
     * URL 生成.
     *
     * @var \Leevel\Router\IUrl
     */
    protected $url;

    /**
     * 构造函数.
     *
     * @param \Leevel\Router\IUrl $url
     */
    public function __construct(IUrl $url)
    {
        $this->url = $url;

        Page::setUrlResolver(function () use ($url) {
            return call_user_func_array(
                [$url, 'make'], func_get_args()
            );
        });
    }

    /**
     * 创建分页对象.
     *
     * @param int   $perPage
     * @param int   $totalRecord
     * @param array $option
     *
     * @return \Leevel\Page\Page
     */
    public function make(int $perPage, int $totalRecord, array $option = []): Page
    {
        return new Page($perPage, $totalRecord, $this->normalizeOption($option));
    }

    /**
     * 创建一个无限数据的分页对象.
     *
     * @param int   $perPage
     * @param array $optoin
     *
     * @return \Leevel\Page\Page
     */
    public function makeMacro(int $perPage, array $option = []): Page
    {
        return new Page($perPage, Page::MACRO, $this->normalizeOption($option));
    }

    /**
     * 创建一个只有上下页的分页对象.
     *
     * @param array $optoin
     *
     * @return \Leevel\Page\Page
     */
    public function makePrevNext(array $option = []): Page
    {
        return new Page(null, null, $this->normalizeOption($option));
    }

    /**
     * 格式化配置.
     *
     * @param array $optoin
     *
     * @return array
     */
    protected function normalizeOption(array $option): array
    {
        if (!isset($option['url'])) {
            $option['url'] = $this->url->getRequest()->getPathInfo();
        }

        if (!isset($option['parameter'])) {
            $parameter = $this->url->getRequest()->toArray();

            // 删除 nginx 重写的 _url
            if (isset($parameter[IRequest::PATHINFO_URL])) {
                unset($parameter[IRequest::PATHINFO_URL]);
            }

            $option['parameter'] = $parameter;
        }

        return $option;
    }
}
