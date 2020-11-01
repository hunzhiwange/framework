# v1.0.2 - TBD

## 新增

- [#dfb82ea](https://github.com/hunzhiwange/framework/commit/ef17c7be35b31e42a117d489d8d4ab3f90d3620f) 功能(cache):  类 Leevel\Cache\Load 添加 clearCacheLoaded 清理已载入的缓存数据.

## 修复

- [#ea43842](https://github.com/hunzhiwange/framework/commit/ea43842dd96054258948e8c623d60279b0430c29) 修复(cache): 类 Leevel\Cache\Load 刷新缓存时 refresh，清理已载入的缓存数据.
- [#cec09bc](https://github.com/hunzhiwange/framework/commit/cec09bc7146c0d48c5c97c61e69e41dee40ac0af) 修复(database): 修复类 Leevel\Database\Ddd\Repository 方法参数类型注释.
- [#a652423](https://github.com/hunzhiwange/framework/commit/a65242334c42641e31d1f58a1e087651741c795a) 修复(database): 修复类 Leevel\Database\Ddd\Select @ method databaseSelect 返回类型错误.
- [#b5529d3](https://github.com/hunzhiwange/framework/commit/b5529d340f176605ab2740d7cb919c9070e99b1b) 修复(console): 修复 Leevel\Console\RunCommand 方法参数类型注释错误
- [#0797959](https://github.com/hunzhiwange/framework/commit/07979595683fbbf7b48f03724f54b49d7da8dc4f) 测试(console): 修复 Tests\Console\BaseCommand:runCommand 参数类型错误
- [#475c7d9](https://github.com/hunzhiwange/framework/commit/475c7d9133d4ba03e3ab4562127949d79f59217d) fix(kernel): 修复 setTestProperty 和 getTestProperty
- [#5cc7175](https://github.com/hunzhiwange/framework/commit/5cc717504f7c399da96ea69989f53f8872a1e007) fix(encryption): 修复加密解密算法不存在的 BUG

## 优化

- [#d16114f](https://github.com/hunzhiwange/framework/commit/d16114fac898f2d3b4fcc97828a4f23be568aa05) 重构(view): 使用 PHP_EOL 替代换行符 \n
- [#6409e26](https://github.com/hunzhiwange/framework/commit/6409e264bdc280c1c2ae04d2a9ab03f3bfd02f24) 重构: 当我们执行命令 `composer dump-autoload --optimize --no-dev` 时，清理无效的 Phinx 数据库迁命令
- [#0becd48](https://github.com/hunzhiwange/framework/commit/0becd48eedef45be917af52f85ea2cdc69ecb084) 重构: 修复数据库迁移文件
- [#074b45f](https://github.com/hunzhiwange/framework/commit/074b45f869c9a060f708dba33f6539aca77ee465) refactor(kernel): 整理代码 Leevel\Kernel\Console\Autoload
- [#538013f](https://github.com/hunzhiwange/framework/commit/538013f21efbe8bd110fdcea55662cbf42bdf2cf) refactor(kernel): 重构清理无效脚本
- [#25ba54d](https://github.com/hunzhiwange/framework/commit/25ba54dce0d93406aa595a1b7f2137cea7048aed) refactor(cache): 删除 Leevel\Cache\Redis\PhpRedis redis 扩展是否安装检测和删除 @ codeCoverageIgnore 标记
- [#5e234cc](https://github.com/hunzhiwange/framework/commit/5e234ccc008b38e549fb32f4a4902887ce7ad5a9) refactor(database): 删除 Leevel\Database\Console\Entity 部分无用代码
- [#1e5ff0b](https://github.com/hunzhiwange/framework/commit/1e5ff0ba0bce91dbee15bda5b8032829b2fa47d3) refactor(di): 重构 newInstanceArgs 和重命名 `$classname` 为 `$className`
- [#3213c39](https://github.com/hunzhiwange/framework/commit/3213c398a360c4d5fa61f1f4ae8e87692331649b) chore: 添加 league/flysystem-sftp 和 league/flysystem-ziparchive 到 require-dev 方便测试

## Changed

- [#d3ddf39](https://github.com/hunzhiwange/framework/commit/d3ddf396845b50f17b77d3b1a416982c80c7d063) refactor(console): 删除一些不常用的方法，大幅度精简
- [#f237e52](https://github.com/hunzhiwange/framework/commit/f237e529f9bde4e13f99e4dfc8545970567444a3) refactor(kernel): 删除 Leevel\Kernel\App 方法 namespacePath 的参数 $throwException

## 测试和文档

- [#71a090c](https://github.com/hunzhiwange/framework/commit/71a090ce8504d77445783e562ae8691c32bd7886) 测试(cache): 为 Leevel\Cache\Load::clearCacheLoaded 添加测试用例和文档.
- [#7bf76eb](https://github.com/hunzhiwange/framework/commit/7bf76ebe892be1ea541d6fc6d9dadb2a71fa0508) 测试(session): 为 Leevel\Session\Console\Clear 添加测试用例.
- [#e0b51c0](https://github.com/hunzhiwange/framework/commit/e0b51c00397057e2d10d0b5ee9df4912ecf1d1a0) 测试(view): 为 Leevel\View\Console\Clear 添加测试用例.
- [#848b46c](https://github.com/hunzhiwange/framework/commit/848b46cf4c367eb52770c4b9625be3ec25d6e11f) 测试: 为组手类添加不存在组手方法的测试用例.
- [#1413568](https://github.com/hunzhiwange/framework/commit/1413568f17f6b5860a510e1d77f8c447463211e8) 测试(datababe): 为数据库迁移脚本添加测试用例。
- [#d447693](https://github.com/hunzhiwange/framework/commit/d447693e05b6708cc93e62fbe0d942cb14728ff1) tests(cache): 添加测试 testWithPassword
- [#258faed](https://github.com/hunzhiwange/framework/commit/258faede3f3a4b3d1c2c924037dc1afab9304dc7) tests(console): 添加参数 $extendCommands 到 trait Tests\Console\BaseCommand 的方法 runCommand 中
- [#21e4ead](https://github.com/hunzhiwange/framework/commit/21e4ead29daacf9dae34155dbe80b9173fd12b95) tests(debug): 添加测试 Tests\Debug\Console\LinkDebugBarTest
- [#e74c68a](https://github.com/hunzhiwange/framework/commit/e74c68a136bbe49a70dc1c1ec10170894986d6cf) tests(kernel): 整理一些测试脚本代码
- [#10edab6](https://github.com/hunzhiwange/framework/commit/10edab653aa0337218deed94af063f1cb98988c1) tests(filesytem): 添加大量测试到 filesystem 
- [#8f2f479](https://github.com/hunzhiwange/framework/commit/8f2f4794451f8e2477b2a0450e90388aaa68fe07) tests(kernel): 添加测试到 link:* command
- [#33e6692](https://github.com/hunzhiwange/framework/commit/33e669216a81029341950fe0f259b507b7f1b854) tests(seccode): 添加测试 testDisplayImage
- [#956fdce](https://github.com/hunzhiwange/framework/commit/956fdcecde66a29c79d88364c18f6a735efb33d8) tests(kernel): 添加测试 Tests\Kernel\Testing\HelperTest
- [#f6f0473](https://github.com/hunzhiwange/framework/commit/f6f047375f55ed39a35820bfc291af1300d5f2c2) tests(kernel): 添加测试 Tests\Kernel\Testing\DatabaseTest
- [#d5eaf4c](https://github.com/hunzhiwange/framework/commit/d5eaf4c2d560bf8729c2dba2f643ce57f34a598b) tests(kernel): 添加测试 Tests\Kernel\Utils\ClassParserTest
- [#cf381af](https://github.com/hunzhiwange/framework/commit/cf381af9ef2e3071e08c838ec2c7cd4386679564) tests(kernel): 添加测试 kernel bootstraplassParserTest
- [#01d3949](https://github.com/hunzhiwange/framework/commit/01d3949d11ecafd73a9b8f265bda818729f3312a) tests(kernel): 添加测试 testRenderForConsole
- [#5daf718](https://github.com/hunzhiwange/framework/commit/5daf7180ed0e031f70879620f6ec75aeef6b0fb4) tests(kernel): 添加测试 testFunctionLang
- [#25cae7e](https://github.com/hunzhiwange/framework/commit/25cae7ecf531e72f79b442b07b4776585e836e77) tests(kernel): 添加测试 Tests\Kernel\InspectorTest
- [#20a9bb8](https://github.com/hunzhiwange/framework/commit/20a9bb89412826c1bb396fe289497489e6d43e3e) tests(mail): 为 mail 添加测试
- [#d96c70d](https://github.com/hunzhiwange/framework/commit/d96c70dc5419e8d78cdca61a807581e3a43e4bac) tests(kernel): 为 Leevel/Kernel/Utils 添加测试
- [#2a495c3](https://github.com/hunzhiwange/framework/commit/2a495c3b32cbab5bd2574292e86562b96a7942e1) tests: 修复 tests/Docs/Preface/PrefaceSummaryDoc.php
- [#06ad7e9](https://github.com/hunzhiwange/framework/commit/06ad7e9a4985469f6a4f8b1d0ad84cb674159d5b) tests(encryption): 添加测试 testEncryptDataFailed
- [#fcf6225](https://github.com/hunzhiwange/framework/commit/fcf6225a6d83229336a95ccd23e6cb1818b1fed9) tests(kernel): 为 Leevel\Kernel\App:namespacePath 添加测试
- [#001d844](https://github.com/hunzhiwange/framework/commit/001d84475780683b6a983871c46eab87c4eb1c1c) tests(kernel): 为 Leevel\Kernel\Bootstrap\LoadOption 添加测试
- [#6bea4e3](https://github.com/hunzhiwange/framework/commit/6bea4e3a17cf1aa5b516a85b90f3a3e9a1d36498) tests(kernel): 为 Leevel\Kernel\ExceptionRuntimeoadOption 添加测试
- [#fc36e0d](https://github.com/hunzhiwange/framework/commit/fc36e0df843ca1bbf99278e2b9c21cb15ab25567) tests(kernel): 为 getConsoleApplication 添加测试
- [#9ebc06f](https://github.com/hunzhiwange/framework/commit/9ebc06ff87dbd659ae0377f90205c10c8c113841) tests(kernel): 为命名行核心添加测试

# v1.0.1

## 修复 

- [#fadd998](https://github.com/hunzhiwange/framework/commit/fadd99826f2ae917df0534be22eabd17e59dae05) WebSocket 释放根协程数据.

## 优化

- [#210a15f](https://github.com/hunzhiwange/framework/commit/210a15f710318d40dc115350afbb116bf7418b77) 优化代码风格.
- [#20b54bc](https://github.com/hunzhiwange/framework/commit/20b54bc1856bb8c835271f65fd57f42d87c7e789) 优化方法文案.
- [#1cfb217](https://github.com/hunzhiwange/framework/commit/1cfb217e8d4b454dff9ff2b2aa256276f1687132) 优化 ide helper.
- [#7e65701](https://github.com/hunzhiwange/framework/commit/7e657012736cc95520cf70448882c0ed87635b76) 链式操作支持可选和多值校验和为断言 assert 添加 IDE helper.
- [#074b45f](https://github.com/hunzhiwange/framework/commit/074b45f869c9a060f708dba33f6539aca77ee465) refactor(kernel): 代码整洁 Leevel\Kernel\Console\Autoload

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
