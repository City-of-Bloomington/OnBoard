#!/bin/bash
# @copyright 2023-2025 City of Bloomington, Indiana
# @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
appl_name="onboard"
appl_home="/srv/sites/${appl_name}"
site_home="/srv/data/${appl_name}"

today=`date +%F`

if ! [ -e $site_home/debug/$today.log ] ; then
    touch $site_home/debug/$today.log
    chown www-data:staff $site_home/debug/$today.log
fi

cd $appl_home/scripts/solr
SITE_HOME=$site_home php indexNewFiles.php

cd $appl_home/scripts/meetings

php sync_event_info.php $site_home
php watch_calendars.php $site_home 60
