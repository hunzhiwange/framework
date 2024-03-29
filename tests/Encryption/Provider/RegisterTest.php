<?php

declare(strict_types=1);

namespace Tests\Encryption\Provider;

use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Encryption\IEncryption;
use Leevel\Encryption\Provider\Register;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());

        $test->register();

        $container->alias($test->providers());

        $encryption = $container->make('encryption');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        static::assertFalse($sourceMessage === $encodeMessage);

        static::assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );

        static::assertSame(
            $encryption->decrypt($encodeMessage.'foo'),
            false
        );
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $config = new Config([
            'app' => [
                'auth_key' => '7becb888f518b20224a988906df51e05',
                'auth_cipher' => 'AES-256-CBC',
                'auth_rsa_private' => '',
                'auth_rsa_public' => '',
            ],
        ]);

        $container->singleton('config', $config);

        return $container;
    }
}
