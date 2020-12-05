# v1.1.0-alpha.3 - TBD

## Fixed

- [#4adb00f](https://github.com/hunzhiwange/framework/commit/4adb00f) fix(kernel): Fix doc

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