<?php
/**
 * @copyright Copyright (C) 2007-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (isset($_SESSION['errorMessages']))
{
	$errorBlock = new Block('errorMessages.inc',array('errorMessages'=>$_SESSION['errorMessages']));
	echo $errorBlock->render($this->outputFormat);
	unset($_SESSION['errorMessages']);
}
