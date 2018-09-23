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

namespace Leevel\Debug;

use Closure;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;
use Exception;
use Leevel\Debug\DataCollector\FilesCollector;
use Leevel\Debug\DataCollector\LeevelCollector;
use Leevel\Debug\DataCollector\LogsCollector;
use Leevel\Debug\DataCollector\SessionCollector;
use Leevel\Di\IContainer;
use Leevel\Http\ApiResponse;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Throwable;

/**
 * 调试器.
 *
 * @method void emergency($message)
 * @method void alert($message)
 * @method void critical($message)
 * @method void error($message)
 * @method void warning($message)
 * @method void notice($message)
 * @method void info($message)
 * @method void debug($message)
 * @method void log($message)
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.20
 *
 * @version 1.0
 */
class Debug extends DebugBar
{
    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 是否启用调试.
     *
     * @var bool
     */
    protected $enabled = true;

    /**
     * 是否已经初始化引导
     *
     * @var bool
     */
    protected $isBootstrap = false;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'json'       => true,
        'console'    => true,
        'javascript' => true,
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Di\IContainer $container
     * @param array                 $option
     */
    public function __construct(IContainer $container, array $option = [])
    {
        $this->container = $container;

        $this->option = array_merge($this->option, $option);
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        $messageLevels = [
            'emergency', 'alert', 'critical',
            'error', 'warning', 'notice',
            'info', 'debug', 'log',
        ];

        if (in_array($method, $messageLevels, true)) {
            foreach ($args as $arg) {
                $this->message($arg, $method);
            }
        }
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value)
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 响应.
     *
     * @param \Leevel\Http\IRequest  $request
     * @param \Leevel\Http\IResponse $response
     */
    public function handle(IRequest $request, IResponse $response)
    {
        if (!$this->enabled) {
            return;
        }

        if ((
                $response instanceof ApiResponse ||
                $response instanceof JsonResponse ||
                $response->isJson()
            ) &&
                is_array(($data = $response->getData()))) {
            if ($this->option['json']) {
                $jsonRenderer = $this->getJsonRenderer();
                $data['@trace'] = $jsonRenderer->render();

                $response->setData($data);
            }
        } elseif (!($response instanceof RedirectResponse)) {
            if ($this->option['javascript']) {
                $javascriptRenderer = $this->getJavascriptRenderer('debugbar');
                $response->appendContent(
                    $javascriptRenderer->renderHead().$javascriptRenderer->render()
                );
            }

            if ($this->option['console']) {
                $consoleRenderer = $this->getConsoleRenderer();
                $response->appendContent($consoleRenderer->render());
            }
        }
    }

    /**
     * 关闭调试.
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * 启用调试.
     */
    public function enable()
    {
        $this->enabled = true;

        if (!$this->isBootstrap) {
            $this->bootstrap();
        }
    }

    /**
     * 添加一条消息.
     *
     * @param mixed  $message
     * @param string $label
     */
    public function message($message, string $label = 'info')
    {
        $collector = $this->getCollector('messages');
        $collector->addMessage($message, $label);
    }

    /**
     * 开始调试时间.
     *
     * @param string $name
     * @param string $label
     */
    public function time(string $name, ?string $label = null)
    {
        $collector = $this->getCollector('time');
        $collector->startMeasure($name, $label);
    }

    /**
     * 停止调试时间.
     *
     * @param string $name
     */
    public function end(string $name)
    {
        $collector = $this->getCollector('time');

        try {
            $collector->stopMeasure($name);
        } catch (Exception $e) {
        }
    }

    /**
     * 添加一个时间调试.
     *
     * @param string $label
     * @param float  $start
     * @param float  $end
     */
    public function addTime(string $label, float $start, float $end)
    {
        $collector = $this->getCollector('time');
        $collector->addMeasure($label, $start, $end);
    }

    /**
     * 调试闭包执行时间.
     *
     * @param string   $label
     * @param \Closure $closure
     */
    public function closureTime(string $label, Closure $closure)
    {
        $collector = $this->getCollector('time');
        $collector->measure($label, $closure);
    }

    /**
     * 添加异常.
     *
     * @param \Throwable $e
     */
    public function exception(Throwable $e)
    {
        $collector = $this->getCollector('exceptions');
        $collector->addThrowable($e);
    }

    /**
     * 获取 JSON 渲染.
     */
    public function getJsonRenderer(): JsonRenderer
    {
        return new JsonRenderer($this);
    }

    /**
     * 获取 Console 渲染.
     */
    public function getConsoleRenderer(): ConsoleRenderer
    {
        return new ConsoleRenderer($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascriptRenderer($baseUrl = null, $basePath = null)
    {
        return new JavascriptRenderer($this, $baseUrl, $basePath);
    }

    /**
     * 初始化.
     */
    public function bootstrap()
    {
        if ($this->isBootstrap) {
            return;
        }

        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());
        $this->addCollector(new RequestDataCollector());
        $this->addCollector(new TimeDataCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new ExceptionsCollector());
        $this->addCollector(new ConfigCollector());
        $this->addCollector(new LeevelCollector($this->container));
        $this->addCollector(new SessionCollector($this->container->make('session')));
        $this->addCollector(new FilesCollector($this->container));
        $this->addCollector(new LogsCollector($this->container->make('log')));

        $this->initData();

        $this->isBootstrap = true;
    }

    /**
     * 初始化数据.
     */
    protected function initData()
    {
        $this->message('Starts from this moment with QueryPHP.', '');

        $this->getCollector('config')->setData($this->container->make('option')->all());
    }
}
