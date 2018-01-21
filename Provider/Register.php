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
namespace Queryyetsimple\Swoole\Provider;

use Queryyetsimple\{
    Swoole\Server,
    Support\Provider,
    Swoole\Http\Server as HttpServer,
    Swoole\Websocket\Server as WebsocketServer
};

/**
 * swoole 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.12.21
 * @version 1.0
 */
class Register extends Provider
{
    
    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        $this->swooleServer();
        $this->swooleHttpServer();
        $this->swooleWebsocketServer();
    }
    
    /**
     * bootstrap
     *
     * @return void
     */
    public function bootstrap()
    {
        $this->console();
    }
    
    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'swoole.default.server' => [
                'Qys\Swoole\Server',
                'Queryyetsimple\Swoole\Server'
            ],
            'swoole.http.server' => [
                'Qys\Swoole\Http\Server',
                'Queryyetsimple\Swoole\Http\Server'
            ],
            'swoole.websocket.server' => [
                'Qys\Swoole\Websocket\Server',
                'Queryyetsimple\Swoole\Websocket\Server'
            ]
        ];
    }

    /**
     * 注册 swoole 服务
     *
     * @return void
     */
    protected function swooleServer()
    {
        $this->singleton('swoole.default.server', function ($project) {
            return new Server($project['option']['swoole\server']);
        });
    }

    /**
     * 注册 swoole http 服务
     *
     * @return void
     */
    protected function swooleHttpServer()
    {
        $this->singleton('swoole.http.server', function ($project) {
            $arrOption = array_merge($project['option']['swoole\server'], $project['option']['swoole\http_server']);
            return new HttpServer($project['router'], $project['request'], $project['response'], $arrOption);
        });
    }
    
    /**
     * 注册 swoole websocket 服务
     *
     * @return void
     */
    protected function swooleWebsocketServer()
    {
        $this->singleton('swoole.websocket.server', function ($project) {
            $arrOption = array_merge($project['option']['swoole\server'], $project['option']['swoole\websocket_server']);
            return new WebsocketServer($project['router'], $project['request'], $project['response'], $arrOption);
        });
    }

    /**
     * 载入命令包
     *
     * @return void
     */
    protected function console()
    {
        $this->loadCommandNamespace('Queryyetsimple\Swoole\Console');
    }
}
