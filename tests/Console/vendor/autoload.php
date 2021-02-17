<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;

if (!class_exists('ComposerMock', false)) {
    class ComposerMock extends ClassLoader
    {
        public function getPrefixesPsr4()
        {
            return [
                'App\\' => [dirname(__DIR__)],
            ];
        }
    }
}

return new ComposerMock();
