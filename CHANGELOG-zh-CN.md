# v1.1.0-beta.1 - TBD

# v1.1.0-alpha.3

## 新增

- [#ddbfb16](https://github.com/hunzhiwange/framework/commit/ddbfb16) feat(database): 领域实体增加 withGlobalConnect 和 globalConnect

## 修复

- [#4adb00f](https://github.com/hunzhiwange/framework/commit/4adb00f) fix(kernel): 修复文档
- [#e8cb516](https://github.com/hunzhiwange/framework/commit/e8cb516) fix(protocol): 修复配置

## 优化

- [#965c2a6](https://github.com/hunzhiwange/framework/commit/965c2a6) refactor: 删除所有 PHP 文件头部注释
- [#c1fd027](https://github.com/hunzhiwange/framework/commit/c1fd027),[#2c832b8](https://github.com/hunzhiwange/framework/commit/2c832b8) style: idehelper
- [#736e0c3](https://github.com/hunzhiwange/framework/commit/736e0c3) refactor: 删除 Leevel\Console\Argument 和 Leevel\Console\Option
- [#8b3889b](https://github.com/hunzhiwange/framework/commit/8b3889b),[#a09725d](https://github.com/hunzhiwange/framework/commit/a09725d),[#6fa0307](https://github.com/hunzhiwange/framework/commit/6fa0307),[#4e52e43](https://github.com/hunzhiwange/framework/commit/4e52e43) refactor(router): 优化注解路由
- [#a4701db](https://github.com/hunzhiwange/framework/commit/a4701db) refactor: 连接管理器方法 connect 和 reconnect 返回对应的组件的接口
- [#e75f3f0](https://github.com/hunzhiwange/framework/commit/e75f3f0),[#b87617f](https://github.com/hunzhiwange/framework/commit/b87617f),[#40518ac](https://github.com/hunzhiwange/framework/commit/40518ac),[#8e622e7](https://github.com/hunzhiwange/framework/commit/8e622e7),[#2f6cac4](https://github.com/hunzhiwange/framework/commit/2f6cac4),[#263c968](https://github.com/hunzhiwange/framework/commit/263c968) refactor: 优化 MySQL 数据库连接池
- [#f6b55ef](https://github.com/hunzhiwange/framework/commit/f6b55ef),[#4daf43b](https://github.com/hunzhiwange/framework/commit/4daf43b) fix(redis): 修复 Redis 连接池
- [#71718fa](https://github.com/hunzhiwange/framework/commit/71718fa) refactor(kernel): Go RoadRunner server 环境变量检测

## 变更 

- [#0fbd875](https://github.com/hunzhiwange/framework/commit/0fbd875),[#878f434](https://github.com/hunzhiwange/framework/commit/878f434),[#ef4a92c](https://github.com/hunzhiwange/framework/commit/ef4a92c) refactor(view): 更新视图
- [#2d114c8](https://github.com/hunzhiwange/framework/commit/2d114c8) refactor(filesystem): 使用新的 league/flysystem 版本支持 PHP 8
- [#271282e](https://github.com/hunzhiwange/framework/commit/271282e) refactor(i18n): 使用新的 gettext 版本支持 PHP 8
- [#b6c1f6a](https://github.com/hunzhiwange/framework/commit/b6c1f6a) refactor: 使用新的 vlucas/phpdotenv 版本支持 PHP 8
- [#921c757](https://github.com/hunzhiwange/framework/commit/921c757) refactor(kernel): 优化文档 command
- [#3d4d775](https://github.com/hunzhiwange/framework/commit/3d4d775),[#401436d](https://github.com/hunzhiwange/framework/commit/401436d) refactor: 优化数据库查询缓存

## 测试和文档

- [#802b9e6](https://github.com/hunzhiwange/framework/commit/802b9e6) docs(router): 更新路由文档 

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