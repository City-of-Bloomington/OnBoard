<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
echo '{"info":';
include APPLICATION_HOME.'/blocks/json/committees/info.inc';
echo ',"seats":';
include APPLICATION_HOME.'/blocks/json/committees/currentMembers.inc';
echo "}";
