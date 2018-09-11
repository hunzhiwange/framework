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
    url = git@github.com:leevels/tests.git
    fetch = +refs/heads/*:refs/remotes/tests/*

[remote "Auth"]
    url = git@github.com:leevels/auth.git
    fetch = +refs/heads/*:refs/remotes/Auth/*

[remote "Cache"]
    url = git@github.com:leevels/cache.git
    fetch = +refs/heads/*:refs/remotes/Cache/*

[remote "Collection"]
    url = git@github.com:leevels/collection.git
    fetch = +refs/heads/*:refs/remotes/Collection/*

[remote "Console"]
    url = git@github.com:leevels/console.git
    fetch = +refs/heads/*:refs/remotes/Console/*

[remote "Cookie"]
    url = git@github.com:leevels/cookie.git
    fetch = +refs/heads/*:refs/remotes/Cookie/*

[remote "Database"]
    url = git@github.com:leevels/database.git
    fetch = +refs/heads/*:refs/remotes/Database/*

[remote "Di"]
    url = git@github.com:leevels/di.git
    fetch = +refs/heads/*:refs/remotes/Di/*

[remote "Encryption"]
    url = git@github.com:leevels/encryption.git
    fetch = +refs/heads/*:refs/remotes/Encryption/*

[remote "Event"]
    url = git@github.com:leevels/event.git
    fetch = +refs/heads/*:refs/remotes/Event/*

[remote "Filesystem"]
    url = git@github.com:leevels/filesystem.git
    fetch = +refs/heads/*:refs/remotes/Filesystem/*

[remote "Flow"]
    url = git@github.com:leevels/flow.git
    fetch = +refs/heads/*:refs/remotes/Flow/*

[remote "Http"]
    url = git@github.com:leevels/http.git
    fetch = +refs/heads/*:refs/remotes/Http/*

[remote "I18n"]
    url = git@github.com:leevels/i18n.git
    fetch = +refs/heads/*:refs/remotes/I18n/*

[remote "Kernel"]
    url = git@github.com:leevels/kernel.git
    fetch = +refs/heads/*:refs/remotes/Kernel/*

[remote "Log"]
    url = git@github.com:leevels/log.git
    fetch = +refs/heads/*:refs/remotes/Log/*

[remote "Mail"]
    url = git@github.com:leevels/mail.git
    fetch = +refs/heads/*:refs/remotes/Mail/*

[remote "Manager"]
    url = git@github.com:leevels/manager.git
    fetch = +refs/heads/*:refs/remotes/Manager/*

[remote "Mvc"]
    url = git@github.com:leevels/mvc.git
    fetch = +refs/heads/*:refs/remotes/Mvc/*

[remote "Option"]
    url = git@github.com:leevels/option.git
    fetch = +refs/heads/*:refs/remotes/Option/*

[remote "Page"]
    url = git@github.com:leevels/page.git
    fetch = +refs/heads/*:refs/remotes/Page/*

[remote "Pipeline"]
    url = git@github.com:leevels/pipeline.git
    fetch = +refs/heads/*:refs/remotes/Pipeline/*

[remote "Router"]
    url = git@github.com:leevels/router.git
    fetch = +refs/heads/*:refs/remotes/Router/*

[remote "Seccode"]
    url = git@github.com:leevels/seccode.git
    fetch = +refs/heads/*:refs/remotes/Seccode/*

[remote "Session"]
    url = git@github.com:leevels/session.git
    fetch = +refs/heads/*:refs/remotes/Session/*

[remote "Stack"]
    url = git@github.com:leevels/stack.git
    fetch = +refs/heads/*:refs/remotes/Stack/*

[remote "Support"]
    url = git@github.com:leevels/support.git
    fetch = +refs/heads/*:refs/remotes/Support/*

[remote "Throttler"]
    url = git@github.com:leevels/throttler.git
    fetch = +refs/heads/*:refs/remotes/Throttler/*

[remote "Tree"]
    url = git@github.com:leevels/tree.git
    fetch = +refs/heads/*:refs/remotes/Tree/*

[remote "Validate"]
    url = git@github.com:leevels/validate.git
    fetch = +refs/heads/*:refs/remotes/Validate/*

[remote "View"]
    url = git@github.com:leevels/view.git
    fetch = +refs/heads/*:refs/remotes/View/*


[alias]      
    stpull = !git subtree pull --prefix=src/Leevel/$1 $1 master \
        && :

    stpush = !git subtree push --prefix=src/Leevel/$1 $1 master \
        && :

    testspull = !git subtree pull --prefix=tests tests master \
        && :

    testspush = !git subtree push --prefix=tests tests master \
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

