#!/bin/bash
# @copyright 2023 City of Bloomington, Indiana
# @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
APPLICATION_HOME="{{ onboard_install_path }}"
SITE_HOME="{{ onboard_site_home }}"

cd $APPLICATION_HOME/scripts/solr
SITE_HOME=$SITE_HOME php indexNewFiles.php
