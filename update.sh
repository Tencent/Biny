#!/bin/bash
if [ -z "$1" ]; then
    source ./biny.version
    cmd=`curl 'https://billge1205.github.io/biny/biny.version'`
    if [ -z "$cmd" ]; then echo "cannot connect to github"; exit 1; fi
    new=`echo "$cmd" | sed '/^version=/!d;s/.*=//'`
    if [ $new == $version ]
    then
        echo "nothing to do, version is up to date."; exit 1;
    else
        wget https://codeload.github.com/billge1205/biny/zip/master
        unzip -o master -d ./update
        php ./update/biny-master/update/update.php
        rm -f master master.*
        rm -rf ./update/*
    fi
else
    rm -rf ./update/*
    unzip -o "$1" -d ./update
    if [ -f  "./update/biny-master/update/update.php" ]; then
        php ./update/biny-master/update/update.php
        rm -rf ./update/*
    else
        echo "zip is not correct";
        rm -rf ./update/*;
        exit 1;
    fi
fi
