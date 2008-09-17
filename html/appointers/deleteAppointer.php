<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET appointer_id
 */
verifyUser(array('Administrator','Clerk'));

$appointer = new Appointer($_GET['appointer_id']);
$appointer->delete();

Header('Location: '.BASE_URL.'/appointers');