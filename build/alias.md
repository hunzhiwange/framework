# QueryPHP Framework Alias

modify this file ../.git/config add alias,then using git foobar to commit your subtree repository



git stpull appfe demo/xxx
git stpush appfe demo/xxx

[alias]
   
    stpull = !git subtree pull --prefix=src/Queryyetsimple/$1 $1 $2 \
        && :
    stpush = !git subtree pull --prefix=$1 appfe $2 \
        && git subtree split --rejoin --prefix=$1 $2 \
        && git subtree push --prefix=$1 appfe $2 \
        && :

    aop = subtree push --prefix=src/Queryyetsimple/Aop git@github.com:queryyetsimple/aop.git master

    auth = subtree push --prefix=src/Queryyetsimple/Auth git@github.com:queryyetsimple/auth.git master

    cache = subtree push --prefix=src/Queryyetsimple/Cache git@github.com:queryyetsimple/cache.git master

    collection = subtree push --prefix=src/Queryyetsimple/Collection git@github.com:queryyetsimple/collection.git master

    console = subtree push --prefix=src/Queryyetsimple/Console git@github.com:queryyetsimple/console.git master

    cookie = subtree push --prefix=src/Queryyetsimple/Cookie git@github.com:queryyetsimple/cookie.git master

    database = subtree push --prefix=src/Queryyetsimple/Database git@github.com:queryyetsimple/database.git master

    di = subtree push --prefix=src/Queryyetsimple/Di git@github.com:queryyetsimple/di.git master

    encryption = subtree push --prefix=src/Queryyetsimple/Encryption git@github.com:queryyetsimple/encryption.git master

    event = subtree push --prefix=src/Queryyetsimple/Event git@github.com:queryyetsimple/event.git master

    filesystem = subtree push --prefix=src/Queryyetsimple/Filesystem git@github.com:queryyetsimple/filesystem.git master

    flows = subtree push --prefix=src/Queryyetsimple/Flow git@github.com:queryyetsimple/flow.git master

    http = subtree push --prefix=src/Queryyetsimple/Http git@github.com:queryyetsimple/http.git master

    i18n = subtree push --prefix=src/Queryyetsimple/I18n git@github.com:queryyetsimple/i18n.git master

    logs = subtree push --prefix=src/Queryyetsimple/Log git@github.com:queryyetsimple/log.git master

    mail = subtree push --prefix=src/Queryyetsimple/Mail git@github.com:queryyetsimple/mail.git master

    manager = subtree push --prefix=src/Queryyetsimple/Manager git@github.com:queryyetsimple/manager.git master

    mvc = subtree push --prefix=src/Queryyetsimple/Mvc git@github.com:queryyetsimple/mvc.git master

    option = subtree push --prefix=src/Queryyetsimple/Option git@github.com:queryyetsimple/option.git master

    page = subtree push --prefix=src/Queryyetsimple/Page git@github.com:queryyetsimple/page.git master

    pipeline = subtree push --prefix=src/Queryyetsimple/Pipeline git@github.com:queryyetsimple/pipeline.git master

    psr4 = subtree push --prefix=src/Queryyetsimple/Psr4 git@github.com:queryyetsimple/psr4.git master

    queue = subtree push --prefix=src/Queryyetsimple/Queue git@github.com:queryyetsimple/queue.git master

    router = subtree push --prefix=src/Queryyetsimple/Router git@github.com:queryyetsimple/router.git master

    seccode = subtree push --prefix=src/Queryyetsimple/Seccode git@github.com:queryyetsimple/seccode.git master

    session = subtree push --prefix=src/Queryyetsimple/Session git@github.com:queryyetsimple/session.git master

    stack = subtree push --prefix=src/Queryyetsimple/Stack git@github.com:queryyetsimple/stack.git master

    support = subtree push --prefix=src/Queryyetsimple/Support git@github.com:queryyetsimple/support.git master

    swoole = subtree push --prefix=src/Queryyetsimple/Swoole git@github.com:queryyetsimple/swoole.git master

    throttler = subtree push --prefix=src/Queryyetsimple/Throttler git@github.com:queryyetsimple/throttler.git master

    tree = subtree push --prefix=src/Queryyetsimple/Tree git@github.com:queryyetsimple/tree.git master

    validate = subtree push --prefix=src/Queryyetsimple/Validate git@github.com:queryyetsimple/validate.git master

    view = subtree push --prefix=src/Queryyetsimple/View git@github.com:queryyetsimple/view.git master

    aopp = subtree pull --prefix=src/Queryyetsimple/Aop git@github.com:queryyetsimple/aop.git master

    authp = subtree pull --prefix=src/Queryyetsimple/Auth git@github.com:queryyetsimple/auth.git master

    cachep = subtree pull --prefix=src/Queryyetsimple/Cache git@github.com:queryyetsimple/cache.git master

    collectionp = subtree pull --prefix=src/Queryyetsimple/Collection git@github.com:queryyetsimple/collection.git master

    consolep = subtree pull --prefix=src/Queryyetsimple/Console git@github.com:queryyetsimple/console.git master

    cookiep = subtree pull --prefix=src/Queryyetsimple/Cookie git@github.com:queryyetsimple/cookie.git master

    databasep = subtree pull --prefix=src/Queryyetsimple/Database git@github.com:queryyetsimple/database.git master

    dip = subtree pull --prefix=src/Queryyetsimple/Di git@github.com:queryyetsimple/di.git master

    encryptionp = subtree pull --prefix=src/Queryyetsimple/Encryption git@github.com:queryyetsimple/encryption.git master

    eventp = subtree pull --prefix=src/Queryyetsimple/Event git@github.com:queryyetsimple/event.git master

    filesystemp = subtree pull --prefix=src/Queryyetsimple/Filesystem git@github.com:queryyetsimple/filesystem.git master

    flowsp = subtree pull --prefix=src/Queryyetsimple/Flow git@github.com:queryyetsimple/flow.git master

    httpp = subtree pull --prefix=src/Queryyetsimple/http git@github.com:queryyetsimple/http.git master

    i18np = subtree pull --prefix=src/Queryyetsimple/I18n git@github.com:queryyetsimple/i18n.git master

    logsp = subtree pull --prefix=src/Queryyetsimple/Log git@github.com:queryyetsimple/log.git master

    mailp = subtree pull --prefix=src/Queryyetsimple/Mail git@github.com:queryyetsimple/mail.git master

    managerp = subtree pull --prefix=src/Queryyetsimple/Manager git@github.com:queryyetsimple/manager.git master

    mvcp = subtree pull --prefix=src/Queryyetsimple/Mvc git@github.com:queryyetsimple/mvc.git master

    optionp = subtree pull --prefix=src/Queryyetsimple/Option git@github.com:queryyetsimple/option.git master

    pagep = subtree pull --prefix=src/Queryyetsimple/Page git@github.com:queryyetsimple/page.git master

    pipelinep = subtree pull --prefix=src/Queryyetsimple/Pipeline git@github.com:queryyetsimple/pipeline.git master

    psr4p = subtree pull --prefix=src/Queryyetsimple/Psr4 git@github.com:queryyetsimple/psr4.git master

    queuep = subtree pull --prefix=src/Queryyetsimple/Queue git@github.com:queryyetsimple/queue.git master

    routerp = subtree pull --prefix=src/Queryyetsimple/Router git@github.com:queryyetsimple/router.git master

    seccodep = subtree pull --prefix=src/Queryyetsimple/Seccode git@github.com:queryyetsimple/seccode.git master

    sessionp = subtree pull --prefix=src/Queryyetsimple/Session git@github.com:queryyetsimple/session.git master

    stackp = subtree pull --prefix=src/Queryyetsimple/Stack git@github.com:queryyetsimple/stack.git master

    supportp = subtree pull --prefix=src/Queryyetsimple/Stack git@github.com:queryyetsimple/stack.git master

    swoolep = subtree pull --prefix=src/Queryyetsimple/Swoole git@github.com:queryyetsimple/swoole.git master

    throttlerp = subtree pull --prefix=src/Queryyetsimple/Throttler git@github.com:queryyetsimple/throttler.git master

    treep = subtree pull --prefix=src/Queryyetsimple/Tree git@github.com:queryyetsimple/tree.git master
    
    validatep = subtree pull --prefix=src/Queryyetsimple/Validate git@github.com:queryyetsimple/validate.git master

    viewp = subtree pull --prefix=src/Queryyetsimple/View git@github.com:queryyetsimple/view.git master
