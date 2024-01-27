#!/usr/bin/env bash

set -e

CURRENT_BRANCH="master"

for REMOTE in auth cache config console database debug di encryption event filesystem http i18n kernel log page router session support validate view
do
    echo ""
    echo ""
    echo "Split $REMOTE";

    TMP_DIR="./leevel-split"
    REMOTE_URL="git@github.com:leevels/$REMOTE.git"

    rm -rf $TMP_DIR;
    mkdir $TMP_DIR;

    (
        SPLIT_NEW_BRANCH="split-tmp-branch"
        REMOTE_DIR=$(echo ${REMOTE:0:1} | tr 'a-z' 'A-Z');
        TMP_DIR_END=$(echo ${REMOTE:1});
        REMOTE_DIR=${REMOTE_DIR}${TMP_DIR_END}

        git subtree split -P "src/Leevel/$REMOTE_DIR" -b $SPLIT_NEW_BRANCH

        cd $TMP_DIR;

        git init;

        git pull ../ $SPLIT_NEW_BRANCH

        git push -f "git@github.com:leevels/$REMOTE.git" "master:$CURRENT_BRANCH"

        cd -

        git branch -D $SPLIT_NEW_BRANCH

        rm -rf $TMP_DIR;
    )
done
