<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\View;

echo "<url>{$this->committee->getUrl()}</url>";

$fields = [
	'name', 'synopsis', 'statutoryName', 'statuteReference', 'statuteUrl', 'yearFormed',
	'description', 'contactInfo', 'meetingSchedule', 'website'
];
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	echo "<$f>".View::escape($this->committee->$get())."</$f>";
}
