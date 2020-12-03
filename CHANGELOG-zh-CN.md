# v1.1.0-alpha.2 - TBD

## 优化

- [#95822e0](https://github.com/hunzhiwange/framework/commit/0dd96ff) refactor: Leevel\Cache\IBlock:handle 类添加 `mixed` 返回值类型
- [#d59554c](https://github.com/hunzhiwange/framework/commit/d59554c) refactor(database): 优化 make:entity 命令，去掉 @var 标记生成
- [#6f640e0](https://github.com/hunzhiwange/framework/commit/6f640e0),[#0dd96ff](https://github.com/hunzhiwange/framework/commit/0dd96ff),[#100eba2](https://github.com/hunzhiwange/framework/commit/100eba2) refactor: 使用 {@ inheritdoc} 从父类或者接口继承 docblock，减少重复注释

## 变更

- [#80fe1e9](https://github.com/hunzhiwange/framework/commit/80fe1e9),[#e512f2a](https://github.com/hunzhiwange/framework/commit/e512f2a),[#431f888](https://github.com/hunzhiwange/framework/commit/431f888) refactor(view): 模板大幅度精简和优化，启用新的模板语法标签
- [#b0d16bc](https://github.com/hunzhiwange/framework/commit/b0d16bc),[#1bb015c](https://github.com/hunzhiwange/framework/commit/1bb015c),[#bc8db8c](https://github.com/hunzhiwange/framework/commit/bc8db8c),[#75df836](https://github.com/hunzhiwange/framework/commit/75df836),[#283fb62](https://github.com/hunzhiwange/framework/commit/283fb62),[#e463354](https://github.com/hunzhiwange/framework/commit/e463354) refactor(router): 采用 PHP 8 属性实现注解路由，去掉之前的基于 OpenApi 3.0 的 Swagger-PHP 包的路由

## 测试和文档

- [#f606ee7](https://github.com/hunzhiwange/framework/commit/f606ee7) docs: 文档方法添加返回类型 void
- [#75df836](https://github.com/hunzhiwange/framework/commit/75df836),[#e463354](https://github.com/hunzhiwange/framework/commit/e463354) docs(router): 更新路由文档

# v1.1.0-alpha.1

## 优化 

- 使用 PHP 8 新语法