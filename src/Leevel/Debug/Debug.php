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
use Leevel\Database\IDatabase;
use Leevel\Debug\DataCollector\FilesCollector;
use Leevel\Debug\DataCollector\LeevelCollector;
use Leevel\Debug\DataCollector\LogsCollector;
use Leevel\Debug\DataCollector\SessionCollector;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Http\ApiResponse;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Throwable;

/**
 * 调试器.
 *
 * I actually copied a lot of ideas from laravel-debugbar app.
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
    protected IContainer $container;

    /**
     * 是否启用调试.
     *
     * @var bool
     */
    protected bool $enabled = true;

    /**
     * 是否已经初始化引导
     *
     * @var bool
     */
    protected bool $isBootstrap = false;

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
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
     * 返回应用管理.
     *
     * @return \Leevel\Di\IContainer
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value): self
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
    public function handle(IRequest $request, IResponse $response): void
    {
        if (!$this->enabled) {
            return;
        }

        if (
                $request->isJson() ||
                $response instanceof ApiResponse ||
                $response instanceof JsonResponse ||
                $response->isJson()
            ) {
            if ($this->option['json'] && is_array($data = $response->getData())) {
                $jsonRenderer = $this->getJsonRenderer();

                if (array_values($data) !== $data) {
                    $data[':trace'] = $jsonRenderer->render();
                } else {
                    $data[] = [':trace' => $jsonRenderer->render()];
                }

                $response->setData($data);
            }
        } elseif (!($response instanceof RedirectResponse)) {
            if ($this->option['javascript']) {
                $javascriptRenderer = $this->getJavascriptRenderer('/debugbar');
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
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * 启用调试.
     */
    public function enable(): void
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
    public function message($message, string $label = 'info'): void
    {
        $this->getCollector('messages')->addMessage($message, $label);
    }

    /**
     * 开始调试时间.
     *
     * @param string $name
     * @param string $label
     */
    public function time(string $name, ?string $label = null): void
    {
        $this->getCollector('time')->startMeasure($name, $label);
    }

    /**
     * 停止调试时间.
     *
     * @param string $name
     */
    public function end(string $name): void
    {
        try {
            $this->getCollector('time')->stopMeasure($name);
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
    public function addTime(string $label, float $start, float $end): void
    {
        $this->getCollector('time')->addMeasure($label, $start, $end);
    }

    /**
     * 调试闭包执行时间.
     *
     * @param string   $label
     * @param \Closure $closure
     */
    public function closureTime(string $label, Closure $closure): void
    {
        $this->getCollector('time')->measure($label, $closure);
    }

    /**
     * 添加异常.
     *
     * @param \Throwable $e
     */
    public function exception(Throwable $e): void
    {
        $this->getCollector('exceptions')->addThrowable($e);
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
    public function getJavascriptRenderer($baseUrl = null, $basePath = null): JavascriptRenderer
    {
        return new JavascriptRenderer($this, $baseUrl, $basePath);
    }

    /**
     * 初始化.
     */
    public function bootstrap(): void
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
        $this->addCollector(new LeevelCollector($this->container->make('app')));
        $this->addCollector(new SessionCollector($this->container->make('session')));
        $this->addCollector(new FilesCollector());
        $this->addCollector(new LogsCollector());

        $this->initData();
        $this->databaseEventDispatch();
        $this->logEventDispatch();

        $this->isBootstrap = true;
    }

    /**
     * 是否初始化.
     *
     * @return bool
     */
    public function isBootstrap(): bool
    {
        return $this->isBootstrap;
    }

    /**
     * 初始化数据.
     */
    protected function initData(): void
    {
        $this->message('Starts from this moment with QueryPHP.', '');

        $this
            ->getCollector('config')
            ->setData($this->container->make('option')->all());
    }

    /**
     * 设置数据库 SQL 监听器.
     */
    protected function databaseEventDispatch(): void
    {
        $this
            ->getEventDispatch()
            ->register(IDatabase::SQL_EVENT, function (string $event, string $sql, array $bindParams = []) {
                $this
                    ->getCollector('logs')
                    ->addMessage($sql.': '.json_encode($bindParams, JSON_UNESCAPED_UNICODE), 'sql');
            });
    }

    /**
     * 设置日志监听器.
     */
    protected function logEventDispatch(): void
    {
        $this->getEventDispatch()
            ->register(ILog::LOG_EVENT, function (string $event, string $level, string $message, array $context = []) {
                $this
                    ->getCollector('logs')
                    ->addMessage(File::formatMessage($level, $message, $context), $level);
            });
    }

    /**
     * 获取事件处理器.
     *
     * @return \Leevel\Event\IDispatch
     */
    protected function getEventDispatch(): IDispatch
    {
        return $this->container->make(IDispatch::class);
    }
}
