#!/usr/bin/env bash

set -e

if (( "$#" != 1 ))
then
    echo "Tag has to be provided"

    exit 1
fi

CURRENT_BRANCH="master"

for REMOTE in auth cache config console database debug di encryption event filesystem http i18n kernel log page router server session validate view
do
    echo ""
    echo ""
    echo "Release $REMOTE";

    TMP_DIR="./leevel-release-split"
    REMOTE_URL="git@github.com:leevels/$REMOTE.git"

    rm -rf $TMP_DIR;
    mkdir $TMP_DIR;

    (
        cd $TMP_DIR;

        git clone $REMOTE_URL .
        git checkout "$CURRENT_BRANCH";

        git tag $1
        git push origin $1

        cd -

        rm -rf $TMP_DIR;
    )
done
