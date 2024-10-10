#!/bin/bash
# @copyright 2023-2024 City of Bloomington, Indiana
# @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
appl_name="onboard"
appl_home="/srv/sites/${appl_name}"
site_home="/srv/data/${appl_home}"

cd $appl_home/scripts/solr
SITE_HOME=$site_home php indexNewFiles.php

cd $appl_home/scripts/meetings
SITE_HOME=$site_home php sync_event_info.php
