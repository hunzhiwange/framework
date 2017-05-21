# QueryPHP Framework Alias

modify this file ../.git/config add alias,then using git string to commit your subtree repository

[alias]
    assert = subtree push --prefix=src/queryyetsimple/assert git@github.com:queryyetsimple/assert.git master
    assertp = subtree pull --prefix=src/queryyetsimple/assert git@github.com:queryyetsimple/assert.git master
    auth = subtree push --prefix=src/queryyetsimple/auth git@github.com:queryyetsimple/auth.git master
    authp = subtree pull --prefix=src/queryyetsimple/auth git@github.com:queryyetsimple/auth.git master
    string = subtree push --prefix=src/queryyetsimple/string git@github.com:queryyetsimple/string.git master
    stringp = subtree pull --prefix=src/queryyetsimple/string git@github.com:queryyetsimple/string.git master