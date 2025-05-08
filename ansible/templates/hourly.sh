#!/bin/bash
# @copyright 2023-2025 City of Bloomington, Indiana
# @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
appl_home="{{ onboard_install_path }}"
site_home="{{ onboard_site_home    }}"

cd $appl_home/scripts/solr
SITE_HOME=$site_home php indexNewFiles.php

cd $appl_home/scripts/meetings
php sync_event_info.php $site_home
php watch_calendars.php $site_home 60

