<?php
/**
 * @copyright 2014-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
use Application\Models\Person;

$person = [];

$fields = [
    'id',
    'firstname',
    'lastname',
	'about',
	'username',
	'gender',
	'race'
];
foreach ($fields as $key) {
	$get = 'get'.ucfirst($key);
	$value = $this->person->$get();

	if ($value) { $person[$key] = $value; }
}

echo json_encode($person);
