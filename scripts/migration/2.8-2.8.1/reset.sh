auth=/etc/mysql/debian.cnf

cd /srv/backups/onboard
sudo mysql --defaults-extra-file=$auth -e "drop   database onboard;"
sudo mysql --defaults-extra-file=$auth -e "create database onboard;"
sudo mysql --defaults-extra-file=$auth onboard < onboard.sql

cd /srv/data/onboard
rsync -rlve ssh onboard.bloomington.in.gov:/srv/data/onboard/applicantFiles/ ./applicantFiles/

cd /srv/sites/onboard/scripts/migration/2.8-2.8.1
sudo mysql --defaults-extra-file=$auth onboard < 1_databaseChanges.sql
SITE_HOME=/srv/data/onboard php 2_mergeApplicants.php
SITE_HOME=/srv/data/onboard php 3_applicantsToPeople.php
sudo mysql --defaults-extra-file=$auth onboard < 4_dropApplicants.sql
