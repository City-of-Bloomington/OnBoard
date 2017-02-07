#!/bin/bash
APPNAME=onboard
DIR=`pwd`
BUILD=$DIR/build

echo "Checking dependencies"
declare -a dependencies=(msgfmt node-sass node)
for i in "${dependencies[@]}"; do
    command -v $i > /dev/null 2>&1 || { echo "$i not installed" >&2; exit 1; }
done

if [ ! -d $BUILD ]
	then mkdir $BUILD
fi

echo "Building $APPNAME"
# Call all the build scripts in any subdirectories
for f in $(find $DIR -name build_*.sh -not -path "$DIR/build/*" -not -path "*/vendor/*"); do
    echo $f
    cd `dirname $f`
    ./`basename $f`
done

cd $DIR

echo "Copying files"
# The PHP code does not need to actually build anything.
# Just copy all the files into the build
rsync -rl --exclude-from=$DIR/buildignore --delete $DIR/ $BUILD/$APPNAME
cd $BUILD
tar czf $APPNAME.tar.gz $APPNAME
