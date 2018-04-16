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
use Leevel\Router\SwaggerRouter;

/**
 * swagger 路由缓存 
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.04.11
 * @version 1.0
 */
class Swagger extends Command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'router:swagger';

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
            $swaggerRouter = new SwaggerRouter($this->option('domain'), $controller = $this->getController());

            // 添加扫描目录
            $scandir = $this->getScandir($controller);
            foreach($scandir as $v) {
                $swaggerRouter->addSwaggerScan($v);
            }

            $result = $swaggerRouter->handle();

            $cache = $this->getCache();

            // 缓存路由
            $cacheDir = dirname($cache);
            if (! is_dir($cacheDir)) {
                mkdir($cacheDir, 0777, true);
            }

            file_put_contents($cache, '<?' . 'php /* ' . date('Y-m-d H:i:s') . ' */ ?' . '>' . 
                PHP_EOL . '<?' . 'php return ' . var_export($result, true) . '; ?' . '>');

            chmod($cache, 0777);

            $this->info(sprintf('Router file %s cache successed.', $cache));
        } catch (Exception $e) {
            $this->error($e->getmessage());
        }
    }

    /**
     * 获取控制器
     * 
     * @return string
     */
    protected function getController() 
    {
        $controller = $this->option('controller');
        if (! $controller) {
            throw new Exception('App controller dir is not set.');
        }
        
        $controller = str_replace('\\', '/', $controller);

        return $controller;
    }

    /**
     * 获取扫描目录
     * 
     * @param string $controller
     * @return array
     */
    protected function getScandir($controller) 
    {
        $scandir = [];

        $scandir[] = $this->getAppordir($controller);

        $extendScandir = $this->option('scandir');
        if (is_array($extendScandir)) {
            $scandir = array_merge($scandir, $extendScandir);
        } else {
            $scandir[] = $extendScandir;
        }

        return $scandir;
    }

    /**
     * 获取应用或者目录
     * 
     * @param string $controller
     * @return string
     */
    protected function getAppordir($controller) 
    {
        $appordir = $this->argument('appordir');
        if (! $appordir) {
            throw new Exception('App or dir is not set.');
        }

        if (! is_dir($appordir)) {
            $appordir = path_application($appordir . '/' . $controller);
        }

        if (! is_dir($appordir)) {
            throw new Exception(sprintf('App controller dir or the set dir is not exists.'));
        }

        return $appordir;
    }

    /**
     * 获取缓存路径
     * 
     * @return string
     */
    protected function getCache() 
    {
        $cache = $this->option('cache');
        if (! $cache) {
            throw new Exception('Cache name is not set.');
        }

        if (! is_dir($cache)) {
            $cache = path_router_cache($cache);
        }
        
        return $cache;
    }

    /**
     * 命令参数
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'appordir',
                Argument::OPTIONAL,
                'The app or an dir to be scan.',
                'app'
            ]
        ];
    }

    /**
     * 命令配置
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'cache',
                null,
                option::VALUE_OPTIONAL,
                'The router cache name.',
                'router.php'
            ],
            [
                'controller',
                null,
                option::VALUE_OPTIONAL,
                'The controller dir in an app.',
                'App/Controller'
            ],
            [
                'domain',
                null,
                option::VALUE_OPTIONAL,
                'The top domain.',
                'queryphp.cn'
            ],
            [
                'scandir',
                null,
                option::VALUE_OPTIONAL | option::VALUE_IS_ARRAY,
                'The swagger extends dir to be scan.'
            ]
        ];
    }
}
