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

namespace Leevel\Leevel\Bootstrap;

use Leevel\Kernel\IProject;
use Leevel\Option\Load;
use Leevel\Option\Option;
use Leevel\Support\Facade;
use RuntimeException;

/**
 * 读取配置.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.24
 *
 * @version 1.0
 */
class LoadOption
{
    /**
     * 响应.
     *
     * @param \Leevel\Kernel\IProject $project
     */
    public function handle(IProject $project): void
    {
        $this->checkRuntimeEnv($project);

        if ($project->isCachedOption()) {
            $data = (array) include $project->optionCachedPath();

            $this->setEnvs($data['app']['_env']);
        } else {
            $load = new Load($project->optionPath());

            $data = $load->loadData($project);
        }

        $project->instance('option', $option = new Option($data));

        $test = 2 === func_num_args();

        if (!$test) {
            // @codeCoverageIgnoreStart
            Facade::setContainer($project);

            $this->initialization($option);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * 载入运行时环境变量.
     *
     * @param \Leevel\Kernel\IProject $projecty
     */
    protected function checkRuntimeEnv(IProject $project)
    {
        if (!getenv('RUNTIME_ENVIRONMENT')) {
            return;
        }

        $file = '.'.getenv('RUNTIME_ENVIRONMENT');

        // 校验运行时环境，防止测试用例清空非测试库的业务数据
        if (!is_file($fullFile = $project->envPath().'/'.$file)) {
            throw new RuntimeException(sprintf('Env file `%s` was not found.', $fullFile));
        }

        $project->setEnvFile($file);
    }

    /**
     * 初始化处理.
     *
     * @param array $env
     */
    protected function setEnvs(array $env): void
    {
        foreach ($env as $name => $value) {
            $this->setEnvVar($name, $value);
        }
    }

    /**
     * 设置环境变量.
     *
     * @param string      $name
     * @param null|string $value
     */
    protected function setEnvVar($name, $value = null): void
    {
        if (is_bool($value)) {
            putenv($name.'='.($value ? '(true)' : '(false)'));
        } elseif (null === $value) {
            putenv($name.'(null)');
        } else {
            putenv($name.'='.$value);
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }

    /**
     * 初始化处理.
     *
     * @param \Leevel\Option\Option $option
     * @codeCoverageIgnore
     */
    protected function initialization(Option $option): void
    {
        mb_internal_encoding('UTF-8');

        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($option->get('time_zone', 'UTC'));
        }

        if (PHP_SAPI === 'cli') {
            return;
        }

        if (function_exists('gz_handler') && $option->get('start_gzip')) {
            ob_start('gz_handler');
        } else {
            ob_start();
        }
    }
}
