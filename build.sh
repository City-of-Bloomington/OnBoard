#!/bin/bash
BUILD=./build
DIST=./dist

if [ ! -d $BUILD ]
	then mkdir $BUILD
	else rm -Rf $BUILD/*
fi

if [ ! -d $DIST ]
	then mkdir $DIST
	else rm -Rf $DIST/*
fi

rsync -rlv --exclude-from=./buildignore --delete ./ ./build/

tar czvf $DIST/civic-legislation.tar.gz --transform=s/build/civic-legislation/ $BUILD
