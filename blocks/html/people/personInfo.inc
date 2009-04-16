<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$username = $this->person->getUsername();
if (!$username && userHasRole('Administrator')) {
	$url = BASE_URL.'/users/addUser.php?person_id='.$this->person->getId();
	$username = "
	<button type=\"button\" onclick=\"document.location.href='$url';\">
		Create Account
	</button>";
}

$name = View::escape($this->person->getFullname());
echo "<h1>$name</h1>";
