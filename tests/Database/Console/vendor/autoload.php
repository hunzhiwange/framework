<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;

if (!class_exists('DatabaseComposerMock', false)) {
    class DatabaseComposerMock extends ClassLoader
    {
        public function findFile($class)
        {
            // mock for class `\\Common\\Index`
            return dirname(__DIR__).'/Index.php';
        }
    }
}

return new DatabaseComposerMock();
