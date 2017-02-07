#!/bin/bash
echo "Building language files"
DIR=`pwd`
for LANG in */*
do
    cd $LANG
    msgfmt -cv *.po
    cd $DIR
done
