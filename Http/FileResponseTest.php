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
namespace Tests\Http;

use Tests\TestCase;
use Queryyetsimple\Http\FileResponse;
use Queryyetsimple\Http\ResponseHeaderBag;
    
/**
 * FileResponseTest test
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package
 * 
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.27
 * @version 1.0
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class FileResponseTest extends TestCase
{
    public function testConstruction()
    {
        $file = __DIR__ . '/../../README.md';

        $response = new FileResponse($file, 404, array('X-Header' => 'Foo'), null, true, true);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        $response = FileResponse::create($file, 404, array(), ResponseHeaderBag::DISPOSITION_INLINE);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($response->headers->has('ETag'));
        $this->assertEquals('inline; filename="README.md"', $response->headers->get('Content-Disposition'));
    }   
}

