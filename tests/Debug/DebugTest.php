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

namespace Tests\Debug;

use Leevel\Bootstrap\Project as Projects;
use Leevel\Debug\Debug;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Log\File as LogFile;
use Leevel\Log\ILog;
use Leevel\Log\Log;
use Leevel\Option\IOption;
use Leevel\Option\Option;
use Leevel\Session\File as SessionFile;
use Leevel\Session\ISession;
use Leevel\Session\Session;
use Tests\TestCase;

/**
 * debug test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.24
 *
 * @version 1.0
 */
class DebugTest extends TestCase
{
    public function testBaseUse()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new Response();

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('<link rel="stylesheet" type="text/css" href="/debugbar/vendor/font-awesome/css/font-awesome.min.css">', $content);

        $this->assertContains('<link rel="stylesheet" type="text/css" href="/debugbar/debugbar.css">', $content);

        $this->assertContains('var phpdebugbar = new PhpDebugBar.DebugBar()', $content);

        $this->assertContains("console.log( '%cThe PHP Framework For Code Poem As Free As Wind %c(http://www.queryphp.com)', 'font-weight: bold;color: #06359a;', 'color: #02d629;' );", $content);

        $this->assertContains('Starts from this moment with QueryPHP.', $content);
    }

    public function testJson()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('{"foo":"bar","@trace":', $content);

        $this->assertContains('"php":{"version":', $content);

        $this->assertContains('Starts from this moment with QueryPHP.', $content);
    }

    public function testDisable()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('{"foo":"bar","@trace":', $content);

        $this->assertContains('"php":{"version":', $content);

        $this->assertContains('Starts from this moment with QueryPHP.', $content);

        $debug->disable();

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        $this->assertNotContains('{"foo":"bar","@trace":', $content);

        $this->assertNotContains('"php":{"version":', $content);

        $this->assertNotContains('Starts from this moment with QueryPHP.', $content);
    }

    /**
     * @dataProvider getMessageLevelsData
     *
     * @param string $level
     */
    public function testMessageLevelsData(string $level)
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->{$level}('hello', 'world');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('{"foo":"bar","@trace":', $content);

        $this->assertContains('"php":{"version":', $content);

        $this->assertContains('Starts from this moment with QueryPHP.', $content);

        $this->assertContains('{"message":"hello","message_html":null,"is_string":true,"label":"'.$level.'",', $content);
        $this->assertContains('{"message":"world","message_html":null,"is_string":true,"label":"'.$level.'",', $content);
    }

    public function getMessageLevelsData()
    {
        return [
            ['emergency'], ['alert'], ['critical'],
            ['error'], ['warning'], ['notice'],
            ['info'], ['debug'], ['log'],
        ];
    }

    protected function createSession(): ISession
    {
        $file = new SessionFile([
            'path' => __DIR__.'/cacheFile',
        ]);

        $session = new Session($file);

        $this->assertInstanceof(ISession::class, $session);

        return $session;
    }

    protected function createLog(): ILog
    {
        $file = new LogFile([
            'path' => __DIR__.'/cacheLog',
        ]);

        $log = new Log($file);

        $this->assertInstanceof(ILog::class, $log);

        return $log;
    }

    protected function createOption(): IOption
    {
        $data = [
            'app' => [
                'environment'       => 'environment',
            ],
        ];

        $option = new Option($data);

        $this->assertInstanceof(IOption::class, $option);

        return $option;
    }
}

class Project extends Projects
{
    protected function registerBaseProvider()
    {
    }
}
