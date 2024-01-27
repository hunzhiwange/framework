# v1.1.0-beta.1 - TBD

# v1.1.0-alpha.3

## Added

- [#ddbfb16](https://github.com/hunzhiwange/framework/commit/ddbfb16) feat(database): Add withGlobalConnect and globalConnect for Entity

## Fixed

- [#4adb00f](https://github.com/hunzhiwange/framework/commit/4adb00f) fix(kernel): Fix doc
- [#e8cb516](https://github.com/hunzhiwange/framework/commit/e8cb516) fix(protocol): Fix option

## Optimized

- [#965c2a6](https://github.com/hunzhiwange/framework/commit/965c2a6) refactor: remove header comment of all PHP files
- [#c1fd027](https://github.com/hunzhiwange/framework/commit/c1fd027),[#2c832b8](https://github.com/hunzhiwange/framework/commit/2c832b8) style: idehelper
- [#736e0c3](https://github.com/hunzhiwange/framework/commit/736e0c3) refactor: remove Leevel\Console\Argument and Leevel\Console\Config
- [#8b3889b](https://github.com/hunzhiwange/framework/commit/8b3889b),[#a09725d](https://github.com/hunzhiwange/framework/commit/a09725d),[#6fa0307](https://github.com/hunzhiwange/framework/commit/6fa0307),[#4e52e43](https://github.com/hunzhiwange/framework/commit/4e52e43) refactor(router): Optimize annotation router
- [#a4701db](https://github.com/hunzhiwange/framework/commit/a4701db) refactor: Manager connect and reconnect return a specific interface
- [#e75f3f0](https://github.com/hunzhiwange/framework/commit/e75f3f0),[#b87617f](https://github.com/hunzhiwange/framework/commit/b87617f),[#40518ac](https://github.com/hunzhiwange/framework/commit/40518ac),[#8e622e7](https://github.com/hunzhiwange/framework/commit/8e622e7),[#2f6cac4](https://github.com/hunzhiwange/framework/commit/2f6cac4),[#263c968](https://github.com/hunzhiwange/framework/commit/263c968) refactor: Optimize MySQL pool
- [#f6b55ef](https://github.com/hunzhiwange/framework/commit/f6b55ef),[#4daf43b](https://github.com/hunzhiwange/framework/commit/4daf43b) fix(redis): Fix redis pool
- [#71718fa](https://github.com/hunzhiwange/framework/commit/71718fa) refactor(kernel): check Go RoadRunner server environment

## Changed

- [#0fbd875](https://github.com/hunzhiwange/framework/commit/0fbd875),[#878f434](https://github.com/hunzhiwange/framework/commit/878f434),[#ef4a92c](https://github.com/hunzhiwange/framework/commit/ef4a92c) refactor(view): Update view
- [#2d114c8](https://github.com/hunzhiwange/framework/commit/2d114c8) refactor(filesystem): Use new league/flysystem
- [#271282e](https://github.com/hunzhiwange/framework/commit/271282e) refactor(i18n): use new gettext version
- [#b6c1f6a](https://github.com/hunzhiwange/framework/commit/b6c1f6a) refactor: use new vlucas/phpdotenv version
- [#921c757](https://github.com/hunzhiwange/framework/commit/921c757) refactor(kernel): Optimize doc command
- [#3d4d775](https://github.com/hunzhiwange/framework/commit/3d4d775),[#401436d](https://github.com/hunzhiwange/framework/commit/401436d) refactor: Optimize database select cache

## Tests && Docs

- [#802b9e6](https://github.com/hunzhiwange/framework/commit/802b9e6) docs(router): update router doc

# v1.1.0-alpha.2

## Fixed

- [#893e952](https://github.com/hunzhiwange/framework/commit/893e952) refactor(database): Fix entity cannot set database connect
- [#5f6dd5d](https://github.com/hunzhiwange/framework/commit/5f6dd5d) fix(protocol): Fix Uncaught ErrorException: unsupported option [xxx] in @swoole-src/library/core/Server/Helper.php:160

## Optimized

- [#95822e0](https://github.com/hunzhiwange/framework/commit/0dd96ff) refactor: add `mixed` return type for Leevel\Cache\IBlock:handle
- [#d59554c](https://github.com/hunzhiwange/framework/commit/d59554c) refactor(database): fix make:entity command
- [#6f640e0](https://github.com/hunzhiwange/framework/commit/6f640e0),[#0dd96ff](https://github.com/hunzhiwange/framework/commit/0dd96ff),[#100eba2](https://github.com/hunzhiwange/framework/commit/100eba2) refactor: use {@ inheritdoc} to inhert docblock from parent class or interface
- [#ff58f3e](https://github.com/hunzhiwange/framework/commit/ff58f3e) refactor(view): optimize code
- [#1c1b8c0](https://github.com/hunzhiwange/framework/commit/1c1b8c0) refactor(ddd): optimize entity code

## Changed

- [#80fe1e9](https://github.com/hunzhiwange/framework/commit/80fe1e9),[#e512f2a](https://github.com/hunzhiwange/framework/commit/e512f2a),[#431f888](https://github.com/hunzhiwange/framework/commit/431f888) refactor(view): Template engine simplification and tag syntax update
- [#b0d16bc](https://github.com/hunzhiwange/framework/commit/b0d16bc),[#1bb015c](https://github.com/hunzhiwange/framework/commit/1bb015c),[#bc8db8c](https://github.com/hunzhiwange/framework/commit/bc8db8c),[#75df836](https://github.com/hunzhiwange/framework/commit/75df836),[#283fb62](https://github.com/hunzhiwange/framework/commit/283fb62),[#e463354](https://github.com/hunzhiwange/framework/commit/e463354) refactor(router): use php 8 attributes instead of `zircote/swagger-php` as annotation routing
- [#853613b](https://github.com/hunzhiwange/framework/commit/853613b) fix(kernel): Fix IdeHelper error
- [#cd73dc4](https://github.com/hunzhiwange/framework/commit/cd73dc4) refactor: Remove \Leevel\Router\View and optimize view code
- [#80a47ff](https://github.com/hunzhiwange/framework/commit/80a47ff) refactor(mail): Fix view for it has changed

## Tests && Docs

- [#f606ee7](https://github.com/hunzhiwange/framework/commit/f606ee7) docs: add void for doc return type
- [#75df836](https://github.com/hunzhiwange/framework/commit/75df836),[#e463354](https://github.com/hunzhiwange/framework/commit/e463354) docs: update router doc

# v1.1.0-alpha.1

## Optimized

- use PHP 8 new syntax