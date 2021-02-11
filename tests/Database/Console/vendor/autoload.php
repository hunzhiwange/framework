<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;

if (!class_exists('DatabaseComposerMock', false)) {
    class DatabaseComposerMock extends ClassLoader
    {
        public function findFile($class)
        {
            // mock for class `\\App`
            return dirname(__DIR__);
        }
    }
}

return new DatabaseComposerMock();
