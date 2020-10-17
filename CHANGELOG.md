# v1.0.2 - TBD

## Added

- [#dfb82ea](https://github.com/hunzhiwange/framework/commit/ef17c7be35b31e42a117d489d8d4ab3f90d3620f) feat(cache): add clearCacheLoaded for Leevel\Cache\Load.

## Fixed

- [#ea43842](https://github.com/hunzhiwange/framework/commit/ea43842dd96054258948e8c623d60279b0430c29) fix(cache): clear loaded cache data of Leevel\Cache\Load when refresh.
- [#cec09bc](https://github.com/hunzhiwange/framework/commit/cec09bc7146c0d48c5c97c61e69e41dee40ac0af) fix(database): fix class Leevel\Database\Ddd\Repository's method params type comment.
- [#a652423](https://github.com/hunzhiwange/framework/commit/a65242334c42641e31d1f58a1e087651741c795a) fix(database): fix class Leevel\Database\Ddd\Select @ method databaseSelect return type error.
- [#b5529d3](https://github.com/hunzhiwange/framework/commit/b5529d340f176605ab2740d7cb919c9070e99b1b) fix(console): fix Leevel\Console\RunCommand param comment
- [#0797959](https://github.com/hunzhiwange/framework/commit/07979595683fbbf7b48f03724f54b49d7da8dc4f) tests(console): fix Tests\Console\BaseCommand:runCommand param type

## Optimized

- [#d16114f](https://github.com/hunzhiwange/framework/commit/d16114fac898f2d3b4fcc97828a4f23be568aa05) refactor(view): use PHP_EOL to replace \n
- [#6409e26](https://github.com/hunzhiwange/framework/commit/6409e264bdc280c1c2ae04d2a9ab03f3bfd02f24) refactor: clear invalid commands of Phinx when execute `composer dump-autoload --optimize --no-dev`
- [#0becd48](https://github.com/hunzhiwange/framework/commit/0becd48eedef45be917af52f85ea2cdc69ecb084) refactor: fix database migrate

## Tests && Docs

- [#71a090c](https://github.com/hunzhiwange/framework/commit/71a090ce8504d77445783e562ae8691c32bd7886) tests(cache): add tests and doc for Leevel\Cache\Load::clearCacheLoaded.
- [#7bf76eb](https://github.com/hunzhiwange/framework/commit/7bf76ebe892be1ea541d6fc6d9dadb2a71fa0508) tests(session): add tests for Leevel\Session\Console\Clear.
- [#e0b51c0](https://github.com/hunzhiwange/framework/commit/e0b51c00397057e2d10d0b5ee9df4912ecf1d1a0) tests(view): add tests for Leevel\View\Console\Clear.
- [#848b46c](https://github.com/hunzhiwange/framework/commit/848b46cf4c367eb52770c4b9625be3ec25d6e11f) tests: add tests for helper not found.
- [#1413568](https://github.com/hunzhiwange/framework/commit/1413568f17f6b5860a510e1d77f8c447463211e8) tests(database): add tests for database migrate command

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
