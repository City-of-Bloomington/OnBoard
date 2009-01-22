#!/bin/bash
PHPCS=/usr/local/PHP_CodeSniffer/scripts/phpcs
STANDARD=COB

for FILE in `ls ../classes/`;
	do $PHPCS --standard=$STANDARD ../classes/$FILE;
done

for FILE in `find ../html/ -name '*.php'`;
	do $PHPCS --standard=$STANDARD $FILE;
done
