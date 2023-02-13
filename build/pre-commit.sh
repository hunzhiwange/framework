#!/bin/bash
#
# check PHP code syntax error and standard with phpcs
# https://blog.csdn.net/xsgnzb/article/details/52222366?locationNum=4&fps=1
# https://blog.csdn.net/ljihe/article/details/80826071
# =================== how to use ====================
# cp ./build/pre-commit.sh ./.git/hooks/pre-commit
# chmod 777 ./.git/hooks/pre-commit
# git commit -h
# git commit -n -m 'pass hook' #bypass pre-commit and commit-msg hooks
# ==================== end ==========================
 
PROJECT=$(git rev-parse --show-toplevel) 
cd $PROJECT 
SFILES=$(git diff --cached --name-only --diff-filter=ACMR HEAD | grep \\.php$)
 
# Determine if a file list is passed
if [ "$#" -ne 0 ] 
then 
    exit 0 
fi 
 
echo "Checking PHP Lint..." 
 
for FILE in $SFILES 
do
    php -l -d display_errors=0 $FILE 
    if [ $? != 0 ] 
    then 
        echo "Fix the php error before commit." 
        exit 1 
    fi 
    FILES="$FILES $PROJECT/$FILE" 
done

phpstan_path=$(cd `dirname $0`; pwd)"/../../build/phpstan"

if [ "$FILES" != "" ]
then
    echo "Running Code PHPstan Check..."

    isCheck=""

    for FILE in $SFILES
    do
        result=`php $phpstan_path analyse $FILE`
        if [[ $result == *"[OK] No errors"* ]]
        then
            isCheck=""
        else
            echo $result
            isCheck=$result
        fi
    done

    if [ "$isCheck" != "" ]
    then
        echo "PHPstan error found."
        exit 1
    fi
fi


phpcsfixer_path=$(cd `dirname $0`; pwd)"/../../build/php-cs-fixer"

# format code style
if [ "$FILES" != "" ] 
then 
    echo "Running Code Sniffer..." 
    
    isCheck=""

    for FILE in $SFILES 
    do 
        #result=`~/.composer/vendor/bin/php-cs-fixer fix $FILE`
        #result=`php-cs-fixer fix $FILE`
        result=`php $phpcsfixer_path fix $FILE`

        if [ "$result" != "" ]
        then
            echo $result
            isCheck=$result
            git add $FILE
        fi
    done 
    
    if [ "$isCheck" != "" ] 
    then 
        echo "The file has been automatically formatted."
    fi 
fi 

git update-index -g
 
exit $?