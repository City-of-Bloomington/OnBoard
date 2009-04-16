<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

$raceList = new RaceList();
$raceList->find();

$template = new Template();
$template->title = 'Races';
$template->blocks[] = new Block('races/raceList.inc',array('raceList'=>$raceList));
echo $template->render();
