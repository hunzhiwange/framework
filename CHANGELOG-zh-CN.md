# v1.0.2 - TBD

## 新增

- [#dfb82ea](https://github.com/hunzhiwange/framework/commit/ef17c7be35b31e42a117d489d8d4ab3f90d3620f) 功能(cache):  类 Leevel\Cache\Load 添加 clearCacheLoaded 清理已载入的缓存数据.

## 修复

- [#ea43842](https://github.com/hunzhiwange/framework/commit/ea43842dd96054258948e8c623d60279b0430c29) 修复(cache): 类 Leevel\Cache\Load 刷新缓存时 refresh，清理已载入的缓存数据.
- [#cec09bc](https://github.com/hunzhiwange/framework/commit/cec09bc7146c0d48c5c97c61e69e41dee40ac0af) 修复(database): 修复类 Leevel\Database\Ddd\Repository 方法参数类型注释.
- [#a652423](https://github.com/hunzhiwange/framework/commit/a65242334c42641e31d1f58a1e087651741c795a) 修复(database): 修复类 Leevel\Database\Ddd\Select @ method databaseSelect 返回类型错误.

## 优化

- [#d16114f](https://github.com/hunzhiwange/framework/commit/d16114fac898f2d3b4fcc97828a4f23be568aa05) 重构(view): 使用 PHP_EOL 替代换行符 \n
- [#6409e26](https://github.com/hunzhiwange/framework/commit/6409e264bdc280c1c2ae04d2a9ab03f3bfd02f24) 重构: 当我们执行命令 `composer dump-autoload --optimize --no-dev` 时，清理无效的 Phinx 数据库迁命令

## 测试和文档

- [#71a090c](https://github.com/hunzhiwange/framework/commit/71a090ce8504d77445783e562ae8691c32bd7886) 测试(cache): 为 Leevel\Cache\Load::clearCacheLoaded 添加测试用例和文档.
- [#7bf76eb](https://github.com/hunzhiwange/framework/commit/7bf76ebe892be1ea541d6fc6d9dadb2a71fa0508) 测试(session): 为 Leevel\Session\Console\Clear 添加测试用例.

# v1.0.1

## 修复 

- [#fadd998](https://github.com/hunzhiwange/framework/commit/fadd99826f2ae917df0534be22eabd17e59dae05) WebSocket 释放根协程数据.

## 优化

- [#210a15f](https://github.com/hunzhiwange/framework/commit/210a15f710318d40dc115350afbb116bf7418b77) 优化代码风格.
- [#20b54bc](https://github.com/hunzhiwange/framework/commit/20b54bc1856bb8c835271f65fd57f42d87c7e789) 优化方法文案.
- [#1cfb217](https://github.com/hunzhiwange/framework/commit/1cfb217e8d4b454dff9ff2b2aa256276f1687132) 优化 ide helper.
- [#7e65701](https://github.com/hunzhiwange/framework/commit/7e657012736cc95520cf70448882c0ed87635b76) 链式操作支持可选和多值校验和为断言 assert 添加 IDE helper.

## 新增

- [#6c56b83](https://github.com/hunzhiwange/framework/commit/6c56b837e5083a64ca3ee8e20af574af253aa6a8) 新增 \Leevel\Protocol\Coroutine::removeContext().

## 变更

- [#6c56b83](https://github.com/hunzhiwange/framework/commit/6c56b837e5083a64ca3ee8e20af574af253aa6a8) IOC 容器可以指定 swoole 协程  id 操作数据

## 删除

- [#6ec26fa](https://github.com/hunzhiwange/framework/commit/6ec26fa92ffc8594623e3fb4da934082b0927a33) 删除 i18n 组件中的方法 __().
- [#0fdade6](https://github.com/hunzhiwange/framework/commit/0fdade66c9ad0a59293d987514916a0c1f66835c) 删除配置命令 make:idehelper --proxy 配置参数 
- [#6437c03](https://github.com/hunzhiwange/framework/commit/6437c0350efa87c417877974b8eb491ad322b3f6) 删除代理 \Leevel\I18n\Proxy\I18，使用全局函数 __() 作为替代

## 测试和文档

- [#10cc02e](https://github.com/hunzhiwange/framework/commit/10cc02e3d4823e95535b02da7a51b3ab88a2edfa) 为 \Leevel\Protocol\Coroutine::removeContext() 编写了测试用例.
- [#2c5349a](https://github.com/hunzhiwange/framework/commit/347aad7e9a71cf5294e5bd63060419e573971472) 新增测试用例 \Tests\Log\Console\ClearTest.
- [#a24a05b](https://github.com/hunzhiwange/framework/commit/ad74c497b9ae9cbc41b3517fdfceabfc61e0d866) 新增测试用例 \Tests\Database\Console\EntityTest.
- [#83dfa30](https://github.com/hunzhiwange/framework/commit/83dfa300647c7144c22b63f546bc72297500d258) 添加无效值 null 到测试 Tests\Validate\Validator\RequiredTest.
