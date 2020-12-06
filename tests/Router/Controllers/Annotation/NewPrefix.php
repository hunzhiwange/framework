<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Annotation;

use Leevel\Di\IContainer;

/**
 * NewPrefix.
 */
class NewPrefix
{
    public function handle(IContainer $container): string
    {
        return 'hello plus for newPrefix, attributes petId is '.
            $container
                ->make('request')
                ->attributes
                ->get('petId');
    }
}
