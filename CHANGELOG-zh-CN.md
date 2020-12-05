# v1.1.0-alpha.3 - TBD

## 修复

- [#4adb00f](https://github.com/hunzhiwange/framework/commit/4adb00f) fix(kernel): Fix doc

# v1.1.0-alpha.2

## 修复

- [#893e952](https://github.com/hunzhiwange/framework/commit/893e952) refactor(database): 修复实体无法设置数据库连接
- [#5f6dd5d](https://github.com/hunzhiwange/framework/commit/5f6dd5d) fix(protocol): 修复 Swoole 错误 Uncaught ErrorException: unsupported option [xxx] in @swoole-src/library/core/Server/Helper.php:160

## 优化

- [#95822e0](https://github.com/hunzhiwange/framework/commit/0dd96ff) refactor: Leevel\Cache\IBlock:handle 类添加 `mixed` 返回值类型
- [#d59554c](https://github.com/hunzhiwange/framework/commit/d59554c) refactor(database): 优化 make:entity 命令，去掉 @var 标记生成
- [#6f640e0](https://github.com/hunzhiwange/framework/commit/6f640e0),[#0dd96ff](https://github.com/hunzhiwange/framework/commit/0dd96ff),[#100eba2](https://github.com/hunzhiwange/framework/commit/100eba2) refactor: 使用 {@ inheritdoc} 从父类或者接口继承 docblock，减少重复注释
- [#ff58f3e](https://github.com/hunzhiwange/framework/commit/ff58f3e) refactor(view): 优化代码
- [#1c1b8c0](https://github.com/hunzhiwange/framework/commit/1c1b8c0) refactor(ddd): 优化实体代码

## 变更

- [#80fe1e9](https://github.com/hunzhiwange/framework/commit/80fe1e9),[#e512f2a](https://github.com/hunzhiwange/framework/commit/e512f2a),[#431f888](https://github.com/hunzhiwange/framework/commit/431f888) refactor(view): 模板大幅度精简和优化，启用新的模板语法标签
- [#b0d16bc](https://github.com/hunzhiwange/framework/commit/b0d16bc),[#1bb015c](https://github.com/hunzhiwange/framework/commit/1bb015c),[#bc8db8c](https://github.com/hunzhiwange/framework/commit/bc8db8c),[#75df836](https://github.com/hunzhiwange/framework/commit/75df836),[#283fb62](https://github.com/hunzhiwange/framework/commit/283fb62),[#e463354](https://github.com/hunzhiwange/framework/commit/e463354) refactor(router): 采用 PHP 8 属性实现注解路由，去掉之前的基于 OpenApi 3.0 的 Swagger-PHP 包的路由
- [#853613b](https://github.com/hunzhiwange/framework/commit/853613b) fix(kernel): 修复 IdeHelper 错误
- [#cd73dc4](https://github.com/hunzhiwange/framework/commit/cd73dc4) refactor: 删除 \Leevel\Router\View 和优化视图代码
- [#80a47ff](https://github.com/hunzhiwange/framework/commit/80a47ff) refactor(mail): 修复邮件中的视图，因为视图组件的变更

## 测试和文档

- [#f606ee7](https://github.com/hunzhiwange/framework/commit/f606ee7) docs: 文档方法添加返回类型 void
- [#75df836](https://github.com/hunzhiwange/framework/commit/75df836),[#e463354](https://github.com/hunzhiwange/framework/commit/e463354) docs(router): 更新路由文档

# v1.1.0-alpha.1

## 优化 

- 使用 PHP 8 新语法