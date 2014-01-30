<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
echo "<committee>";
include APPLICATION_HOME.'/blocks/xml/committees/info.inc';
include APPLICATION_HOME.'/blocks/xml/committees/currentMembers.inc';
echo "</committee>";
