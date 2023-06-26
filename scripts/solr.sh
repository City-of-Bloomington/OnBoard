#!/bin/bash
# @copyright 2023 City of Bloomington, Indiana
# @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
APPLICATION_NAME="onboard"
APPLICATION_HOME="/srv/sites/${APPLICATION_NAME}"
SITE_HOME="/srv/data/${APPLICATION_HOME}"

cd $APPLICATION_HOME/scripts/solr
SITE_HOME=$SITE_HOME php indexNewFiles.php
