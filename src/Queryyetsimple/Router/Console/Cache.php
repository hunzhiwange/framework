<?php
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
namespace Leevel\Router\Console;

use Exception;
use Leevel\Console\{
    Option,
    Command,
    Argument
};
use Common\Infra\Provider\Router;
use Leevel\Router\ScanSwaggerRouter;

/**
 * swagger 路由缓存 
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.04.11
 * @version 1.0
 */
class Cache extends Command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'router:cache';

    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription = 'Swagger as the router.';

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        $this->line('Start to do convert swager to router.');

        try {
            $routers = (new Router(app()))->getRouters(true);

            $this->info(sprintf('Router file %s cache successed.', path_router_cache('router.php')));
        } catch (Exception $e) {
            $this->error($e->getmessage());
        }
    }

    /**
     * 命令参数
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * 命令配置
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
