<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;

if (!class_exists('ComposerMock', false)) {
    class ComposerMock extends ClassLoader
    {
        public function findFile($class)
        {
            // mock for class `\\App\\Index`
            return dirname(__DIR__).'/Index.php';
        }
    }
}

return new ComposerMock();
