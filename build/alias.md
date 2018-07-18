# QueryPHP Framework Alias

Modify this file ../.git/config add alias and remote,then using git stpull and stpush to manager your subtree repository.

```
git stpull Auth
git stpush Auth

git testspull
git testspush
```

The code below need to be add.

```
[core]
    repositoryformatversion = 0
    filemode = true
    bare = false
    logallrefupdates = true
    ignorecase = true
    precomposeunicode = true
[remote "origin"]
    url = git@github.com:hunzhiwange/framework.git
    fetch = +refs/heads/*:refs/remotes/origin/*
[branch "master"]
    remote = origin
    merge = refs/heads/master
[remote "origin"]
    url = git@gitee.com:dyhb/framework.git
    fetch = +refs/heads/*:refs/remotes/origin/*

[remote "tests"]
    url = git@github.com:queryyetsimple/tests.git
    fetch = +refs/heads/*:refs/remotes/tests/*

[remote "Auth"]
    url = git@github.com:queryyetsimple/auth.git
    fetch = +refs/heads/*:refs/remotes/Auth/*

[remote "Cache"]
    url = git@github.com:queryyetsimple/cache.git
    fetch = +refs/heads/*:refs/remotes/Cache/*

[remote "Client"]
    url = git@github.com:queryyetsimple/client.git
    fetch = +refs/heads/*:refs/remotes/Client/*

[remote "Collection"]
    url = git@github.com:queryyetsimple/collection.git
    fetch = +refs/heads/*:refs/remotes/Collection/*

[remote "Console"]
    url = git@github.com:queryyetsimple/console.git
    fetch = +refs/heads/*:refs/remotes/Console/*

[remote "Cookie"]
    url = git@github.com:queryyetsimple/cookie.git
    fetch = +refs/heads/*:refs/remotes/Cookie/*

[remote "Database"]
    url = git@github.com:queryyetsimple/database.git
    fetch = +refs/heads/*:refs/remotes/Database/*

[remote "Di"]
    url = git@github.com:queryyetsimple/di.git
    fetch = +refs/heads/*:refs/remotes/Di/*

[remote "Encryption"]
    url = git@github.com:queryyetsimple/encryption.git
    fetch = +refs/heads/*:refs/remotes/Encryption/*

[remote "Event"]
    url = git@github.com:queryyetsimple/event.git
    fetch = +refs/heads/*:refs/remotes/Event/*

[remote "Filesystem"]
    url = git@github.com:queryyetsimple/filesystem.git
    fetch = +refs/heads/*:refs/remotes/Filesystem/*

[remote "Flow"]
    url = git@github.com:queryyetsimple/flow.git
    fetch = +refs/heads/*:refs/remotes/Flow/*

[remote "Http"]
    url = git@github.com:queryyetsimple/http.git
    fetch = +refs/heads/*:refs/remotes/Http/*

[remote "I18n"]
    url = git@github.com:queryyetsimple/i18n.git
    fetch = +refs/heads/*:refs/remotes/I18n/*

[remote "Kernel"]
    url = git@github.com:queryyetsimple/kernel.git
    fetch = +refs/heads/*:refs/remotes/Kernel/*

[remote "Log"]
    url = git@github.com:queryyetsimple/log.git
    fetch = +refs/heads/*:refs/remotes/Log/*

[remote "Mail"]
    url = git@github.com:queryyetsimple/mail.git
    fetch = +refs/heads/*:refs/remotes/Mail/*

[remote "Manager"]
    url = git@github.com:queryyetsimple/manager.git
    fetch = +refs/heads/*:refs/remotes/Manager/*

[remote "Mvc"]
    url = git@github.com:queryyetsimple/mvc.git
    fetch = +refs/heads/*:refs/remotes/Mvc/*

[remote "Option"]
    url = git@github.com:queryyetsimple/option.git
    fetch = +refs/heads/*:refs/remotes/Option/*

[remote "Page"]
    url = git@github.com:queryyetsimple/page.git
    fetch = +refs/heads/*:refs/remotes/Page/*

[remote "Pipeline"]
    url = git@github.com:queryyetsimple/pipeline.git
    fetch = +refs/heads/*:refs/remotes/Pipeline/*

[remote "Protocol"]
    url = git@github.com:queryyetsimple/protocol.git
    fetch = +refs/heads/*:refs/remotes/Protocol/*

[remote "Queue"]
    url = git@github.com:queryyetsimple/queue.git
    fetch = +refs/heads/*:refs/remotes/Queue/*

[remote "Router"]
    url = git@github.com:queryyetsimple/router.git
    fetch = +refs/heads/*:refs/remotes/Router/*

[remote "Seccode"]
    url = git@github.com:queryyetsimple/seccode.git
    fetch = +refs/heads/*:refs/remotes/Seccode/*

[remote "Session"]
    url = git@github.com:queryyetsimple/session.git
    fetch = +refs/heads/*:refs/remotes/Session/*

[remote "Stack"]
    url = git@github.com:queryyetsimple/stack.git
    fetch = +refs/heads/*:refs/remotes/Stack/*

[remote "Support"]
    url = git@github.com:queryyetsimple/support.git
    fetch = +refs/heads/*:refs/remotes/Support/*

[remote "Task"]
    url = git@github.com:queryyetsimple/task.git
    fetch = +refs/heads/*:refs/remotes/Task/*

[remote "Throttler"]
    url = git@github.com:queryyetsimple/throttler.git
    fetch = +refs/heads/*:refs/remotes/Throttler/*

[remote "Tree"]
    url = git@github.com:queryyetsimple/tree.git
    fetch = +refs/heads/*:refs/remotes/Tree/*

[remote "Validate"]
    url = git@github.com:queryyetsimple/validate.git
    fetch = +refs/heads/*:refs/remotes/Validate/*

[remote "View"]
    url = git@github.com:queryyetsimple/view.git
    fetch = +refs/heads/*:refs/remotes/View/*


[alias]      
    stpull = !git subtree pull --prefix=src/Queryyetsimple/$1 $1 master \
        && :

    stpush = !git subtree split --rejoin --prefix=src/Queryyetsimple/$1 master \
        && git subtree push --prefix=src/Queryyetsimple/$1 $1 master \
        && :

    testspull = !git subtree pull --prefix=tests tests master \
        && :

    testspush = !git subtree split --rejoin --prefix=tests master \
        && git subtree push --prefix=tests tests master \
        && :
```

## QueryPHP Git


```
[core]
    repositoryformatversion = 0
    filemode = true
    bare = false
    logallrefupdates = true
    ignorecase = true
    precomposeunicode = true
[remote "origin"]
    url = git@github.com:hunzhiwange/framework.git
    fetch = +refs/heads/*:refs/remotes/origin/*
[branch "master"]
    remote = origin
    merge = refs/heads/master
[remote "origin"]
    url = git@gitee.com:dyhb/framework.git
    fetch = +refs/heads/*:refs/remotes/origin/*
```

