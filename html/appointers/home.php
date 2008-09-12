<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');

$appointerList = new AppointerList();
$appointerList->find();

$template = new Template();
$template->blocks[] = new Block('appointers/appointerList.inc',array('appointerList'=>$appointerList));
echo $template->render();