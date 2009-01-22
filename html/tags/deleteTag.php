<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET tag_id
 */
verifyUser(array('Administrator','Clerk'));

$tag = new Tag($_GET['tag_id']);
$tag->delete();
Header('Location: '.BASE_URL.'/tags');
