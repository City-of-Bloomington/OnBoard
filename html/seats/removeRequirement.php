<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET seat_id
 * @param GET requirement_id
 */
verifyUser(array('Administrator','Clerk'));
$seat = new Seat($_GET['seat_id']);
$requirement = new Requirement($_GET['requirement_id']);

$seat->removeRequirement($requirement);

Header('Location: '.$seat->getURL());