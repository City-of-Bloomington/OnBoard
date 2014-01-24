#!/bin/bash
# Creates a tarball containing a full snapshot of the data in the site
#
# @copyright Copyright 2011-2013 City of Bloomington, Indiana
# @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
# @author Cliff Ingham <inghamn@bloomington.in.gov>
MYSQLDUMP=/usr/local/mysql/bin/mysqldump
BACKUP_DIR=/var/www/backups/application_name
APPLICATION_HOME=/var/www/sites/application_name

MYSQL_DBNAME=application_name

# How many days worth of tarballs to keep around
num_days_to_keep=5

#----------------------------------------------------------
# No Editing Required below this line
#----------------------------------------------------------
now=`date +%s`
today=`date +%F`

cd $BACKUP_DIR
mkdir $today

# Dump the database
$MYSQLDUMP --defaults-extra-file=$APPLICATION_HOME/scripts/backup.cnf $MYSQL_DBNAME > $today/$MYSQL_DBNAME.sql

# Copy any data directories into this directory, so they're backed up, too.
# For example, if we had a media directory....
#cp -R $APPLICATION_HOME/data/media $today/media

# Tarball the Data
tar czf $today.tar.gz $today
rm -Rf $today

# Purge any backup tarballs that are too old
for file in `ls`
do
	atime=`stat -c %Y $file`
	if [ $(( $now - $atime >= $num_days_to_keep*24*60*60 )) = 1 ]
	then
		rm $file
	fi
done
