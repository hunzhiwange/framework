#!/usr/bin/env bash

set -e

CURRENT_BRANCH="master"

REMOTE="tests";

echo "Split $REMOTE";

TMP_DIR="./leevel-split-tests"
REMOTE_URL="git@github.com:leevels/$REMOTE.git"

rm -rf $TMP_DIR;
mkdir $TMP_DIR;

(
    SPLIT_NEW_BRANCH="split-tests-tmp-branch"
    REMOTE_DIR=${REMOTE}

    git subtree split -P ${REMOTE_DIR} -b $SPLIT_NEW_BRANCH

    cd $TMP_DIR;

    git init;

    git pull ../ $SPLIT_NEW_BRANCH

    git push -f "git@github.com:leevels/$REMOTE.git" "master:$CURRENT_BRANCH"

    cd -

    git branch -D $SPLIT_NEW_BRANCH

    rm -rf $TMP_DIR;
)
