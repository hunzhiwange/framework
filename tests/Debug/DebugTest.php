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

use Error;
use Exception;
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

        // twice same with once
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

    public function testEnable()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->disable();

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertNotContains('{"foo":"bar","@trace":', $content);

        $this->assertNotContains('"php":{"version":', $content);

        $this->assertNotContains('Starts from this moment with QueryPHP.', $content);

        $debug->enable();

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        $this->assertContains('{"foo":"bar","@trace":', $content);

        $this->assertContains('"php":{"version":', $content);

        $this->assertContains('Starts from this moment with QueryPHP.', $content);
    }

    public function testEnableWithoutBootstrap()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->disable();

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertNotContains('{"foo":"bar","@trace":', $content);

        $this->assertNotContains('"php":{"version":', $content);

        $this->assertNotContains('Starts from this moment with QueryPHP.', $content);

        $debug->enable();

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        $this->assertContains('{"foo":"bar","@trace":', $content);

        $this->assertContains('"php":{"version":', $content);

        $this->assertContains('Starts from this moment with QueryPHP.', $content);
    }

    public function testEnableTwiceSameWithOne()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->disable();

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertNotContains('{"foo":"bar","@trace":', $content);

        $this->assertNotContains('"php":{"version":', $content);

        $this->assertNotContains('Starts from this moment with QueryPHP.', $content);

        $debug->enable();

        $response2 = new JsonResponse(['foo' => 'bar']);

        $debug->handle($request, $response2);

        $content = $response2->getContent();

        $this->assertContains('{"foo":"bar","@trace":', $content);

        $this->assertContains('"php":{"version":', $content);

        $this->assertContains('Starts from this moment with QueryPHP.', $content);
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

    public function testWithSession()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $session = $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $session->set('test_session', 'test_value');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('"session":{"test_session":"test_value"},', $content);
    }

    public function testWithLog()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $log = $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $log->info('test_log', ['exends' => 'bar']);
        $log->debug('test_log_debug');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('"logs":{"count":2,"messages":[', $content);

        $this->assertContains('{"message":"test_log","message_html":null,"is_string":true,"label":"info"', $content);

        $this->assertContains('{"message":"test_log_debug","message_html":null,"is_string":true,"label":"debug"', $content);
    }

    public function testTime()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->time('time_test');

        sleep(1);

        $debug->end('time_test');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('"time":{"start"', $content);

        $this->assertContains('"measures":[{"label":"time_test","start":', $content);

        $this->assertContains('"duration_str":"1s",', $content);
    }

    public function testTimeWithLabel()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->time('time_test', 'time_label');

        sleep(1);

        $debug->end('time_test');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('"time":{"start"', $content);

        $this->assertContains('"measures":[{"label":"time_label","start":', $content);

        $this->assertContains('"duration_str":"1s",', $content);
    }

    public function testEndWithNoStartDoNothing()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->end('time_without_start');

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('"time":{"start"', $content);

        $this->assertContains('"measures":[]', $content);
    }

    public function testAddTime()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->addTime('time_test', 1, 5);

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('"time":{"start"', $content);

        $this->assertContains('"measures":[{"label":"time_test","start":1', $content);

        $this->assertContains('"end":5,', $content);

        $this->assertContains('"relative_end":5,', $content);

        $this->assertContains('"duration":4,', $content);

        $this->assertContains('"duration_str":"4s",', $content);
    }

    public function testClosureTime()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->closureTime('time_test', function () {
            sleep(1);
        });

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('"time":{"start"', $content);

        $this->assertContains('"measures":[{"label":"time_test","start":', $content);

        $this->assertContains('"duration_str":"1s",', $content);
    }

    public function testException()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->exception(new Exception('test_exception'));

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('"exceptions":{"count":1,"exceptions":[', $content);

        $this->assertContains('"type":"Exception",', $content);

        $this->assertContains('"message":"test_exception",', $content);

        $this->assertContains('"code":0,', $content);

        $this->assertContains('$response = new JsonResponse([\'foo\' => \'bar\']);', $content);

        $this->assertContains('$debug->exception(new Exception(\'test_exception\'));', $content);
    }

    public function testExceptionWithError()
    {
        $debug = new Debug($project = new Project());

        $project->instance('session', $this->createSession());

        $project->instance('log', $this->createLog());

        $project->instance('option', $this->createOption());

        $debug->bootstrap();

        $request = new Request();
        $response = new JsonResponse(['foo' => 'bar']);

        $debug->exception(new Error('test_error'));

        $debug->handle($request, $response);

        $content = $response->getContent();

        $this->assertContains('"exceptions":{"count":1,"exceptions":[', $content);

        $this->assertContains('"type":"Error",', $content);

        $this->assertContains('"message":"test_error",', $content);

        $this->assertContains('"code":0,', $content);

        $this->assertContains('$response = new JsonResponse([\'foo\' => \'bar\']);', $content);

        $this->assertContains('$debug->exception(new Error(\'test_error\'));', $content);
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
            'debug' => [
                'json'       => true,
                'console'    => true,
                'javascript' => true,
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
