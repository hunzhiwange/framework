# v1.0.3

## Optimized

- PHP 8 compatiblD

# v1.0.2

## Added

- [#dfb82ea](https://github.com/hunzhiwange/framework/commit/ef17c7be35b31e42a117d489d8d4ab3f90d3620f) feat(cache): add clearCacheLoaded for Leevel\Cache\Load.

## Fixed

- [#ea43842](https://github.com/hunzhiwange/framework/commit/ea43842dd96054258948e8c623d60279b0430c29) fix(cache): clear loaded cache data of Leevel\Cache\Load when refresh.
- [#cec09bc](https://github.com/hunzhiwange/framework/commit/cec09bc7146c0d48c5c97c61e69e41dee40ac0af) fix(database): fix class Leevel\Database\Ddd\Repository's method params type comment.
- [#a652423](https://github.com/hunzhiwange/framework/commit/a65242334c42641e31d1f58a1e087651741c795a) fix(database): fix class Leevel\Database\Ddd\Select @ method databaseSelect return type error.
- [#b5529d3](https://github.com/hunzhiwange/framework/commit/b5529d340f176605ab2740d7cb919c9070e99b1b) fix(console): fix Leevel\Console\RunCommand param comment
- [#0797959](https://github.com/hunzhiwange/framework/commit/07979595683fbbf7b48f03724f54b49d7da8dc4f) tests(console): fix Tests\Console\BaseCommand:runCommand param type
- [#475c7d9](https://github.com/hunzhiwange/framework/commit/475c7d9133d4ba03e3ab4562127949d79f59217d) fix(kernel): fix setTestProperty and getTestProperty
- [#5cc7175](https://github.com/hunzhiwange/framework/commit/5cc717504f7c399da96ea69989f53f8872a1e007) fix(encryption): fix Encrypt cipher was not found
- [#09423ad](https://github.com/hunzhiwange/framework/commit/09423ade009d3c35f0a5ded9cca13ebb824de11a) fix(database): fix Leevel\Database\Console\SeedRun

## Optimized

- [#d16114f](https://github.com/hunzhiwange/framework/commit/d16114fac898f2d3b4fcc97828a4f23be568aa05) refactor(view): use PHP_EOL to replace \n
- [#6409e26](https://github.com/hunzhiwange/framework/commit/6409e264bdc280c1c2ae04d2a9ab03f3bfd02f24) refactor: clear invalid commands of Phinx when execute `composer dump-autoload --optimize --no-dev`
- [#0becd48](https://github.com/hunzhiwange/framework/commit/0becd48eedef45be917af52f85ea2cdc69ecb084) refactor: fix database migrate
- [#074b45f](https://github.com/hunzhiwange/framework/commit/074b45f869c9a060f708dba33f6539aca77ee465) refactor(kernel): make Leevel\Kernel\Console\Autoload code clean
- [#538013f](https://github.com/hunzhiwange/framework/commit/538013f21efbe8bd110fdcea55662cbf42bdf2cf) refactor(kernel): refact clear invalid commands code
- [#25ba54d](https://github.com/hunzhiwange/framework/commit/25ba54dce0d93406aa595a1b7f2137cea7048aed) refactor(cache): remove redis extension loaded check of Leevel\Cache\Redis\PhpRedis and remove @ codeCoverageIgnore
- [#5e234cc](https://github.com/hunzhiwange/framework/commit/5e234ccc008b38e549fb32f4a4902887ce7ad5a9) refactor(database): remove bad code of Leevel\Database\Console\Entity
- [#1e5ff0b](https://github.com/hunzhiwange/framework/commit/1e5ff0ba0bce91dbee15bda5b8032829b2fa47d3) refactor(di): refact newInstanceArgs and rename `$classname` to `$className`
- [#3213c39](https://github.com/hunzhiwange/framework/commit/3213c398a360c4d5fa61f1f4ae8e87692331649b) chore: add league/flysystem-sftp and league/flysystem-ziparchive to require-dev
- [#e6112f5](https://github.com/hunzhiwange/framework/commit/e6112f508f7782e7aa773be154984eb3122925fa) refactor(option): fix for composer 2.0
- [#155465d](https://github.com/hunzhiwange/framework/commit/155465d2572b3135ba318e941bf965442f66f975) chore: update build composer and phpunt version
- [#1f69242](https://github.com/hunzhiwange/framework/commit/1f692429a047edc502d604f74bcb6f85fd13bbee) chore: remove phpunit/phpunit and phpunit/php-token-stream from composer.json
- [#91d1861](https://github.com/hunzhiwange/framework/commit/91d186173a1034df390162d7b334963cf34c7ece) chore: move robmorgan/phinx to composer require-dev

## Changed

- [#d3ddf39](https://github.com/hunzhiwange/framework/commit/d3ddf396845b50f17b77d3b1a416982c80c7d063) refactor(console): remove some less commonly used method
- [#f237e52](https://github.com/hunzhiwange/framework/commit/f237e529f9bde4e13f99e4dfc8545970567444a3) refactor(kernel): remove parameter $throwException from Leevel\Kernel\App method namespacePath

## Tests && Docs

- [#71a090c](https://github.com/hunzhiwange/framework/commit/71a090ce8504d77445783e562ae8691c32bd7886) tests(cache): add tests and doc for Leevel\Cache\Load::clearCacheLoaded.
- [#7bf76eb](https://github.com/hunzhiwange/framework/commit/7bf76ebe892be1ea541d6fc6d9dadb2a71fa0508) tests(session): add tests for Leevel\Session\Console\Clear.
- [#e0b51c0](https://github.com/hunzhiwange/framework/commit/e0b51c00397057e2d10d0b5ee9df4912ecf1d1a0) tests(view): add tests for Leevel\View\Console\Clear.
- [#848b46c](https://github.com/hunzhiwange/framework/commit/848b46cf4c367eb52770c4b9625be3ec25d6e11f) tests: add tests for helper not found.
- [#1413568](https://github.com/hunzhiwange/framework/commit/1413568f17f6b5860a510e1d77f8c447463211e8) tests(database): add tests for database migrate command
- [#d447693](https://github.com/hunzhiwange/framework/commit/d447693e05b6708cc93e62fbe0d942cb14728ff1) tests(cache): add test testWithPassword
- [#258faed](https://github.com/hunzhiwange/framework/commit/258faede3f3a4b3d1c2c924037dc1afab9304dc7) tests(console): add $extendCommands to runCommand of trait Tests\Console\BaseCommand
- [#21e4ead](https://github.com/hunzhiwange/framework/commit/21e4ead29daacf9dae34155dbe80b9173fd12b95) tests(debug): add tests Tests\Debug\Console\LinkDebugBarTest
- [#e74c68a](https://github.com/hunzhiwange/framework/commit/e74c68a136bbe49a70dc1c1ec10170894986d6cf) tests(kernel): clean tests command
- [#10edab6](https://github.com/hunzhiwange/framework/commit/10edab653aa0337218deed94af063f1cb98988c1) tests(filesytem): add tests for filesystem
- [#8f2f479](https://github.com/hunzhiwange/framework/commit/8f2f4794451f8e2477b2a0450e90388aaa68fe07) tests(kernel): add tests for link:* command
- [#33e6692](https://github.com/hunzhiwange/framework/commit/33e669216a81029341950fe0f259b507b7f1b854) tests(seccode): add test testDisplayImage
- [#956fdce](https://github.com/hunzhiwange/framework/commit/956fdcecde66a29c79d88364c18f6a735efb33d8) tests(kernel): add tests Tests\Kernel\Testing\HelperTest
- [#f6f0473](https://github.com/hunzhiwange/framework/commit/f6f047375f55ed39a35820bfc291af1300d5f2c2) tests(kernel): add tests Tests\Kernel\Testing\DatabaseTest
- [#d5eaf4c](https://github.com/hunzhiwange/framework/commit/d5eaf4c2d560bf8729c2dba2f643ce57f34a598b) tests(kernel): add tests Tests\Kernel\Utils\ClassParserTest
- [#cf381af](https://github.com/hunzhiwange/framework/commit/cf381af9ef2e3071e08c838ec2c7cd4386679564) tests(kernel): add tests kernel bootstraplassParserTest
- [#01d3949](https://github.com/hunzhiwange/framework/commit/01d3949d11ecafd73a9b8f265bda818729f3312a) tests(kernel): add tests testRenderForConsole
- [#5daf718](https://github.com/hunzhiwange/framework/commit/5daf7180ed0e031f70879620f6ec75aeef6b0fb4) tests(kernel): add tests testFunctionLang
- [#25cae7e](https://github.com/hunzhiwange/framework/commit/25cae7ecf531e72f79b442b07b4776585e836e77) tests(kernel): add tests Tests\Kernel\InspectorTest
- [#20a9bb8](https://github.com/hunzhiwange/framework/commit/20a9bb89412826c1bb396fe289497489e6d43e3e) tests(mail): add tests for mail
- [#d96c70d](https://github.com/hunzhiwange/framework/commit/d96c70dc5419e8d78cdca61a807581e3a43e4bac) tests(kernel): add tests for Leevel/Kernel/Utils
- [#2a495c3](https://github.com/hunzhiwange/framework/commit/2a495c3b32cbab5bd2574292e86562b96a7942e1) tests: fix tests/Docs/Preface/PrefaceSummaryDoc.php
- [#06ad7e9](https://github.com/hunzhiwange/framework/commit/06ad7e9a4985469f6a4f8b1d0ad84cb674159d5b) tests(encryption): add testEncryptDataFailed
- [#fcf6225](https://github.com/hunzhiwange/framework/commit/fcf6225a6d83229336a95ccd23e6cb1818b1fed9) tests(kernel): add tests for Leevel\Kernel\App:namespacePath
- [#001d844](https://github.com/hunzhiwange/framework/commit/001d84475780683b6a983871c46eab87c4eb1c1c) tests(kernel): add tests for Leevel\Kernel\Bootstrap\LoadOption
- [#6bea4e3](https://github.com/hunzhiwange/framework/commit/6bea4e3a17cf1aa5b516a85b90f3a3e9a1d36498) tests(kernel): add tests for Leevel\Kernel\ExceptionRuntimeoadOption
- [#fc36e0d](https://github.com/hunzhiwange/framework/commit/fc36e0df843ca1bbf99278e2b9c21cb15ab25567) tests(kernel): add tests for getConsoleApplication
- [#9ebc06f](https://github.com/hunzhiwange/framework/commit/9ebc06ff87dbd659ae0377f90205c10c8c113841) tests(kernel): add tests for kernel console
- [#8bdad89](https://github.com/hunzhiwange/framework/commit/8bdad89c8ba5e4130eddbf666101cf82a59e5d4f) tests(view): add test Tests\View\Console\CacheTest
- [#1b817b1](https://github.com/hunzhiwange/framework/commit/1b817b1f6d3267e86a46cb8ebb5032f954a46a60) tests(kernel): add test Tests\Kernel\Console\DocFrameworkTest
- [#4b3095b](https://github.com/hunzhiwange/framework/commit/4b3095bb86f4188cf08ab9fb1d2f1af5129631d8) tests(kernel): add test Tests\Kernel\Console\DocTest
- [#53a245e](https://github.com/hunzhiwange/framework/commit/53a245ec6bade9d67ac7d81dbc87b6c60b79c303) tests(kernel): add test Tests\Kernel\Console\IdeHelperFunctionTest
- [#e449509](https://github.com/hunzhiwange/framework/commit/e4495090d7ae7c58199cb6b04baf5fe2e9f30f41) tests: refact assertFileNotExists to assertFileDoesNotExist,assertDirectoryNotExists to assertDirectoryDoesNotExist when use phpunit 9
- [#5b59f7a](https://github.com/hunzhiwange/framework/commit/5b59f7a6364da0f477cdb2b695529d8576aefe84) tests: refact test_procedure migrate

# v1.0.1

## Fixed

- [#fadd998](https://github.com/hunzhiwange/framework/commit/fadd99826f2ae917df0534be22eabd17e59dae05) refactor(protocol): release root coroutine data for WebSocket.

## Optimized

- [#210a15f](https://github.com/hunzhiwange/framework/commit/210a15f710318d40dc115350afbb116bf7418b77) normalize code style.
- [#20b54bc](https://github.com/hunzhiwange/framework/commit/20b54bc1856bb8c835271f65fd57f42d87c7e789) normalize method description.
- [#1cfb217](https://github.com/hunzhiwange/framework/commit/1cfb217e8d4b454dff9ff2b2aa256276f1687132) refactor(auth): use @ method to implement proxy ide helper.
- [#7e65701](https://github.com/hunzhiwange/framework/commit/7e657012736cc95520cf70448882c0ed87635b76) refactor: chain support optional multi operation and add ide helper for assert.

## Added

- [#6c56b83](https://github.com/hunzhiwange/framework/commit/6c56b837e5083a64ca3ee8e20af574af253aa6a8) add feature \Leevel\Protocol\Coroutine::removeContext().

## Changed

- [#6c56b83](https://github.com/hunzhiwange/framework/commit/6c56b837e5083a64ca3ee8e20af574af253aa6a8) refactor(any): IOC container can manipulate data for a specified swoole coroutine id.

## Removed

- [#6ec26fa](https://github.com/hunzhiwange/framework/commit/6ec26fa92ffc8594623e3fb4da934082b0927a33) refactor(i18n): remove method __().
- [#0fdade6](https://github.com/hunzhiwange/framework/commit/0fdade66c9ad0a59293d987514916a0c1f66835c) refactor: remove option --proxy of command make:idehelper
- [#6437c03](https://github.com/hunzhiwange/framework/commit/6437c0350efa87c417877974b8eb491ad322b3f6) refactor(i18n): remove \Leevel\I18n\Proxy\I18n

## Tests && Docs

- [#10cc02e](https://github.com/hunzhiwange/framework/commit/10cc02e3d4823e95535b02da7a51b3ab88a2edfa) tests(protocol): add tests for \Leevel\Protocol\Coroutine::removeContext().
- [#2c5349a](https://github.com/hunzhiwange/framework/commit/347aad7e9a71cf5294e5bd63060419e573971472) tests(log): add \Tests\Log\Console\ClearTest.
- [#a24a05b](https://github.com/hunzhiwange/framework/commit/ad74c497b9ae9cbc41b3517fdfceabfc61e0d866) tests(database): add \Tests\Database\Console\EntityTest.
- [#83dfa30](https://github.com/hunzhiwange/framework/commit/83dfa300647c7144c22b63f546bc72297500d258) tests(validate): add invalid value null for Tests\Validate\Validator\RequiredTest.
