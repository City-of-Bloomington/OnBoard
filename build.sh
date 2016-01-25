#!/bin/bash
APPNAME=OnBoard
DIR=`pwd`
BUILD=$DIR/build

if [ ! -d $BUILD ]
	then mkdir $BUILD
fi

# Compile the Lanague files
cd $DIR/language
./build_lang.sh
cd $DIR

# Compile the SASS
cd $DIR/public/css/local
./build_css.sh
cd $DIR

# The PHP code does not need to actually build anything.
# Just copy all the files into the build
rsync -rlv --exclude-from=$DIR/buildignore --delete $DIR/ $BUILD/$APPNAME
cd $BUILD
tar czvf $APPNAME.tar.gz $APPNAME
