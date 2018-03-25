<?php
/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__ \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
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
use Queryyetsimple\Http\FileBag;
use Queryyetsimple\Http\UploadedFile;
    
/**
 * FileBagTest test
 * This class borrows heavily from the Symfony2 Framework and is part of the symfony package
 * 
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.25
 * @version 1.0
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class FileBagTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFileMustBeAnArrayOrUploadedFile()
    {
        new FileBag(array('file' => 'foo'));
    }    
    
    public function testShouldConvertsUploadedFiles()
    {
        $tmpFile = $this->createTempFile();

        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');

        $bag = new FileBag(array(
            'file' => array(
                'name' => basename($tmpFile),
                'type' => 'text/plain',
                'tmp_name' => $tmpFile,
                'error' => 0,
                'size' => null,
        )));
        
        $this->assertEquals($file, $bag->get('file'));
    }    
    
    protected function createTempFile()
    {
        $tempFile = sys_get_temp_dir() . '/form_test' . md5(time() . rand()) . '.tmp';
        file_put_contents($tempFile, '1');

        return $tempFile;
    }

    protected function setUp()
    {
        mkdir(sys_get_temp_dir() . '/form_test', 0777, true);
    }

    protected function tearDown()
    {
        foreach (glob(sys_get_temp_dir() . '/form_test/*') as $file) {
            unlink($file);
        }

        rmdir(sys_get_temp_dir() . '/form_test');
    }
}

