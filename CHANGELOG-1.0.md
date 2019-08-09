# [](https://github.com/hunzhiwange/framework/compare/v1.0.0-beta.4...v) (2019-08-09)


### Bug Fixes

* **tests:**  fix the tests of Router ([b1ffc32](https://github.com/hunzhiwange/framework/commit/b1ffc32))
* fix tests for unique rule of validator ([0ca1838](https://github.com/hunzhiwange/framework/commit/0ca1838))
* fix tests of Console BaseMake ([fe32d51](https://github.com/hunzhiwange/framework/commit/fe32d51))
* getPcid of swoole runtime can be false and auto add context for class ([d1888dd](https://github.com/hunzhiwange/framework/commit/d1888dd))
* try to fix `go(): Using Xdebug in coroutines is extremely dangerous, please notice that it may lead to coredump!` ([cf92ae3](https://github.com/hunzhiwange/framework/commit/cf92ae3))



# [1.0.0-beta.4](https://github.com/hunzhiwange/framework/compare/v1.0.0-beta.3...v1.0.0-beta.4) (2019-07-28)


### Bug Fixes

* add cors for swoole ([fe43aa3](https://github.com/hunzhiwange/framework/commit/fe43aa3))
* coroutine first for container ([cfe19ad](https://github.com/hunzhiwange/framework/commit/cfe19ad))
* create new redis connect every time for redisPool ([7c8758b](https://github.com/hunzhiwange/framework/commit/7c8758b))
* fix api ([ba20b11](https://github.com/hunzhiwange/framework/commit/ba20b11))
* fix hotoverload process for swoole ([08b4db3](https://github.com/hunzhiwange/framework/commit/08b4db3))
* fix task method of task manager ([e0eebc9](https://github.com/hunzhiwange/framework/commit/e0eebc9))
* fix the base pool ([39d5f1c](https://github.com/hunzhiwange/framework/commit/39d5f1c))
* fix the debugbar path ([7b44557](https://github.com/hunzhiwange/framework/commit/7b44557))
* package  `egulias/email-validator` to ^2.1.10 to  fix ` Trying to access array offset on value of type null` ([7f76869](https://github.com/hunzhiwange/framework/commit/7f76869))
* rename `mix` to  `min` ([aac5c95](https://github.com/hunzhiwange/framework/commit/aac5c95))
* set coroutine to the container ([cda8803](https://github.com/hunzhiwange/framework/commit/cda8803))


### Features

* add `onlyNew` for manager connect ([26da3c4](https://github.com/hunzhiwange/framework/commit/26da3c4))
* add `RunCommand` to run a command in controller ([b3f32d6](https://github.com/hunzhiwange/framework/commit/b3f32d6))
* add mysql pool to app() ([ff0b1b6](https://github.com/hunzhiwange/framework/commit/ff0b1b6))
* add pool for swoole ([fdd1335](https://github.com/hunzhiwange/framework/commit/fdd1335))
* add redisPool for cache component ([0ef9fa1](https://github.com/hunzhiwange/framework/commit/0ef9fa1))
* add task for swoole server ([b058ea1](https://github.com/hunzhiwange/framework/commit/b058ea1))
* add task manager ([cf9613b](https://github.com/hunzhiwange/framework/commit/cf9613b))
* add timer ([3af710d](https://github.com/hunzhiwange/framework/commit/3af710d))
* base mysql pool done,but not work for transaction ([349bb53](https://github.com/hunzhiwange/framework/commit/349bb53))
* mysql pool base done ([565e1a4](https://github.com/hunzhiwange/framework/commit/565e1a4))



# [1.0.0-beta.3](https://github.com/hunzhiwange/framework/compare/v1.0.0-beta.2...v1.0.0-beta.3) (2019-06-23)


### Bug Fixes

* add IUnitOfWork to provider ([4dfea34](https://github.com/hunzhiwange/framework/commit/4dfea34))
* fix database privider ([7406c73](https://github.com/hunzhiwange/framework/commit/7406c73))
* fix tests ([e309883](https://github.com/hunzhiwange/framework/commit/e309883))
* fix tests for session ([f8098dd](https://github.com/hunzhiwange/framework/commit/f8098dd))
* rename `nulls` to `test` ([36282f7](https://github.com/hunzhiwange/framework/commit/36282f7))
* **database tests:** make the tests more reliable ([401a276](https://github.com/hunzhiwange/framework/commit/401a276))
* **entity:** fix ?? for entity ([496fd6a](https://github.com/hunzhiwange/framework/commit/496fd6a))
* **flash:** fix flash helper ([3b7d813](https://github.com/hunzhiwange/framework/commit/3b7d813))
* **fn:** fix the comment ([a8b6521](https://github.com/hunzhiwange/framework/commit/a8b6521))
* **fn doc:** `leevel/support` but not `leeevel/support` ([6dfd872](https://github.com/hunzhiwange/framework/commit/6dfd872))
* **hl:** fix hl ([6f297d6](https://github.com/hunzhiwange/framework/commit/6f297d6))
* **mail:** fix comment ([9f5e977](https://github.com/hunzhiwange/framework/commit/9f5e977))
* **readme:** fix readme ([5e4b04a](https://github.com/hunzhiwange/framework/commit/5e4b04a))
* **response proxy:** fix the comment ([5357efb](https://github.com/hunzhiwange/framework/commit/5357efb))
* **test:** fix helper tests ([7257207](https://github.com/hunzhiwange/framework/commit/7257207))
* **test:** fix test ([aeb4276](https://github.com/hunzhiwange/framework/commit/aeb4276))
* **test:** Method xx may not return value of type NULL ([7e75f47](https://github.com/hunzhiwange/framework/commit/7e75f47))
* **test:** Method xx may not return value of type NULL ([6b19fab](https://github.com/hunzhiwange/framework/commit/6b19fab))
* **test:** try to fix `Function Redis::delete() is deprecated` ([2691a4b](https://github.com/hunzhiwange/framework/commit/2691a4b))
* **tests:** fix all tests for ci ([98b08b2](https://github.com/hunzhiwange/framework/commit/98b08b2))
* **tests:** fix database tests ([148d43b](https://github.com/hunzhiwange/framework/commit/148d43b))
* **tests:** fix tests of database ([f210530](https://github.com/hunzhiwange/framework/commit/f210530))
* **tests:** fix tests of database ([93a1cf3](https://github.com/hunzhiwange/framework/commit/93a1cf3))
* **tests:** fix tests of database ([96a1c4d](https://github.com/hunzhiwange/framework/commit/96a1c4d))
* **tests:** PDOException: SQLSTATE[HY000]: General error: 1364 Field ([b8e5b4c](https://github.com/hunzhiwange/framework/commit/b8e5b4c))
* **tests:** PDOException: SQLSTATE[HY000]: General error: 1364 Field 'content' doesn't have a default value ([66cef8d](https://github.com/hunzhiwange/framework/commit/66cef8d))
* **tests:** PDOException: SQLSTATE[HY000]: General error: 1364 Field 'user_id' doesn't have a default value ([a5cd2c8](https://github.com/hunzhiwange/framework/commit/a5cd2c8))
* **tests:** PDOException: SQLSTATE[HY000]: General error: 1364 Field 'user_id' doesn't have a default value ([d97a89e](https://github.com/hunzhiwange/framework/commit/d97a89e))
* **tests:** SQLSTATE[HY000]: General error: 1364 Field 'user_id' doesn't have a default value ([3ce69ca](https://github.com/hunzhiwange/framework/commit/3ce69ca))
* **tests:** try fix ([aaa0891](https://github.com/hunzhiwange/framework/commit/aaa0891))
* **tests:** try fix ([4bcce26](https://github.com/hunzhiwange/framework/commit/4bcce26))
* **tests:** try fix ([b855cb2](https://github.com/hunzhiwange/framework/commit/b855cb2))
* **tests:** try fix ([15a935e](https://github.com/hunzhiwange/framework/commit/15a935e))
* **view tests:** fix tests for view ([ef6f649](https://github.com/hunzhiwange/framework/commit/ef6f649))
* fix instruction of common thrift ([bdbaf34](https://github.com/hunzhiwange/framework/commit/bdbaf34))


### Features

* **doc:** add git for doc make ([1f525cf](https://github.com/hunzhiwange/framework/commit/1f525cf))



# [1.0.0-beta.2](https://github.com/hunzhiwange/framework/compare/v1.0.0-beta.1...v1.0.0-beta.2) (2019-05-20)


### Bug Fixes

* **app:** app:make to container:make ([4a48651](https://github.com/hunzhiwange/framework/commit/4a48651))
* **app:** use Container replace App ([e33e5c8](https://github.com/hunzhiwange/framework/commit/e33e5c8))
* **cache:** fix the comment ([389adb1](https://github.com/hunzhiwange/framework/commit/389adb1))
* **cache proxy:** fix the namespace and rename CacheLoad to Load for short ([21f25b4](https://github.com/hunzhiwange/framework/commit/21f25b4))
* **composer:** add `leevel/di` to composer ([ceda06d](https://github.com/hunzhiwange/framework/commit/ceda06d))
* **composer:** fix the composer ([53c6571](https://github.com/hunzhiwange/framework/commit/53c6571))
* **console:** fix error of console ([f7e3a46](https://github.com/hunzhiwange/framework/commit/f7e3a46))
* **container:** fix clear and remove ([cfb38fc](https://github.com/hunzhiwange/framework/commit/cfb38fc))
* **database:** remove UnitOfWork from singleton ([5dc5a78](https://github.com/hunzhiwange/framework/commit/5dc5a78))
* **database:** where and having add a default null func ([e50a986](https://github.com/hunzhiwange/framework/commit/e50a986))
* **database console:** fix seedcreate and seedrun for case problem ([b5f3648](https://github.com/hunzhiwange/framework/commit/b5f3648))
* **database console:** fix seedcreate and seedrun for case problem ([7f20ac5](https://github.com/hunzhiwange/framework/commit/7f20ac5))
* **datas:** fix tests error ([03d1aef](https://github.com/hunzhiwange/framework/commit/03d1aef))
* **datas:** rename datas to data ([5e9870b](https://github.com/hunzhiwange/framework/commit/5e9870b))
* **facade:** App to Container ([ec12a2b](https://github.com/hunzhiwange/framework/commit/ec12a2b))
* **fn:** fix fn ([eaf6649](https://github.com/hunzhiwange/framework/commit/eaf6649))
* **hash test:** fix tests for php7.4 ([31fbc9c](https://github.com/hunzhiwange/framework/commit/31fbc9c))
* **mail:** fix mail proxy ([ba4e90e](https://github.com/hunzhiwange/framework/commit/ba4e90e))
* **no-dev:** fix errors with --no-dev ([cfdec38](https://github.com/hunzhiwange/framework/commit/cfdec38))
* **phpstan ...:** fix ... ([7c8cd8b](https://github.com/hunzhiwange/framework/commit/7c8cd8b))
* **phpstan ...:** fix for ... ([6d3c978](https://github.com/hunzhiwange/framework/commit/6d3c978))
* **phpstan auth:** fix auth with phpstan 0 ([5490398](https://github.com/hunzhiwange/framework/commit/5490398))
* **phpstan cache:** fix cache with phpstan 0 ([18e2e88](https://github.com/hunzhiwange/framework/commit/18e2e88))
* **phpstan di:** fix di with phpstan 0 ([1ddc55b](https://github.com/hunzhiwange/framework/commit/1ddc55b))
* **phpstan filesystem:** fix filesystem with phpstan 0 ([e74071e](https://github.com/hunzhiwange/framework/commit/e74071e))
* **phpstan http:** fix http with phpstan 0 ([b9b88e7](https://github.com/hunzhiwange/framework/commit/b9b88e7))
* **phpstan kernel:** fix kernel with phpstan 0 ([412aec4](https://github.com/hunzhiwange/framework/commit/412aec4))
* **phpstan router:** fix router with phpstan 0 ([a3a750b](https://github.com/hunzhiwange/framework/commit/a3a750b))
* **phpstan session:** fix session with phpstan 0 ([edf1458](https://github.com/hunzhiwange/framework/commit/edf1458))
* **phpstan support:** fix support with phpstan 0 ([3095c2b](https://github.com/hunzhiwange/framework/commit/3095c2b))
* **phpstan throttler:** fix throttler with phpstan 0 ([741c424](https://github.com/hunzhiwange/framework/commit/741c424))
* **router:** fix the comment of IResponseFactory ([0123193](https://github.com/hunzhiwange/framework/commit/0123193))
* **str:** for fn change ([798cc08](https://github.com/hunzhiwange/framework/commit/798cc08))
* **switch:** remove one of return or break in switch ([3a79976](https://github.com/hunzhiwange/framework/commit/3a79976))
* **test:** fix redis tests ([6c2e482](https://github.com/hunzhiwange/framework/commit/6c2e482))
* **test fn:** fix fn for php7.4 ([6c140a4](https://github.com/hunzhiwange/framework/commit/6c140a4))
* add public method to IValidator ([1e89ea5](https://github.com/hunzhiwange/framework/commit/1e89ea5))
* option of console can return bool and do on Leevel\Console\Autoload ([f3af165](https://github.com/hunzhiwange/framework/commit/f3af165))
* setFoo to setterFoo and some othor changes of database ([549687f](https://github.com/hunzhiwange/framework/commit/549687f))


### Features

* add base assert ([116dacb](https://github.com/hunzhiwange/framework/commit/116dacb))
* add before and after for UnitOfWork ([6df0cbd](https://github.com/hunzhiwange/framework/commit/6df0cbd))
* add dump and dd ([2b618df](https://github.com/hunzhiwange/framework/commit/2b618df))
* refactor fso helpers ([687605a](https://github.com/hunzhiwange/framework/commit/687605a))



# [1.0.0-beta.1](https://github.com/hunzhiwange/framework/compare/v1.0.0-alpha.3...v1.0.0-beta.1) (2019-04-14)


### Bug Fixes

* \Leevel method support Str::unCamelize ([6a84023](https://github.com/hunzhiwange/framework/commit/6a84023))
* add default trace for debug ([9856e9c](https://github.com/hunzhiwange/framework/commit/9856e9c))
* fix all tests about facade ([f1eed1e](https://github.com/hunzhiwange/framework/commit/f1eed1e))
* fix all tests for __() ([dc83021](https://github.com/hunzhiwange/framework/commit/dc83021))
* fix all tests of FunctionTest ([bb2b6a5](https://github.com/hunzhiwange/framework/commit/bb2b6a5))
* fix comment for tests/Database/Ddd/Create/CreateTest.php ([e1aaec4](https://github.com/hunzhiwange/framework/commit/e1aaec4))
* fix error for Arr::only ([81936f6](https://github.com/hunzhiwange/framework/commit/81936f6))
* fix fn ([3533cdb](https://github.com/hunzhiwange/framework/commit/3533cdb))
* fix Leevel\Support\Fn ([79d0d4c](https://github.com/hunzhiwange/framework/commit/79d0d4c))
* fix markdown type of doc tool ([fa87955](https://github.com/hunzhiwange/framework/commit/fa87955))
* fix some bug of Request ([f9d647a](https://github.com/hunzhiwange/framework/commit/f9d647a))
* fix template ([9410716](https://github.com/hunzhiwange/framework/commit/9410716))
* fix tests of facade ([093b3b0](https://github.com/hunzhiwange/framework/commit/093b3b0))
* fix tests of FunctionTest ([94642c3](https://github.com/hunzhiwange/framework/commit/94642c3))
* fix the error of doc ([5195023](https://github.com/hunzhiwange/framework/commit/5195023))
* fix the ignore of Leevel2Psr2 ([2555ce7](https://github.com/hunzhiwange/framework/commit/2555ce7))
* fix the include path of database ([46c9c3a](https://github.com/hunzhiwange/framework/commit/46c9c3a))
* fix the namespace of Router\ViewTest ([48e859a](https://github.com/hunzhiwange/framework/commit/48e859a))
* fix the path ([94a931d](https://github.com/hunzhiwange/framework/commit/94a931d))
* fix the test for container change ([aed98d7](https://github.com/hunzhiwange/framework/commit/aed98d7))
* fix the tests of Console\Cache ([0f31440](https://github.com/hunzhiwange/framework/commit/0f31440))
* fix the un_camelize include path ([4d1089a](https://github.com/hunzhiwange/framework/commit/4d1089a))
* fix whereRaw sql parse ([0c34431](https://github.com/hunzhiwange/framework/commit/0c34431))
* http content can be string,null and resource ([988fd49](https://github.com/hunzhiwange/framework/commit/988fd49))
* Leevel\Router\Cookie to Leevel\Http\Cookie ([a5ecc8d](https://github.com/hunzhiwange/framework/commit/a5ecc8d))
* rename to right ([7715cfc](https://github.com/hunzhiwange/framework/commit/7715cfc))
* set zendframework/zend-diactoros version to ^2.1.1 ([c937f83](https://github.com/hunzhiwange/framework/commit/c937f83))
* temp of rename ([c3af227](https://github.com/hunzhiwange/framework/commit/c3af227))
* update doctrine/annotations to ^1.6.1 ([dbf7671](https://github.com/hunzhiwange/framework/commit/dbf7671))


### Features

*  rename helper func ([37e3880](https://github.com/hunzhiwange/framework/commit/37e3880))
* \Leevel::path added and more of Leevel\Leevel\App ([d0de528](https://github.com/hunzhiwange/framework/commit/d0de528))
* add a getMethodBody for Leevel\Leevel\Utils\Doc ([d4a536e](https://github.com/hunzhiwange/framework/commit/d4a536e))
* add app() ([071fbfa](https://github.com/hunzhiwange/framework/commit/071fbfa))
* add clear for Container ([45f8f16](https://github.com/hunzhiwange/framework/commit/45f8f16))
* add Fn for function autoload ([6fd903a](https://github.com/hunzhiwange/framework/commit/6fd903a))
* add fn() for queryphp ([97d1a32](https://github.com/hunzhiwange/framework/commit/97d1a32))
* add getClassBody for Doc ([938a40a](https://github.com/hunzhiwange/framework/commit/938a40a))
* add helper fn for i18n ([680d008](https://github.com/hunzhiwange/framework/commit/680d008))
* add make:docwithin for framework ([f1187c3](https://github.com/hunzhiwange/framework/commit/f1187c3))
* add setI18nCachedPath,setOptionCachedPath,setRouterCachedPath,setComposer ([1fbd05e](https://github.com/hunzhiwange/framework/commit/1fbd05e))
* add todo for router ([2df0d0e](https://github.com/hunzhiwange/framework/commit/2df0d0e))
* add white of autoload file ([ecb0e2f](https://github.com/hunzhiwange/framework/commit/ecb0e2f))
* arr add only,except and filter method ([4029bfe](https://github.com/hunzhiwange/framework/commit/4029bfe))
* create file support a content ([2088da2](https://github.com/hunzhiwange/framework/commit/2088da2))



# [1.0.0-alpha.3](https://github.com/hunzhiwange/framework/compare/v1.0.0-alpha.2...v1.0.0-alpha.3) (2019-03-12)


### Bug Fixes

* clear the \Leevel\Router\IView ([530596d](https://github.com/hunzhiwange/framework/commit/530596d))
* fix trace for debug ([0008354](https://github.com/hunzhiwange/framework/commit/0008354))
* mvc compontent has moved to router ([4f9a297](https://github.com/hunzhiwange/framework/commit/4f9a297))


### Features

* **cookie:** format cookie to string ([8409889](https://github.com/hunzhiwange/framework/commit/8409889))
* **doc:** add new doc tool for this project ([8f977b1](https://github.com/hunzhiwange/framework/commit/8f977b1))
* **roadRunner:** add roadRunner for leevel ([012877f](https://github.com/hunzhiwange/framework/commit/012877f))



# 1.0.0-alpha.1 (2018-11-07)



