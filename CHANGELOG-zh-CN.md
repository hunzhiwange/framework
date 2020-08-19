# v1.0.1 - TBD

## 修复 

- [#fadd998](https://github.com/hunzhiwange/framework/commit/fadd99826f2ae917df0534be22eabd17e59dae05) WebSocket 释放根协程数据.

## 优化

- [#210a15f](https://github.com/hunzhiwange/framework/commit/210a15f710318d40dc115350afbb116bf7418b77) 优化代码风格.
- [#20b54bc](https://github.com/hunzhiwange/framework/commit/20b54bc1856bb8c835271f65fd57f42d87c7e789) 优化方法文案.
- [#1cfb217](https://github.com/hunzhiwange/framework/commit/1cfb217e8d4b454dff9ff2b2aa256276f1687132) 优化 ide helper.

## 新增

- [#6c56b83](https://github.com/hunzhiwange/framework/commit/6c56b837e5083a64ca3ee8e20af574af253aa6a8) 新增 \Leevel\Protocol\Coroutine::removeContext().

## 变更

- [#6c56b83](https://github.com/hunzhiwange/framework/commit/6c56b837e5083a64ca3ee8e20af574af253aa6a8) IOC 容器可以指定 swoole 协程  id 操作数据

## 删除

- [#6ec26fa](https://github.com/hunzhiwange/framework/commit/6ec26fa92ffc8594623e3fb4da934082b0927a33) 删除 i18n 组件中的方法 __().
- [#0fdade6](https://github.com/hunzhiwange/framework/commit/0fdade66c9ad0a59293d987514916a0c1f66835c) 删除配置命令 make:idehelper --proxy 配置参数 

## 测试和文档

- [#10cc02e](https://github.com/hunzhiwange/framework/commit/10cc02e3d4823e95535b02da7a51b3ab88a2edfa) 为 \Leevel\Protocol\Coroutine::removeContext() 编写了测试用例.
- [#2c5349a](https://github.com/hunzhiwange/framework/commit/347aad7e9a71cf5294e5bd63060419e573971472) 新增测试用例 Tests\Log\Console\ClearTest.
- [#a24a05b](https://github.com/hunzhiwange/framework/commit/ad74c497b9ae9cbc41b3517fdfceabfc61e0d866) 新增测试用例 \Tests\Database\Console\EntityTest.
