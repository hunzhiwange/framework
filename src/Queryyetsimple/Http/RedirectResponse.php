<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Http;

use InvalidArgumentException;
use Queryyetsimple\Session\ISession;

/**
 * Redirect 响应请求
 * This class borrows heavily from the Symfony2 Framework and is part of the symfony package
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.28
 * @version 1.0
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class RedirectResponse extends Response
{

    /**
     * 目标 URL 地址
     * 
     * @var string
     */
    protected $targetUrl;

    /**
     * HTTP 请求
     *
     * @var \Queryyetsimple\Http\IRequest
     */
    protected $request;

    /**
     * SESSION 仓储
     *
     * @var \Queryyetsimple\Session\ISession
     */
    protected $session;

    /**
     * 构造函数
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return void
     */
    public function __construct(?string $url, int $status = 302, array $headers = [])
    {
        parent::__construct('', $status, $headers);

        $this->setTargetUrl($url);

        if (! $this->isRedirect()) {
            throw new InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
        }

        if (301 == $status && ! array_key_exists('cache-control', $headers)) {
            $this->headers->remove('cache-control');
        }
    }

    /**
     * 创建 URL 跳转响应
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return static
     */
    public static function create($url = '', int $status = 302, array $headers = [])
    {
        return new static($url, $status, $headers);
    }

    /**
     * 获取目标 URL 地址
     *
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * 设置目标 URL 地址
     *
     * @param string $url
     * @return $this
     */
    public function setTargetUrl($url)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (empty($url)) {
            throw new InvalidArgumentException('Cannot redirect to an empty URL.');
        }

        $this->targetUrl = $url;

        $this->setContent(
            sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url=%1$s" />
        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));

        $this->headers->set('Location', $url);

        return $this;
    }

    /**
     * 获取 HTTP 请求
     *
     * @return \Queryyetsimple\Http\IRequest|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * 设置 HTTP 请求
     *
     * @param \Queryyetsimple\Http\IRequest $request
     * @return void
     */
    public function setRequest(IRequest $request)
    {
        $this->request = $request;
    }

    /**
     * 获取 SESSION 仓储
     *
     * @return \Queryyetsimple\Session\ISession|null
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * 设置 SESSION 仓储
     *
     * @param \Queryyetsimple\Session\ISession $session
     * @return void
     */
    public function setSession(ISession $session)
    {
        $this->session = $session;
    }
}
